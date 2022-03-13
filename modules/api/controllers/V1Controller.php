<?php

/*
 * Developer : Vasim Pathan
 * Date      : 24-6-2021
 *
 */

namespace app\modules\api\controllers;

use app\models\Country;
use app\models\Currencies;
use app\models\Notifications;
use app\models\Users;
use stdClass;
use Yii;
use yii\rest\Controller;
use app\models\Banner;
use app\models\Category;
use app\models\Doctors;
use app\models\Clinics;
use app\models\Pharmacies;
use yii\web\UploadedFile;
use yii\data\Pagination;
use app\helpers\AppHelper;
use app\helpers\AppointmentHelper;
use app\models\DoctorAppointments;
use app\models\LabAppointments;
use app\models\Orders;
use app\models\Settings;

class V1Controller extends Controller
{
    use \app\modules\api\traits\LabAppointmentTrait;
    use \app\modules\api\traits\EcommerceTrait;
    use \app\modules\api\traits\AddressTrait;
    use \app\modules\api\traits\DriverTrait;

    public $data;
    public $message = "";
    public $customKeys = [];
    public $response_code = 200;
    public $noPreviewImg = '';

    public function init()
    {
        $headers = Yii::$app->response->headers;
        $headers->add("Cache-Control", "no-cache, no-store, must-revalidate");
        $headers->add("Pragma", "no-cache");
        $headers->add("Expires", 0);
        //Yii::$app->params['bufferQty'] = AppHelper::getBufferStockQty();
        $this->imgUrl = AppHelper::getUploadUrl();
        $this->noPreviewImg = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => (YII_ENV_PROD) ? ['*'] : ['*'],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => false,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }

    /**
     *
     * @param type $action
     * @return mixed
     */
    public function beforeAction($action)
    {
        if (
            $action->id == 'forgot-password'
        ) {
            Yii::$app->controller->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     *
     * @return array
     */
    private function response()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = $this->data;
        if (empty($response)) {
            $response = [];
        }
        $data = [
            'success' => Yii::$app->response->isSuccessful,
            'status' => $this->response_code,
            'message' => $this->message,
            'data' => $response,
        ];
        if (!empty($this->customKeys)) {
            $data = array_merge($data, $this->customKeys);
        }
        return (object) $data;
    }

    private function response_array()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = $this->data;
        if (empty($response)) {
            $response = [];
        }
        $data = [
            'success' => Yii::$app->response->isSuccessful,
            'status' => $this->response_code,
            'message' => $this->message,
            'data' => $response,
        ];
        if (!empty($this->customKeys)) {
            $data = array_merge($data, $this->customKeys);
        }
        return $data;
    }


    public function actionHome($lang = 'en', $user_id = "", $page = 1, $per_page = 20)
    {
        $settings = \app\models\Settings::find()
            ->where(['setting_id' => 1])
            // ->asArray()
            ->one();
        $notificationModel = 0;
        if (!empty($user_id)) {
            $notificationModel = Notifications::find()->where(['user_id' => $user_id, "is_read" => 0])->count();
        }

        $this->data = [
            'support_phone' => !empty($settings) ? $settings['support_phone'] : '',
            'support_email' => !empty($settings) ? $settings['support_email'] : '',
            'upcoming_appointments' => \app\helpers\AppHelper::countUpcomingAppointment($user_id),
            'banner_list' => $this->getBannerList($lang),
            'what_we_have' => $this->getWhatWeHave($lang),
            'main_categories' => $this->getSettingImages($lang),
            'doctor_specialties' => $this->getDoctorSpecialtiesList($lang),
            'top_doctor' => $this->getTopDoctorList($lang),
            'top_hospital' => $this->getTopClinicsList($lang),
            'test_categories' => $this->getTestCategories($lang),
            'un_read_notifications' => $notificationModel,
        ];
        return $this->response();
    }



    private function getWhatWeHave($lang = 'en')
    {
        $settings = \app\models\Settings::find()
            ->where(['setting_id' => 1])
            ->asArray()
            ->one();
        $list = [
            [
                "name" => ($lang != 'ar') ? "Physical Consultations" : ' الاستشارات البدنية ',
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['physical_consultation_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "I",
            ],
            [
                "name" => ($lang != 'ar') ? "Online Consultations" : 'استشارات عبر الإنترنت',
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['online_consultation_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "V",
            ]
        ];
        return $list;
    }

    private function getSettingImages($lang = 'en')
    {
        $settings = \app\models\Settings::find()
            ->where(['setting_id' => 1])
            ->asArray()
            ->one();
        $list = [

            [
                "name" => ($lang != 'ar') ? "Hospital" : " المستشفى ",
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['hospital_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "H",
            ],
            [
                "name" => ($lang != 'ar') ? "Lab Tests" : 'الاختبارات المخبرية',
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['lab_test_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "T",
            ],
            [
                "name" => ($lang != 'ar') ? "Clinic" : "عياده ",
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['beauty_clinic_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "C",
            ],
            [
                "name" => ($lang != 'ar') ? "Pharmacies" : " الصيدليات ",
                "image" => !empty($settings) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $settings['pharmacies_image']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'),
                "type" => "F",
            ]
        ];
        return $list;
    }

    private function getBannerList($lang = "en", $limit = 10)
    {
        $bannerList = [];
        $query = Banner::find()
            ->where(['is_deleted' => 0, 'is_active' => 1])
            ->orderby(['sort_order' => SORT_ASC, 'banner_id' => SORT_DESC]);
        if ($limit != null) {
            $query->limit($limit);
        }
        $banners = $query->all();
        foreach ($banners as $row) {
            $banner_title = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $sub_title = ($lang == 'en') ? $row->sub_title_en : $row->sub_title_ar;
            $banner_image = (!empty($row->{'image_' . $lang})) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->{'image_' . $lang}) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            $d['id'] = $row->banner_id;
            $d['banner_title'] = (!empty($banner_title)) ? $banner_title : "";
            $d['sub_title'] = (!empty($sub_title)) ? $sub_title : "";
            $d['link_type'] = $row->link_type;
            $d['link_id'] = (string) $row->link_id;
            $d['url'] = (string) $row->url;
            $d['position'] = (string) $row->position;
            $d['image'] = $banner_image;
            array_push($bannerList, $d);
        }
        return $bannerList;
    }
    public function actionInsuranceList($lang = "en")
    {
        $list = [];
        $model = \app\models\Insurances::find()
            ->where(['is_deleted' => 0, 'is_active' => 1])
            ->orderby(['insurance_id' => SORT_ASC])->all();
        if (!empty($model)) {
            foreach ($model as $row) {
                $name               = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['insurance_id']  = $row->insurance_id;
                $d['name']          = (!empty($name)) ? $name : "";
                array_push($list, $d);
            }
            $this->response_code = 200;
            $this->message =  ($lang == "en") ? "Insurance List" : "قائمة التأمين";
            $this->data = $list;
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? "Insurance list not found" : "قائمة التأمين غير موجودة";
            // $this->data = new stdClass();
        }
        return $this->response_array();
    }
    private function getDoctorSpecialtiesList($lang = "en", $limit = 10)
    {
        $list = [];
        $query = Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'type' => 'D', 'hide_category_in_app' => 0, 'show_in_home' => 1]);

        if ($limit != null) {
            $query->limit($limit);
        }
        $model = $query->all();
        foreach ($model as $row) {
            $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
            if ($lang == 'en' && !empty($row->icon)) {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon);
            } elseif ($lang == 'ar' && !empty($row->icon_ar)) {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon_ar);
            } else {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }

            $d['id'] = $row->category_id;
            $d['name'] = (!empty($name)) ? $name : "";
            $d['image'] = $category_icon;
            array_push($list, $d);
        }
        return $list;
    }

    private function getTestCategories($lang = "en", $limit = 10)
    {
        $list = [];
        $query = Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'type' => 'T', 'show_in_home' => 1]);

        if ($limit != null) {
            $query->limit($limit);
        }
        $model = $query->all();
        foreach ($model as $row) {
            $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
            if ($lang == 'en' && !empty($row->icon)) {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon);
            } elseif ($lang == 'ar' && !empty($row->icon_ar)) {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon_ar);
            } else {
                $category_icon = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }

            $d['id'] = $row->category_id;
            $d['name'] = (!empty($name)) ? $name : "";
            $d['image'] = $category_icon;
            array_push($list, $d);
        }
        return $list;
    }

    private function getTopDoctorList($lang = "en", $limit = 10)
    {
        $list = [];
        $query = Doctors::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'is_featured' => 1]);

        if ($limit != null) {
            $query->limit($limit);
        }
        $model = $query->all();
        foreach ($model as $row) {
            $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
            if ($lang == 'en' && !empty($row->image)) {
                $image = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image);
            } else {
                $image = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }

            $c_list = [];
            if (isset($row->doctorCategories)) {
                foreach ($row->doctorCategories as $cat) {
                    $ds['category_id'] = $cat->category_id;
                    $ds['name'] = ($lang == 'ar') ? $cat->category->name_ar : $cat->category->name_en;
                    array_push($c_list, $ds);
                }
            }

            $d['id'] = $row->doctor_id;
            $d['name'] = (!empty($name)) ? $name : "";
            $d['image'] = $image;
            $d['years_experience'] = $row->years_experience;
            $d['specialties'] = $c_list;
            array_push($list, $d);
        }
        return $list;
    }

    private function getTopClinicsList($lang = "en", $limit = 10)
    {
        $list = [];
        $query = Clinics::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'is_featured' => 1]);

        if ($limit != null) {
            $query->limit($limit);
        }
        $model = $query->all();
        foreach ($model as $row) {
            $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
            if ($lang == 'ar') {
                $image = (!empty($row->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            } else {
                $image = (!empty($row->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }
            $total_doctors = Doctors::find()->where(['is_deleted' => 0, 'is_active' => 1, 'clinic_id' => $row->clinic_id])->count();
            $d['id'] = $row->clinic_id;
            $d['name'] = (!empty($name)) ? $name : "";
            $d['image'] = $image;
            $d['governorate'] = (isset($row->governorate)) ? $row->governorate->{'name_' . $lang} : '';
            $d['area'] = (isset($row->area)) ? $row->area->{'name_' . $lang} : '';
            $d['total_doctors'] = $total_doctors;
            array_push($list, $d);
        }
        return $list;
    }

    /**
     *
     * @return mixed
     * @User Register
     */
    public function actionRegister($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $userCount = \app\models\Users::find()
                ->where(['email' => $request['email'], 'is_deleted' => 0])
                ->one();
            if (empty($userCount)) {
                $model = new \app\models\Users();
                $model->first_name = (isset($request['first_name']) && !empty($request['first_name'])) ? $request['first_name'] : "";
                $model->last_name = (isset($request['last_name']) && !empty($request['last_name'])) ? $request['last_name'] : "";
                $model->gender = (isset($request['gender']) && !empty($request['gender'])) ? $request['gender'] : null;
                $model->dob = (isset($request['dob']) && !empty($request['dob'])) ? date('Y-m-d', strtotime($request['dob'])) : "";
                $model->email = $request['email'];
                $model->password = Yii::$app->security->generatePasswordHash($request['password']);
                $model->phone_code = (isset($request['phone_code']) && !empty($request['phone_code'])) ? $request['phone_code'] : "";
                $model->phone = (isset($request['phone']) && !empty($request['phone'])) ? $request['phone'] : "";
                $model->blood_group = (isset($request['blood_group']) && !empty($request['blood_group'])) ? $request['blood_group'] : "";
                $model->civil_id = (isset($request['civil_id']) && !empty($request['civil_id'])) ? $request['civil_id'] : "";
                $model->previous_hospital_visit = (isset($request['previous_hospital_visit']) && !empty($request['previous_hospital_visit'])) ? $request['previous_hospital_visit'] : "";
                $model->device_token = (isset($request['device_token'])) ? $request['device_token'] : "";
                $model->device_type = (isset($request['device_type'])) ? $request['device_type'] : "";
                $model->device_model = (isset($request['device_model'])) ? $request['device_model'] : "";
                $model->app_version = (isset($request['app_version'])) ? $request['app_version'] : "";
                $model->os_version = (isset($request['os_version'])) ? $request['os_version'] : "";
                $model->create_date = date('Y-m-d H:i:s');
                $model->newsletter_subscribed = $request['newsletter_subscribed'];
                if (isset($request['image']) && !empty($request['image'])) {
                    $image = base64_decode($request['image']);
                    if ($image) {
                        $img = imagecreatefromstring($image);
                        if ($img !== false) {
                            $imageName = time() . '.png';
                            imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                            imagedestroy($img);
                            $model->image = $imageName;
                        }
                    }
                }

                $model->push_enabled = 1;
                if ($model->save()) {
                    Yii::$app->mailer->compose('@app/mail/register', [
                        "name" => $model->first_name . ' ' . $model->last_name,
                        "email" => $model->email,
                        "password" => $request['password'],
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->email)
                        ->setSubject("Welcome to Edayat")
                        ->send();

                    $this->message = ($lang == "en") ? 'User successfully registered' : "تم تسجيل المستخدم بنجاح";
                    $this->data = [
                        'id' => (string) $model->user_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'gender' => (string) $model->gender,
                        'dob' => (string) date('Y-m-d', strtotime($model->dob)),
                        'email' => $model->email,
                        'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone_code' => (string) $model->phone_code,
                        'phone' => $model->phone,
                        'blood_group' => $model->blood_group,
                        'civil_id' => $model->civil_id,
                        'previous_hospital_visit' => $model->previous_hospital_visit,
                        'code' => (string) $model->code,
                        'is_phone_verified' => (int) $model->is_phone_verified,
                        'is_email_verified' => (int) $model->is_email_verified,
                        'is_social_register' => (int) $model->is_social_register,
                        'social_register_type' => (string) $model->social_register_type,
                        'device_token' => (string) $model->device_token,
                        'device_type' => (string) $model->device_type,
                        'device_model' => (string) $model->device_model,
                        'app_version' => (string) $model->app_version,
                        'os_version' => (string) $model->os_version,
                        'push_enabled' => (string) $model->push_enabled,
                        'newsletter_subscribed' => (int) $model->newsletter_subscribed,
                        'create_date' => $model->create_date,
                        'total_prescription' => $this->getTotalUesrPrescriptions($model->user_id),
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 406;
                $this->message =  ($lang == "en") ? 'User with same email already exists.' : "المستخدم بنفس البريد الإلكتروني موجود بالفعل";
                $this->data = new stdClass();
            }
        } else {
            $this->response_code = 500;
            $this->message =  ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionSocialRegister($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Users::find()
                ->where(['email' => $request['email'], 'is_deleted' => 0])
                ->one();
            if (empty($model)) {
                $model = new \app\models\Users();
                $model->first_name = $request['first_name'];
                $model->last_name = $request['last_name'];
                $model->gender = (isset($request['gender']) && !empty($request['gender'])) ? $request['gender'] : null;
                if (isset($request['dob']) && !empty($request['dob'])) {
                    $model->dob = date('Y-m-d', strtotime($request['dob']));
                }
                $model->email = $request['email'];
                $randomString = Yii::$app->security->generateRandomString(6);
                $model->password = Yii::$app->security->generatePasswordHash($randomString);
                if (isset($request['phone']) && !empty($request['phone'])) {
                    $model->phone = $request['phone'];
                }
                $model->is_social_register = 1;
                $model->social_register_type = $request['social_register_type'];
                if (!empty($request['device_token'])) {
                    $model->device_token = $request['device_token'];
                }
                if (!empty($request['device_type'])) {
                    $model->device_type = $request['device_type'];
                }
                if (!empty($request['device_model'])) {
                    $model->device_model = $request['device_model'];
                }
                if (!empty($request['app_version'])) {
                    $model->app_version = $request['app_version'];
                }
                if (!empty($request['os_version'])) {
                    $model->os_version = $request['os_version'];
                }
                $model->create_date = date('Y-m-d H:i:s');
                $model->push_enabled = 1;
                if (isset($request['facebook_id']) && $request['facebook_id'] != "") {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://graph.facebook.com/' . $request['facebook_id'] . '/picture?redirect=false&height=200&width=200');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    curl_close($ch);
                    if (!empty($data)) {
                        $data = json_decode($data);
                        $imageName = time() . '.jpg';
                        $file = fopen('uploads/' . $imageName, 'w+');
                        fputs($file, file_get_contents($data->data->url));
                        fclose($file);
                        $model->image = $imageName;
                    }
                }
                if ($model->save()) {
                    $this->message = ($lang == "en") ? 'User successfully registered' : "تم تسجيل المستخدم بنجاح";;
                    $this->data = [
                        'id' => (string) $model->user_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'gender' => (string) $model->gender,
                        'dob' => (!empty($model->dob)) ? (string) $model->dob : "",
                        'email' => $model->email,
                        'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone' => (!empty($model->phone)) ? $model->phone : "",
                        'code' => (string) $model->code,
                        'blood_group' => (string) $model->blood_group,
                        'civil_id' => (string) $model->civil_id,
                        'previous_hospital_visit' => $model->previous_hospital_visit,
                        'is_phone_verified' => (int) $model->is_phone_verified,
                        'is_email_verified' => (int) $model->is_email_verified,
                        'is_social_register' => (int) $model->is_social_register,
                        'social_register_type' => (string) $model->social_register_type,
                        'device_token' => $model->device_token,
                        'device_type' => $model->device_type,
                        'device_model' => $model->device_model,
                        'app_version' => $model->app_version,
                        'os_version' => $model->os_version,
                        'push_enabled' => (string) $model->push_enabled,
                        'newsletter_subscribed' => (int) $model->newsletter_subscribed,
                        'create_date' => $model->create_date,
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $model->first_name = $request['first_name'];
                $model->last_name = $request['last_name'];
                $model->gender = (!empty($request['gender'])) ? $request['gender'] : null;
                if (isset($request['dob']) && !empty($request['dob'])) {
                    $model->dob = date('Y-m-d', strtotime($request['dob']));
                }
                $model->email = $request['email'];
                $model->push_enabled = 1;
                if (!empty($request['device_token'])) {
                    $model->device_token = $request['device_token'];
                }
                if (!empty($request['device_type'])) {
                    $model->device_type = $request['device_type'];
                }
                if (!empty($request['device_model'])) {
                    $model->device_model = $request['device_model'];
                }
                if (!empty($request['app_version'])) {
                    $model->app_version = $request['app_version'];
                }
                if (!empty($request['os_version'])) {
                    $model->os_version = $request['os_version'];
                }
                $model->is_social_register = 1;
                $model->social_register_type = $request['social_register_type'];
                if (isset($request['phone']) && !empty($request['phone'])) {
                    $model->phone = $request['phone'];
                }
                if (isset($request['facebook_id']) && $request['facebook_id'] != "") {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://graph.facebook.com/' . $request['facebook_id'] . '/picture?redirect=false&height=200&width=200');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    curl_close($ch);
                    if (!empty($data)) {
                        $data = json_decode($data);
                        $imageName = time() . '.jpg';
                        $file = fopen('uploads/' . $imageName, 'w+');
                        fputs($file, file_get_contents($data->data->url));
                        fclose($file);
                        $model->image = $imageName;
                    }
                }
                if ($model->save()) {
                    $this->data = [
                        'id' => (string) $model->user_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'gender' => (string) $model->gender,
                        'dob' => (string) $model->dob,
                        'email' => $model->email,
                        'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone' => $model->phone,
                        'code' => (string) $model->code,
                        'blood_group' => (string) $model->blood_group,
                        'civil_id' => (string) $model->civil_id,
                        'previous_hospital_visit' => $model->previous_hospital_visit,
                        'is_phone_verified' => (int) $model->is_phone_verified,
                        'is_email_verified' => (int) $model->is_email_verified,
                        'is_social_register' => (int) $model->is_social_register,
                        'social_register_type' => (string) $model->social_register_type,
                        'device_token' => $model->device_token,
                        'device_type' => $model->device_type,
                        'device_model' => $model->device_model,
                        'app_version' => $model->app_version,
                        'os_version' => $model->os_version,
                        'push_enabled' => (string) $model->push_enabled,
                        'newsletter_subscribed' => (int) $model->newsletter_subscribed,
                        'create_date' => $model->create_date,
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionLogin($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        $this->data = new stdClass();
        if (!empty($request)) {
            $model = \app\models\Users::find()
                ->where(['email' => $request['email'], 'is_deleted' => 0])
                ->one();
            if (!empty($model)) {
                $validate = Yii::$app->security->validatePassword($request['password'], $model->password);
                if ($validate) {
                    if (isset($request['device_type']) && $request['device_type'] != "") {
                        $model->device_type = $request['device_type'];
                    }
                    if (isset($request['device_model']) && $request['device_model'] != "") {
                        $model->device_model = $request['device_model'];
                    }
                    if (isset($request['app_version']) && $request['app_version'] != "") {
                        $model->app_version = $request['app_version'];
                    }
                    if (isset($request['os_version']) && $request['os_version'] != "") {
                        $model->os_version = $request['os_version'];
                    }
                    if (isset($request['device_token']) && $request['device_token'] != "") {
                        $model->device_token = $request['device_token'];
                    }
                    $model->save(false);
                    $user_dob = $model->dob;
                    $today = date("Y-m-d");
                    $user_age = "";
                    if ($user_dob != null) {
                        $diff_user = date_diff(date_create($user_dob), date_create($today));
                        $user_age = $diff_user->format('%y');
                    }
                    $kids = \app\models\Kids::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0])
                        ->all();
                    $kidsList = [];
                    if (!empty($kids)) {
                        foreach ($kids as $kid) {
                            $age = '';
                            if ($kid->dob != null) {
                                $dateOfBirth = $kid->dob;
                                $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                $age = $diff->format('%y');
                            }
                            $k = [
                                'id' => $kid->kid_id,
                                'name' => $kid->name_en,
                                'civil_id' => $kid->civil_id,
                                'dob' => $kid->dob,
                                'age' => $age,
                                'gender' => $kid->gender,
                                'blood_group' => $kid->blood_group,
                                'relation' => $kid->relation,
                            ];
                            array_push($kidsList, $k);
                        }
                    }

                    $notificationModel = 0;
                    if (!empty($user_id)) {
                        $notificationModel = Notifications::find()->where(['user_id' => $user_id, "is_read" => 0])->count();
                    }


                    $this->data = [
                        'id' => (string) $model->user_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'gender' => (string) $model->gender,
                        'dob' => (string) $model->dob,
                        'height' => (string) $model->height,
                        'weight' => (string) $model->weight,
                        'blood_group' => (string) $model->blood_group,
                        'civil_id' => (string) $model->civil_id,
                        'previous_hospital_visit' => (string) $model->previous_hospital_visit,
                        'user_age' => (string) $user_age,
                        'email' => $model->email,
                        'image' => (string) ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone_code' => (string) $model->phone_code,
                        'phone' => $model->phone,
                        'code' => (string) $model->code,
                        'is_phone_verified' => (int) $model->is_phone_verified,
                        'is_email_verified' => (int) $model->is_email_verified,
                        'is_social_register' => (int) $model->is_social_register,
                        'social_register_type' => (string) $model->social_register_type,
                        'device_token' => (string) $model->device_token,
                        'device_type' => (string) $model->device_type,
                        'device_model' => (string) $model->device_model,
                        'app_version' => (string) $model->app_version,
                        'os_version' => (string) $model->os_version,
                        'push_enabled' => (string) $model->push_enabled,
                        'newsletter_subscribed' => (int) $model->newsletter_subscribed,
                        'create_date' => $model->create_date,
                        'total_prescription' => $this->getTotalUesrPrescriptions($model->user_id),
                        'kids' => $kidsList,
                        'un_read_notifications' => $notificationModel
                    ];
                } else {
                    $this->response_code = 201;
                    $this->message = ($lang == "en") ? 'Invalid password' : "رمز مرور خاطئ";
                }
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? 'User does not exist' : "المستخدم غير موجود";
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionEditProfile($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Users::find()
                ->where(['user_id' => $request['user_id'], 'is_deleted' => 0])
                ->one();

            if (!empty($model)) {
                if (isset($request['first_name']) && !empty($request['first_name'])) {
                    $model->first_name = $request['first_name'];
                }

                if (isset($request['last_name']) && !empty($request['last_name'])) {
                    $model->last_name = $request['last_name'];
                }

                if (isset($request['gender']) && !empty($request['gender'])) {
                    $model->gender = $request['gender'];
                }

                if (isset($request['dob']) && !empty($request['dob'])) {
                    $model->dob = date('Y-m-d', strtotime($request['dob']));
                }



                if (isset($request['phone_code']) && !empty($request['phone_code'])) {
                    $model->phone_code = $request['phone_code'];
                }

                if (isset($request['height']) && !empty($request['height'])) {
                    $model->height = $request['height'];
                }

                if (isset($request['weight']) && !empty($request['weight'])) {
                    $model->weight = $request['weight'];
                }

                if (isset($request['blood_group']) && !empty($request['blood_group'])) {
                    $model->blood_group = $request['blood_group'];
                }

                if (isset($request['civil_id']) && !empty($request['civil_id'])) {
                    $model->civil_id = $request['civil_id'];
                }

                if (isset($request['previous_hospital_visit']) && !empty($request['previous_hospital_visit'])) {
                    $model->previous_hospital_visit = $request['previous_hospital_visit'];
                }

                if (isset($request['insurance_id']) && !empty($request['insurance_id'])) {
                    $model->insurance_id = $request['insurance_id'];
                }

                if (isset($request['insurance_numbar']) && !empty($request['insurance_numbar'])) {
                    $model->insurance_numbar = $request['insurance_numbar'];
                }

                if (isset($request['image']) && !empty($request['image'])) {
                    $image = base64_decode($request['image']);
                    if ($image) {
                        $img = imagecreatefromstring($image);
                        if ($img !== false) {
                            $imageName = time() . '.png';
                            imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                            imagedestroy($img);
                            $model->image = $imageName;
                        }
                    }
                }
                if (isset($request['newsletter_subscribed']) && $request['newsletter_subscribed'] != "") {
                    $model->newsletter_subscribed = $request['newsletter_subscribed'];
                }

                if (isset($request['old_password']) && !empty($request['old_password'])) {
                    $data_password = $model->password;
                    $old_Password = $request['old_password'];

                    if (Yii::$app->getSecurity()->validatePassword($old_Password, $data_password)) {
                        if (isset($request['new_password']) && !empty($request['new_password'])) {
                            $model->password = Yii::$app->security->generatePasswordHash($request['new_password']);
                        } else {
                            $this->response_code = 201;
                            $this->message = ($lang == "en") ? 'New password cannot be blank.' : "لا يمكن أن تكون كلمة المرور الجديدة فارغة.";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                    } else {
                        $this->response_code = 201;
                        $this->message = ($lang == "en") ? 'Old password does not match.' : "كلمة المرور القديمة غير متطابقة.";
                        $this->data = new stdClass();
                        return $this->response();
                    }
                }

                if (isset($request['password']) && !empty($request['password'])) {
                    $model->password = Yii::$app->security->generatePasswordHash($request['password']);
                }

                if ($model->save()) {
                    $this->message = ($lang == "en") ? 'User details successfully updated.' : "تم تحديث تفاصيل المستخدم بنجاح.";
                    $this->data = [
                        'id' => (string) $model->user_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'gender' => (string) $model->gender,
                        'dob' => (string) $model->dob,
                        'email' => $model->email,
                        'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone_code' => (string) $model->phone_code,
                        'phone' => ($model->phone != "") ? $model->phone : "",
                        'blood_group' => ($model->blood_group != "") ? $model->blood_group : "",
                        'civil_id' => ($model->civil_id != "") ? $model->civil_id : "",
                        'insurance_id' => ($model->insurance_id != "") ? $model->insurance_id : "",
                        'insurance_name' => ($model->insurance_numbar != "") ? $model->insurance->name_en : "",
                        'insurance_numbar' => ($model->insurance_numbar != "") ? $model->insurance_numbar : "",
                        'previous_hospital_visit' => ($model->previous_hospital_visit != "") ? $model->previous_hospital_visit : "",
                        'code' => (string) $model->code,
                        'height' => (string) $model->height,
                        'weight' => (string) $model->weight,
                        'blood_group' => (string) $model->blood_group,
                        'is_phone_verified' => (int) $model->is_phone_verified,
                        'is_email_verified' => (int) $model->is_email_verified,
                        'is_social_register' => (int) $model->is_social_register,
                        'social_register_type' => (string) $model->social_register_type,
                        'device_token' => (string) $model->device_token,
                        'device_type' => (string) $model->device_type,
                        'device_model' => (string) $model->device_model,
                        'app_version' => (string) $model->app_version,
                        'os_version' => (string) $model->os_version,
                        'push_enabled' => (string) $model->push_enabled,
                        'newsletter_subscribed' => (int) $model->newsletter_subscribed,
                        'create_date' => $model->create_date,
                        'total_prescription' => $this->getTotalUesrPrescriptions($model->user_id),
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? 'User not found.' : "لم يتم العثور على المستخدم.";
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }



    public function actionForgotPassword($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            if ($request['email'] != '') {
                $model = \app\models\Users::find()
                    ->where(['email' => $request['email'], 'is_deleted' => 0])
                    ->one();
            } elseif ($request['mobile'] != '') {
                $model = \app\models\Users::find()
                    ->where(['phone' => $request['mobile'], 'is_deleted' => 0])
                    ->one();
            }
            if (!empty($model)) {
                $newPassword = Yii::$app->security->generateRandomString(8);
                $model->password = Yii::$app->security->generatePasswordHash($newPassword);
                if ($model->save(false)) {
                    if ($request['email'] != '') {
                        Yii::$app->mailer->compose('@app/mail/user-forgot-password', [
                            "name" => $model->first_name . ' ' . $model->last_name,
                            "email" => $model->email,
                            "password" => $newPassword,
                            "msg" => 'Greetings from Edayat, as per your request we have successfully reset your password. To log in, please use the password below:',
                            'supportEmail' => Yii::$app->params['supportEmail'],
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($model->email)
                            ->setSubject("Reset password")
                            ->send();
                        $this->message = ($lang == "en") ? 'Password was sent, please check Email.' : 'تم إعادة تعيين كلمة المرور بنجاح.يرجى التحقق من صندوق الوارد الخاص بك';
                    }
                    if ($request['mobile'] != '') {
                        $curl = curl_init();
                        $msg = (($lang == "en") ? urlencode('Your new password for eyadat app: ') : urlencode('كلمة مرورك الجديدة لتطبيق Eyadat:')) . $newPassword;
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'http://smsbox.com/smsgateway/services/messaging.asmx/Http_SendSMS?username=Support&password=Gic@2021&customerid=3004&sendertext=KW-INFO&messagebody=' . $msg . '&recipientnumbers=965' . $request['mobile'] . '&defdate=&isblink=false&isflash=false',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ));

                        $response = curl_exec($curl);

                        if ($response) {
                            $this->message = ($lang == "en") ? 'Password was sent, please check SMS. ' : 'تم إعادة تعيين كلمة المرور بنجاح.يرجى التحقق من رسائل SMS الخاصة بك';
                        }

                        curl_close($curl);
                    }
                } else {
                    $this->response_code = 500;
                    $this->message = $model->errors;
                }
            } else {
                $this->response_code = 404;
                if ($request['email'] != '') {
                    $this->message = ($lang == "en")  ? 'Email address does not exist' : 'لا وجود عنوان البريد الإلكتروني';
                } elseif ($request['mobile'] != '') {
                    $this->message = ($lang == "en") ?  'Mobile number does not exist' : 'رقم الجوال غير موجود';
                }
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionCountry($lang = 'en', $is_countries_enabled = "")
    {
        if ($is_countries_enabled != '') {
            $model = \app\models\Country::find()
                ->where(['is_deleted' => 0])
                ->all();
        } else {
            $model = \app\models\Country::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->all();
        }
        $result = [];
        foreach ($model as $row) {
            $d['id'] = $row->country_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $d['phonecode'] = $row->phonecode;
            $d['iso'] = $row->iso;
            $states = $this->getCountryState($lang, $row);
            $d['has_states'] = !empty($states) ? '1' : '0';
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    public function actionCms($page, $lang = "en")
    {
        $cms = \app\models\Cms::findOne($page);
        if (!empty($cms)) {
            $this->data = ['page' => ($lang == 'en') ? $cms->title_en : $cms->title_ar, 'content' => ($lang == 'en') ? $cms->content_en : $cms->content_ar];
        } else {
            $this->response_code = 404;
            $this->message = ($lang == "en") ? 'No content for this page.' : "لا يوجد محتوى لهذه الصفحة.";
        }
        return $this->response();
    }

    public function actionState($country_id, $lang = 'en')
    {
        $model = \app\models\State::find()
            ->where(['country_id' => $country_id, 'is_deleted' => 0, 'is_active' => 1])
            ->all();
        $result = [];
        foreach ($model as $row) {
            $d['id'] = $row->state_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $areas = $this->getStateAreas($lang, $row);
            $d['has_areas'] = !empty($areas) ? "1" : "0";
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    public function actionArea($state_id, $lang = 'en')
    {
        $model = \app\models\Area::find()
            ->where(['state_id' => $state_id, 'is_deleted' => 0, 'is_active' => 1])
            ->all();
        $result = [];
        foreach ($model as $row) {
            $d['id'] = $row->area_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $blocks = $this->getBlocks($lang, $row);
            $d['has_blocks'] = !empty($blocks) ? "1" : "0";
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    public function actionSector($area_id, $lang = 'en')
    {
        $query = \app\models\Block::find()
            ->select([
                'block.*',
                'cast(substr(name_en, 6) as signed) AS block_no',
            ])
            ->where(['area_id' => $area_id])
            ->orderBy([
                'block_no' => SORT_ASC
            ]);
        $model = $query->all();
        $result = [];
        foreach ($model as $row) {
            $d['id'] = $row->block_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    private function getCountryState($lang = 'en', $row)
    {
        $result = [];
        if (!empty($row->states)) {
            foreach ($row->states as $st) {
                if ($st->is_deleted == 0 && $st->is_active == 1) {
                    $d['id'] = $st->state_id;
                    $d['name'] = ($lang == 'en') ? $st->name_en : $st->name_ar;
                    array_push($result, $d);
                }
            }
        }
        return $result;
    }

    /**
     *
     * @param type $lang
     * @param type $row
     * @return array
     */
    private function getStateAreas($lang = 'en', $row)
    {
        $result = [];
        if (!empty($row->areas)) {
            foreach ($row->areas as $ar) {
                if ($ar->is_deleted == 0 && $ar->is_active == 1) {
                    $d['id'] = $ar->area_id;
                    $d['name'] = ($lang == 'en') ? $ar->name_en : $ar->name_ar;
                    $d['blocks'] = $this->getBlocks($lang, $ar);
                    array_push($result, $d);
                }
            }
        }
        return $result;
    }

    private function getBlocks($lang = 'en', $row)
    {
        $result = [];
        foreach ($row->blocks as $block) {
            $d['id'] = $block->block_id;
            $d['name'] = ($lang == 'en') ? $block->name_en : $block->name_ar;
            array_push($result, $d);
        }
        return $result;
    }

    public function actionBlockList($lang = 'en', $area_id)
    {
        $model = \app\models\Block::find()
            ->where(['area_id' => $area_id])
            ->all();

        $result = [];
        foreach ($model as $block) {
            $d['id'] = $block['block_id'];
            $d['name'] = ($lang == 'en') ? $block['name_en'] : $block['name_ar'];
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    public function actionAddUserFamily($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = new \app\models\Kids();
            if (isset($request['image']) && !empty($request['image'])) {
                $image = base64_decode($request['image']);
                if ($image) {
                    $img = imagecreatefromstring($image);
                    if ($img !== false) {
                        $imageName = time() . '.png';
                        imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                        imagedestroy($img);
                        $model->image = $imageName;
                    }
                }
            }

            $model->name_en = (isset($request['name_en'])) ? $request['name_en'] : "";
            $model->name_ar = (isset($request['name_ar'])) ? $request['name_ar'] : "";
            $model->user_id = (isset($request['user_id'])) ? $request['user_id'] : "";
            $model->gender = (isset($request['gender'])) ? $request['gender'] : "";
            $model->relation = (isset($request['relation'])) ? $request['relation'] : "";
            $model->blood_group = (isset($request['blood_group'])) ? $request['blood_group'] : "";
            $model->dob = (isset($request['dob'])) ? date('Y-m-d', strtotime($request['dob'])) : "";
            $model->civil_id = (isset($request['civil_id'])) ? $request['civil_id'] : "";
            $model->is_deleted = 0;
            $model->created_at = date('Y-m-d H:i:S');
            $model->updated_at = date('Y-m-d H:i:S');
            if ($model->save()) {
                $this->message = ($lang == "en") ? 'kids successfully added.' : 'تم إضافة الأطفال بنجاح.';

                $age = '';
                if ($model->dob != null) {
                    $dateOfBirth = $model->dob;
                    $today = date("Y-m-d");
                    $diff = date_diff(date_create($dateOfBirth), date_create($today));
                    $age = $diff->format('%y');
                }

                $this->data = [
                    'kid_id' => $model->kid_id,
                    'user_id' => $model->user_id,
                    'user_name' => ($model->user->first_name . ' ' . $model->user->last_name),
                    'name' => ($lang == 'ar') ? $model->name_ar : $model->name_en,
                    'dob' => date('d-m-Y', strtotime($model->dob)),
                    'age' => $age,
                    'civil_id' => $model->civil_id,
                    'gender' => $model->gender,
                    'relation' => $model->relation,
                    'blood_group' => $model->blood_group,
                    'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                ];
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionUpdateUserFamily($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $kid_id = $request['kid_id'];
            $model = \app\models\Kids::find()
                ->where(['kid_id' => $kid_id])
                ->one();
            if (isset($request['image']) && !empty($request['image'])) {
                $image = base64_decode($request['image']);
                if ($image) {
                    $img = imagecreatefromstring($image);
                    if ($img !== false) {
                        $imageName = time() . '.png';
                        imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                        imagedestroy($img);
                        $model->image = $imageName;
                    }
                }
            }

            $model->name_en = (isset($request['name_en'])) ? $request['name_en'] : "";
            $model->name_ar = (isset($request['name_ar'])) ? $request['name_ar'] : "";
            $model->user_id = (isset($request['user_id'])) ? $request['user_id'] : "";
            $model->gender = (isset($request['gender'])) ? $request['gender'] : "";
            $model->relation = (isset($request['relation'])) ? $request['relation'] : "";
            $model->blood_group = (isset($request['blood_group'])) ? $request['blood_group'] : "";
            $model->dob = (isset($request['dob'])) ? date('Y-m-d', strtotime($request['dob'])) : "";
            $model->civil_id = (isset($request['civil_id'])) ? $request['civil_id'] : "";
            $model->is_deleted = 0;
            $model->created_at = date('Y-m-d H:i:S');
            $model->updated_at = date('Y-m-d H:i:S');
            if ($model->save()) {
                $this->message = ($lang == "en")  ? 'Kids successfully updated.' : 'تم تحديث الأطفال بنجاح.';
                $this->data = [
                    'kid_id' => $model->kid_id,
                    'user_id' => $model->user_id,
                    'user_name' => ($model->user->first_name . ' ' . $model->user->last_name),
                    'kid_id' => $model->kid_id,
                    'name' => ($lang == 'ar') ? $model->name_ar : $model->name_en,
                    'dob' => date('d-m-Y', strtotime($model->dob)),
                    'civil_id' => $model->civil_id,
                    'gender' => $model->gender,
                    'relation' => $model->relation,
                    'blood_group' => $model->blood_group,
                    'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                ];
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionUserFamilyList($lang = 'en', $user_id = '')
    {
        $model = \app\models\Kids::find()
            ->where(['is_deleted' => 0, 'user_id' => $user_id])
            ->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $d['kid_id'] = $row->kid_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['user_id'] = $row->user_id;
                $d['User_name'] = ($lang == 'en') ? ($row->user->first_name . ' ' . $row->user->last_name) : $row->name_ar;
                $d['dob'] = date('d-m-Y', strtotime($row->dob));
                $d['civil_id'] = $row->civil_id;
                $d['gender'] = $row->gender;
                $d['relation'] = $row->relation;
                $d['blood_group'] = $row->blood_group;
                $d['image'] = ($row->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ?  'No family list found' : 'لم يتم العثور على قائمة عائلية';
            $this->data = "";
            return $this->response_array();
        }
        $this->data = $result;
        return $this->response_array();
    }

    public function actionDeleteUserFamily($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $id = $request['id'];
            $user_id = $request['user_id'];
            $data = explode(',', $id);
            foreach ($data as $d) {
                $model = \app\models\Kids::find()
                    ->where(['user_id' => $user_id, 'kid_id' => $d])
                    ->one();
                if (!empty($model)) {
                    $model->is_deleted = 1;
                    $model->save();
                }
            }
            $models = \app\models\Kids::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->orderBy(['user_id' => SORT_DESC])
                ->all();
            if (!empty($models)) {
                $result = [];
                foreach ($models as $row) {
                    $d = [];
                    $d['kid_id'] = $row->kid_id;
                    $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                    $d['user_id'] = $row->user_id;
                    $d['User_name'] = ($lang == 'en') ? ($row->user->first_name . ' ' . $row->user->last_name) : $row->name_ar;
                    $d['dob'] = date('d-m-Y', strtotime($row->dob));
                    $d['civil_id'] = $row->civil_id;
                    array_push($result, $d);
                }
                $this->data = $result;
            } else {
                $this->message = ($lang == "en") ? 'User family successfully deleted.' : 'تم حذف عائلة المستخدم بنجاح.';
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionDoctorList($lang = 'en', $page = 1, $per_page = 10, $sort_by = "", $is_available = "", $category_id = null)
    {
        $request = Yii::$app->request->bodyParams;
        $symptom_id = isset($request['symptom_id']) ? $request['symptom_id'] : '';
        $clinic_id = isset($request['clinic_id']) ? $request['clinic_id'] : '';
        $gender = isset($request['gender']) ? $request['gender'] : '';
        $category_id = isset($request['category_id']) ? $request['category_id'] : '';
        $consultation_type = isset($request['consultation_type']) ? $request['consultation_type'] : '';
        $query = \app\models\Doctors::find()
            ->join('LEFT JOIN', 'doctor_categories', 'doctor_categories.doctor_id = doctors.doctor_id')
            ->join('LEFT JOIN', 'doctor_symptoms', 'doctor_symptoms.doctor_id = doctors.doctor_id')
            ->join('LEFT JOIN', 'category', 'doctor_categories.category_id = category.category_id')
            ->join('LEFT JOIN', 'symptoms', 'doctor_symptoms.symptom_id = symptoms.symptom_id');
        if (isset($clinic_id) && $clinic_id != null) {
            $query->andwhere(['doctors.clinic_id' => $clinic_id]);
        }
        if (isset($category_id) && $category_id != null) {
            $query->andwhere(['doctor_categories.category_id' => $category_id]);
        }
        if (isset($symptom_id) && !empty($symptom_id)) {
            $query->andWhere(['IN', 'doctor_symptoms.symptom_id', $symptom_id]);
        }
        if (isset($gender) && !empty($gender)) {
            $query->andwhere(['IN', 'doctors.gender', explode(",", $gender)]);
        }
        if ($consultation_type != '') {
            $query->andWhere(new \yii\db\Expression('FIND_IN_SET(:type, doctors.type)'))->addParams([':type' => $consultation_type]);
        }
        if ($is_available != '') {
            $date = date('Y-m-d');
            $day = date('l', strtotime($date));
            $time = date('H:i:s');
            $query->join('LEFT JOIN', 'doctor_working_days', 'doctors.doctor_id=doctor_working_days.doctor_id')
                ->andWhere(['doctor_working_days.day' => $day]);
            if ('is_available' == 1) {
                $query->andWhere(['>=', 'doctor_working_days.end_time', $time]);
            }
        }
        $query->andwhere(['doctors.is_active' => 1, 'doctors.is_deleted' => 0]);
        $query->groupBy('doctors.doctor_id');
        $countQuery = clone $query;
        $categoryValueQuery = clone $query;

        $symptomsValueQuery = clone $query;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $per_page,
        ]);

        $model = $query->limit($per_page)->offset(($page - 1) * $per_page)->all();

        if (isset($sort_by) && !empty($sort_by)) {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by == 1) {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by == 2) {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            }
        } else {
            $query->addOrderBy(['doctors.sort_order' => SORT_ASC, 'doctors.doctor_id' => SORT_DESC]);
        }

        $categoryValueQuery = clone $query;
        $symptomsValueQuery = clone $query;
        $model = $query->all();
        $result = [];
        $attributesValue = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $type = '';
                $types = explode(',', $row->type);
                if (count($types) > 1) {
                    $type = 'B';
                } else {
                    $type = $row->type;
                }
                //                $type = $row->type;
                $d['doctor_id'] = $row->doctor_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['clinic_id'] = (isset($row->clinic)) ?  $row->clinic->clinic_id  : '';
                $d['clinic'] = (isset($row->clinic)) ? ($lang == 'en') ? $row->clinic->name_en : $row->clinic->name_ar : '';
                $d['email'] = $row->email;
                $d['registration_number'] = ($row->registration_number != null) ? (string) $row->registration_number : "";
                $d['years_experience'] = $row->years_experience;
                $d['qualification'] = $row->qualification;
                $d['gender'] = $row->gender;
                $d['type'] = $type;
                $d['consultation_time_online'] = $row->consultation_time_online;
                $d['consultation_time_offline'] = $row->consultation_time_offline;
                $d['consultation_price_regular'] = $row->consultation_price_regular;
                $d['consultation_price_final'] = $row->consultation_price_final;
                $d['about_us'] = ($row->{'description_' . $lang} != null) ? $row->{'description_' . $lang} : '';
                $d['doctor_categories'] = $this::getCategoriesByIds($lang, $row->doctor_id, 'D');
                $d['doctor_insurance'] = $this::getInsuranceByIds($lang, $row->doctor_id, 'D');
                $d['doctor_symptoms'] = $this::getSymptomsByIds($lang, $row->doctor_id);
                $d['image'] = (!empty($row->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
            $categoryValueQuery->select([
                'DISTINCT(`doctor_categories`.`category_id`)',
                'category.name_en',
                'category.name_ar'
            ])->andFilterWhere(['IS NOT', 'doctor_categories.category_id', new \yii\db\Expression('NULL')]);

            //echo $categoryValueQuery->createCommand()->rawSql;die;
            $categories = $categoryValueQuery
                ->asArray()
                ->all();
            $tmp = [];
            foreach ($categories as $cat) {
                $c = [
                    'id'    => $cat['category_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }
            $categoriesCollected = [
                'filter_name' => ($lang == 'en') ? 'Categories' : 'الاقسام',
                'filter_type' => 'Categories',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $categoriesCollected);
            $symptomsValueQuery->select([
                'DISTINCT(`doctor_symptoms`.`symptom_id`)',
                'symptoms.name_en',
                'symptoms.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'doctor_symptoms.symptom_id', new \yii\db\Expression('NULL')]);
            $symptoms = $symptomsValueQuery
                // ->asArray()
                ->all();
            $tmp = [];
            foreach ($symptoms as $cat) {
                $c = [
                    'id'    => $cat['symptom_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }
            $symptomsCollected = [
                'filter_name' => ($lang == 'en') ? 'Symptoms' : 'أعراض',
                'filter_type' => 'Symptoms',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $symptomsCollected);
            $tmpgender = [];
            $gender = [
                [
                    'id'    => "W",
                    'value' => ($lang == 'en') ? "Women" : "النساء",
                ],
                [
                    'id'    => "M",
                    'value' => ($lang == 'en') ? "Men" : "رجال",
                ],
                [
                    'id'    => "U",
                    'value' => ($lang == 'en') ? "Unisex" : "للجنسين",
                ],
            ];
            array_push($tmpgender, $gender);
            $genderSelected = [
                'filter_name' => ($lang == 'en') ? 'Gender' : 'جنس',
                'filter_type' => 'Gender',
                'filter_values' => $gender
            ];
            array_push($attributesValue, $genderSelected);
        }
        $this->data = [
            'doctors'        => $result,
            'total_doctors'  => $countQuery->count(),
            'total_pages'    => $pages->pageCount,
            'filter'         => $attributesValue,
        ];
        return $this->response_array();
    }
    public function actionDoctorDetails($lang = 'en', $doctor_id = '')
    {
        $model = \app\models\Doctors::find()
            ->where(['is_active' => 1, 'is_deleted' => 0, 'doctor_id' => $doctor_id])
            ->one();
        if (!empty($model)) {
            $type = '';
            $types = explode(',', $model->type);
            if (count($types) > 1) {
                $type = 'B';
            } else {
                $type = $model->type;
            }
            $d['doctor_id'] = $model->doctor_id;
            $d['name'] = ($lang == 'en') ? $model->name_en : $model->name_ar;
            $d['clinic'] = (isset($model->clinic)) ? ($lang == 'en') ? $model->clinic->name_en : $model->clinic->name_ar : '';
            $d['email'] = $model->email;
            $d['registration_number'] = ($model->registration_number != null) ? (string) $model->registration_number : "";
            $d['years_experience'] = $model->years_experience;
            $d['qualification'] = $model->qualification;
            $d['gender'] = $model->gender;
            $d['type'] = $type;
            $d['consultation_time_online'] = $model->consultation_time_online;
            $d['consultation_time_offline'] = $model->consultation_time_offline;
            $d['consultation_price_regular'] = $model->consultation_price_regular;
            $d['consultation_price_final'] = $model->consultation_price_final;
            $d['about_us'] = ($model->{'description_' . $lang} != null) ? $model->{'description_' . $lang} : '';
            $d['doctor_categories'] = $this::getCategoriesByIds($lang, $model->doctor_id, 'D');
            $d['doctor_insurance'] = $this::getInsuranceByIds($lang, $model->doctor_id, 'D');
            $d['working_days'] = isset($model->doctorWorkingDays) ? $model->doctorWorkingDays : [];
            $d['image'] = (!empty($model->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');

            $this->data = $d;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == 'en') ? 'No details found' : 'لم يتم العثور على تفاصيل';
            $this->data = "";
            return $this->response_array();
        }
    }

    private function distance($lat1, $lat2, $lon1, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }




    public function actionClinicList($lang = 'en', $page = 1, $per_page = 10, $sort_by = "", $is_featured = "", $is_available = "", $type = "", $is_local = "", $latlon = "")
    {
        $request = Yii::$app->request->bodyParams;
        $category_id = isset($request['category_id']) ? $request['category_id'] : '';
        $governorate_id = isset($request['governorate_id']) ? $request['governorate_id'] : '';
        $latlong = explode(',', $latlon);
        // debugPrint($latlong);
        // die;
        if ($latlon != "") {
            $query = \app\models\Clinics::find()
                ->select(['clinics.*'])
                // ->select(['clinics.*'])
                ->join('LEFT JOIN', 'clinic_categories', 'clinic_categories.clinic_id = clinics.clinic_id')
                ->join('LEFT JOIN', 'category', 'clinic_categories.category_id = category.category_id')
                ->join('LEFT JOIN', 'state', 'state.state_id = clinics.governorate_id');
        } else {
            $query = \app\models\Clinics::find()
                ->join('LEFT JOIN', 'clinic_categories', 'clinic_categories.clinic_id = clinics.clinic_id')
                ->join('LEFT JOIN', 'category', 'clinic_categories.category_id = category.category_id')
                ->join('LEFT JOIN', 'state', 'state.state_id = clinics.governorate_id');
        }

        if ($category_id != '') {
            $query->andwhere(['clinic_categories.category_id' => $category_id]);
        }

        if ($type != "") {
            if ($type == "H" && $is_local == 1) {
                $query->join('LEFT JOIN', 'country', 'country.country_id = clinics.country_id');
                $query->andwhere(['clinics.type' => 'H']);
                $query->andwhere(['clinics.country_id' => 114]);
            } elseif ($type == "H" && $is_local == 0 && $is_local != "") {
                $query->join('LEFT JOIN', 'country', 'country.country_id = clinics.country_id');
                $query->andwhere(['clinics.type' => 'H']);
                $query->andwhere(['NOT IN', 'clinics.country_id', 114]);
            } else {
                $query->andwhere(['clinics.type' => $type]);
            }
        } elseif ($type == "") {
            $query->andwhere(['clinics.type' => 'C']);
        }
        $query->andwhere(['clinics.is_active' => 1, 'clinics.is_deleted' => 0]);
        $query->groupBy('clinics.clinic_id');
        //echo $query->createCommand()->rawSql;die;
        $countQuery = clone $query;

        $categoryValueQuery = clone $query;
        $governoateValueQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $per_page,
        ]);


        $model = $query->limit($per_page)->offset(($page - 1) * $per_page)->all();

        if (isset($sort_by) && !empty($sort_by)) {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by == 1) {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by == 2) {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            } elseif ($sort_by == 3) {
                $query->addOrderBy(["latlon" => SORT_DESC]);
            }
        }

        if ($latlon != "" && $sort_by == "") {
            $query->addOrderBy(["latlon" => SORT_DESC]);
        }

        if ($governorate_id != '') {
            $query->andwhere(['clinics.governorate_id' => $governorate_id]);
        }
        if ($is_featured == 1) {
            $query->andwhere(['clinics.is_featured' => $is_featured]);
        }

        if ($is_available != '') {
            $date = date('Y-m-d');
            $day  = date('l', strtotime($date));
            $time = date('H:i:s');
            $query->join('LEFT JOIN', 'clinic_working_days', 'clinics.clinic_id=clinic_working_days.clinic_id')
                ->andWhere(['clinic_working_days.day' => $day]);
            if ($is_available == 1) {
                $query->andWhere(['>=', 'clinic_working_days.end_time', $time]);
            } elseif ($is_available == 0) {
                $query->andWhere(['<=', 'clinic_working_days.end_time', $time]);
            }
        }

        $model = $query->all();
        $result = [];
        $attributesValue = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $total_doctors = Doctors::find()->where(['is_deleted' => 0, 'is_active' => 1, 'clinic_id' => $row->clinic_id])->count();
                $country_id = ($row->country_id != "") ? $row->area->state->country->country_id : $row->country_id;
                $clinic_is_local = 1;
                if ($row->type == 'H') {
                    $clinic_is_local = ($country_id == 114) ? 1 : 0;
                }
                $clinic_latlon = explode(',', $row->latlon);
                // debugPrint($clinic_latlon);
                // die;
                $d['clinic_id'] = $row->clinic_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['description'] = ($row->{'description_' . $lang} != "") ? $row->{'description_' . $lang} : "";
                $d['latlon'] = preg_replace('/\s+/', '', $row->latlon);
                // $d['distance'] = ($latlon != "" && $row->latlon != "") ? $row->distance : "0";
                // $d['distance'] = number_format($this->distance($clinic_latlon[0], $latlong[0], $clinic_latlon[1], $latlong[1], "K"), 2, ".", "");
                if ($row->latlon != null &&  $row->latlon != "") {
                    $d['distance'] = number_format($this->distance($clinic_latlon[0], $latlong[0], $clinic_latlon[1], $latlong[1], "K"), 2, ".", "");
                } else {
                    $d['distance'] = 0;
                }
                $d['type'] = $row->type;
                $d['email'] = $row->email;
                $d['country_id'] = $country_id;
                $d['governorate'] = (isset($row->governorate)) ? $row->governorate->name_en : '';
                $d['area'] = (isset($row->area)) ? $row->area->name_en : '';
                $d['block'] = $row->block;
                $d['street'] = $row->street;
                $d['building'] = $row->building;
                $d['total_doctors'] = $total_doctors;
                $d['is_local'] = $clinic_is_local;
                if ($lang == 'ar') {
                    $d['image'] = (!empty($row->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['image'] = (!empty($row->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }

                $d['clinic_categories'] = $this::getCategoriesByIds($lang, $row->clinic_id, 'C');
                $d['clinic_insurance'] = $this::getInsuranceByIds($lang, $row->clinic_id, 'C');
                if ($type == "H" && $is_local != "") {
                    if ($is_local == $clinic_is_local) {
                        array_push($result, $d);
                    }
                } else {
                    array_push($result, $d);
                }
            }

            $categoryValueQuery->select([
                'DISTINCT(`clinic_categories`.`category_id`)',
                'category.name_en',
                'category.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'clinic_categories.category_id', new \yii\db\Expression('NULL')]);
            $categories = $categoryValueQuery
                ->asArray()
                ->all();

            $tmp = [];
            foreach ($categories as $cat) {
                $c = [
                    'id'    => $cat['category_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }

            $categoriesCollected = [
                'filter_name' => ($lang == 'en') ? 'Categories' : 'الاقسام',
                'filter_type' => 'Categories',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $categoriesCollected);
            if ($is_local == 1) {
                $governoateValueQuery->select([
                    'DISTINCT(`state`.`state_id`)',
                    'state.name_en',
                    'state.name_ar'
                ])
                    ->andFilterWhere(['IS NOT', 'clinics.governorate_id', new \yii\db\Expression('NULL')]);
                $governorate = $governoateValueQuery
                    ->asArray()
                    ->all();

                $tmp = [];
                foreach ($governorate as $state) {
                    $c = [
                        'id' => $state['state_id'],
                        'value' => $state['name_' . $lang],
                    ];
                    array_push($tmp, $c);
                }

                $governorateCollected = [
                    'filter_name' => ($lang == 'en') ? 'Governorate' : 'محافظة',
                    'filter_type' => 'Governorate',
                    'filter_values' => $tmp
                ];
                array_push($attributesValue, $governorateCollected);
            } else {
                $countryModel = Country::find()->where(['is_active' => 1, 'is_deleted' => 0])->asArray()->all();
                $tmpCountry = [];
                foreach ($countryModel as $countryList) {
                    $countryData = [
                        'id' => $countryList['country_id'],
                        'value' => $countryList['name_' . $lang],
                    ];
                    array_push($tmpCountry, $countryData);
                }
                $CountryCollected = [
                    'filter_name' => ($lang == 'en') ? 'Country' : 'بلد',
                    'filter_type' => 'Country',
                    'filter_values' => $tmpCountry
                ];
                array_push($attributesValue, $CountryCollected);
            }
        }
        $total_result_count = count($result);
        $total_query_count  = $countQuery->count();
        $total_record_count = ($type == 'H' && $is_local != "") ? $total_result_count : $total_query_count;

        $this->data = [
            'clinics'        => $result,
            'result_clinic'  => count($result),
            'total_clinics'  => $countQuery->count(), //$total_record_count,
            'total_pages'    => $pages->pageCount,
            'filter'         => $attributesValue,
        ];
        return $this->response_array();
    }

    public function actionClinicDetails($lang = 'en', $clinic_id = '')
    {
        $model = \app\models\Clinics::find()
            ->where(['is_active' => 1, 'is_deleted' => 0, 'clinic_id' => $clinic_id])
            ->one();
        $address = [];
        if (!empty($model)) {
            $address = [
                'governorate' => (isset($model->governorate)) ? $model->governorate->name_en : '',
                'area' => (isset($model->area)) ? $model->area->name_en : '',
                'block' => $model->block,
                'street' => $model->street,
                'building' => $model->building,
                'latlon' => $model->latlon
            ];
            $d['clinic_id'] = $model->clinic_id;
            $d['name'] = ($lang == 'en') ? $model->name_en : $model->name_ar;
            $d['description'] = (string)($model->{'description_' . $lang} != "") ? $model->{'description_' . $lang} : "";
            $d['latlon'] = $model->latlon;
            $d['type'] = $model->type;
            $d['email'] = $model->email;
            $d['address'] = $address;
            if ($lang == 'ar') {
                $d['image'] = (!empty($model->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            } else {
                $d['image'] = (!empty($model->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }

            $d['clinic_categories'] = $this::getCategoriesByIds($lang, $model->clinic_id, 'C');
            $d['clinic_insurance'] = $this::getInsuranceByIds($lang, $model->clinic_id, 'C');
            $arr = [];
            if (isset($model->doctors)) {
                foreach ($model->doctors as $doc) {
                    if ($doc->is_deleted == 0 && $doc->is_active == 1) {
                        $type = '';
                        $types = explode(',', $doc->type);
                        if (count($types) > 1) {
                            $type = 'B';
                        } else {
                            $type = $doc->type;
                        }

                        $temp['doctor_id'] = $doc->doctor_id;
                        $temp['name_en'] = ($lang == 'en') ? $doc->name_en : $doc->name_ar;
                        $temp['email'] = $doc->email;
                        $temp['years_experience'] = $doc->years_experience;
                        $temp['qualification'] = $doc->qualification;
                        $temp['gender'] = $doc->gender;
                        $temp['type'] = $type;
                        $temp['gender'] = $doc->gender;
                        $temp['consultation_time_online'] = $doc->consultation_time_online;
                        $temp['consultation_time_offline'] = $doc->consultation_time_offline;
                        $temp['consultation_price_regular'] = $doc->consultation_price_regular;
                        $temp['consultation_price_final'] = $doc->consultation_price_final;
                        $temp['clinic'] = $doc->clinic->name_en;
                        $temp['image'] = ($doc->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $doc->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                        array_push($arr, $temp);
                    }
                }
            }
            $d['clinic_doctors'] = ""; //$arr;
            $this->data = $d;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No details found' : 'لم يتم العثور على تفاصيل';
            $this->data = "";
            return $this->response_array();
        }
    }

    public function actionLabList($lang = 'en', $page = 1, $per_page = 10, $sort_by = "", $is_available = "", $latlon = "")
    {
        $request = Yii::$app->request->bodyParams;
        $category_id = (isset($request['category_id'])) ? $request['category_id'] : '';
        $service_id = (isset($request['service_id'])) ? $request['service_id'] : '';
        $test_id = (isset($request['test_id'])) ? $request['test_id'] : '';
        $governorate_id = isset($request['governorate_id']) ? $request['governorate_id'] : '';

        $governorate_array =  $governorate_id != "" ? explode(',', $governorate_id) : [];

        $latlong = explode(',', $latlon);
        if ($latlon != '') {
            $query = \app\models\Labs::find()
                ->select(['labs.*', '111.045*haversine(' . $latlong[0] . ',' . $latlong[1] . ',substring_index(latlon,",",1),substring_index(substring_index(latlon,",",-1),",",1)) AS distance'])
                ->join('LEFT JOIN', 'lab_tests', 'lab_tests.lab_id=labs.lab_id')
                ->join('LEFT JOIN', 'test_categories', 'test_categories.test_id=lab_tests.test_id')
                ->join('LEFT JOIN', 'category', 'test_categories.category_id = category.category_id')
                ->join('LEFT JOIN', 'lab_services', 'lab_services.lab_id=labs.lab_id')
                ->join('LEFT JOIN', 'services', 'services.service_id=lab_services.service_id')
                ->join('LEFT JOIN', 'state', 'state.state_id = labs.governorate_id')
                ->andwhere(['labs.is_active' => 1, 'labs.is_deleted' => 0]);
        } else {
            $query = \app\models\Labs::find()
                ->join('LEFT JOIN', 'lab_tests', 'lab_tests.lab_id=labs.lab_id')
                ->join('LEFT JOIN', 'test_categories', 'test_categories.test_id=lab_tests.test_id')
                ->join('LEFT JOIN', 'category', 'test_categories.category_id = category.category_id')
                ->join('LEFT JOIN', 'lab_services', 'lab_services.lab_id=labs.lab_id')
                ->join('LEFT JOIN', 'services', 'services.service_id=lab_services.service_id')
                ->join('LEFT JOIN', 'state', 'state.state_id = labs.governorate_id')
                ->andwhere(['labs.is_active' => 1, 'labs.is_deleted' => 0]);
        }
        if ($category_id != '') {
            $query->andwhere(['test_categories.category_id' => $category_id]);
        }

        if ($service_id != '') {
            //            $query->join('LEFT JOIN', 'lab_services', 'lab_services.lab_id=labs.lab_id')
            $query->andwhere(['lab_services.service_id' => $service_id]);
        }

        if ($test_id != '') {
            $query->andwhere(['lab_tests.test_id' => $test_id]);
        }

        // if ($governorate_id != '') {
        //     $query->andwhere(['labs.governorate_id' => $governorate_id]);
        // }


        if (count($governorate_array) > 0) {
            $query->andwhere(['IN', 'labs.governorate_id', $governorate_array]);
        }


        $query->groupBy('labs.lab_id');

        $countQuery = clone $query;
        $testcategoryValueQuery = clone $query;
        $governoateValueQuery = clone $query;
        $serviceValueQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $per_page,
        ]);




        if (isset($sort_by) && !empty($sort_by)) {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by == 1) {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by == 2) {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            } elseif ($sort_by == 3) {
                $query->addOrderBy(["distance" => SORT_ASC]);
            }
        }

        if ($latlon != "" && $sort_by == "") {
            $query->addOrderBy(["distance" => SORT_ASC]);
        }

        if ($is_available != '') {
            $date = date('Y-m-d');
            $day = date('l', strtotime($date));
            $time = date('H:i:s');
            if ($is_available == 1) {
                $query->andWhere(['>=', 'labs.end_time', $time]);
            } elseif ($is_available == 0) {
                $query->andWhere(['<=', 'labs.end_time', $time]);
            }
        }
        //echo $query->createCommand()->rawSql;die;
        $model = $query->limit($per_page)->offset(($page - 1) * $per_page)->all();

        $model = $query->all();
        $result = [];
        $attributesValue = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $country_name = (isset($row->area)) ? $row->area->state->country->name_en : '';
                $lab_address = [
                    'country_name' => $country_name,
                    'governorate' => (isset($row->governorate)) ? $row->governorate->name_en : '',
                    'area' => (isset($row->area)) ? $row->area->name_en : '',
                    'block' => (isset($row->block)) ? $row->block : '',
                    'street' => (isset($row->street)) ? $row->street : '',
                    'building' => (isset($row->building)) ? $row->building : '',
                    'latlon' => (isset($row->latlon)) ? $row->latlon : '',
                ];
                $d['lab_id'] = $row->lab_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['email'] = $row->email;

                $d['latlon'] = ($row->latlon != "") ? preg_replace('/\s+/', '', $row->latlon) : "";
                $d['distance'] = ($latlon != "" && $row->latlon != "") ? $row->distance : "0";
                $d['home_test_charge'] = $row->home_test_charge;
                $d['consultation_time_interval'] = $row->consultation_time_interval;
                $d['max_booking_per_lot'] = $row->max_booking_per_lot;
                $d['start_time'] = $row->start_time;
                $d['end_time'] = $row->end_time;
                $d['lab_address'] = ($country_name != '') ? $lab_address : new stdClass();
                if ($lang == 'ar') {
                    $d['image'] = (!empty($row->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['image'] = (!empty($row->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }
                $d['lab_tests'] = $this::getTestsByIds($lang, $row->lab_id);
                $d['lab_services'] = $this::getServicesByIds($lang, $row->lab_id, 'L');
                $d['lab_insurance'] = $this::getInsuranceByIds($lang, $row->lab_id, 'L');
                array_push($result, $d);
            }


            $testcategoryValueQuery->select([
                'DISTINCT(`test_categories`.`category_id`)',
                'category.name_en',
                'category.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'test_categories.category_id', new \yii\db\Expression('NULL')]);
            $categories = $testcategoryValueQuery
                ->asArray()
                ->all();

            $tmp = [];
            foreach ($categories as $cat) {
                $c = [
                    'id'    => $cat['category_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }

            $categoriesCollected = [
                'filter_name' => ($lang == 'en') ? 'Test Categories' : 'فئات الاختبار',
                'filter_type' => 'Test Categories',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $categoriesCollected);

            $governoateValueQuery->select([
                'DISTINCT(`state`.`state_id`)',
                'state.name_en',
                'state.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'labs.governorate_id', new \yii\db\Expression('NULL')]);
            $governorate = $governoateValueQuery
                ->asArray()
                ->all();

            $tmp = [];
            foreach ($governorate as $state) {
                $c = [
                    'id'    => $state['state_id'],
                    'value' => $state['name_' . $lang],
                ];
                array_push($tmp, $c);
            }

            $governorateCollected = [
                'filter_name' => ($lang == 'en') ? 'Governorate' : 'محافظة',
                'filter_type' => 'Governorate',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $governorateCollected);

            $serviceValueQuery->select([
                'DISTINCT(`lab_services`.`service_id`)',
                'services.name_en',
                'services.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'lab_services.service_id', new \yii\db\Expression('NULL')]);
            $services = $serviceValueQuery
                ->asArray()
                ->all();

            $tmp = [];
            foreach ($services as $cat) {
                $c = [
                    'id'    => $cat['service_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }

            $servicesCollected = [
                'filter_name' => ($lang == 'en') ? 'Services' : 'فئات الاختبار',
                'filter_type' => 'Services',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $servicesCollected);
        }
        $this->data = [
            'labs'           => $result,
            'total_labs' => $countQuery->count(),
            'total_pages'    => $pages->pageCount,
            'filter'         => $attributesValue,
        ];
        return $this->response_array();
    }

    public function actionLabDetails($lang = 'en', $lab_id = '')
    {
        $model = \app\models\Labs::find()
            ->where(['is_active' => 1, 'is_deleted' => 0, 'lab_id' => $lab_id])
            ->one();
        if (!empty($model)) {
            $d['lab_id'] = $model->lab_id;
            $d['name'] = ($lang == 'en') ? $model->name_en : $model->name_ar;
            $d['email'] = $model->email;
            $d['home_test_charge'] = $model->home_test_charge;
            $d['consultation_time_interval'] = $model->consultation_time_interval;
            $d['max_booking_per_lot'] = $model->max_booking_per_lot;
            $d['start_time'] = $model->start_time;
            $d['end_time'] = $model->end_time;

            $country_name = (isset($model->area)) ? $model->area->state->country->name_en : '';
            $lab_address = [
                'country_name' => $country_name,
                'governorate' => (isset($model->governorate)) ? $model->governorate->name_en : '',
                'area' => (isset($model->area)) ? $model->area->name_en : '',
                'block' => (isset($model->block)) ? $model->block : '',
                'street' => (isset($model->street)) ? $model->street : '',
                'building' => (isset($model->building)) ? $model->building : '',
                'latlon' => (isset($model->latlon)) ? $model->latlon : '',
            ];
            $d['lab_address'] = ($country_name != '') ? $lab_address : new stdClass();
            if ($lang == 'ar') {
                $d['image'] = (!empty($model->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            } else {
                $d['image'] = (!empty($model->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }
            $d['lab_tests'] = $this::getTestsByIds($lang, $model->lab_id);
            $d['lab_services'] = $this::getServicesByIds($lang, $model->lab_id, 'L');
            $d['lab_insurance'] = $this::getInsuranceByIds($lang, $model->lab_id, 'L');
            $this->data = $d;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? "No Details Found" : "لم يتم العثور على تفاصيل";
            $this->data = "";
            return $this->response_array();
        }
    }

    private function getCategoriesByIds($lang = 'en', $id, $type)
    {
        if ($type == 'D') {
            $model = \app\models\DoctorCategories::find()
                ->where(['doctor_id' => $id])
                ->all();
            $result = [];
            foreach ($model as $row) {
                $d['category_id'] = $row->category_id;
                $d['name'] = ($lang == 'en') ? $row->category->name_en : $row->category->name_ar;
                array_push($result, $d);
            }
        } elseif ($type == 'C') {
            $model = \app\models\ClinicCategories::find()
                ->where(['clinic_id' => $id])
                ->all();
            $result = [];
            foreach ($model as $row) {
                $d['category_id'] = $row->category_id;
                $d['name'] = ($lang == 'en') ? $row->category->name_en : $row->category->name_ar;
                array_push($result, $d);
            }
        }
        return $result;
    }

    private function getServicesByIds($lang = 'en', $id)
    {
        $model = \app\models\LabServices::find()
            ->where(['lab_id' => $id])
            ->all();
        $result = [];
        foreach ($model as $row) {
            $d['service_id'] = $row->service_id;
            $d['name'] = ($lang == 'en') ? $row->service->name_en : $row->service->name_ar;
            if ($lang == 'ar') {
                $d['image'] = (!empty($row->service->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->service->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            } else {
                $d['image'] = (!empty($row->service->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->service->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
            }
            array_push($result, $d);
        }
        return $result;
    }

    private function getInsuranceByIds($lang = 'en', $id, $type)
    {
        if ($type == 'D') {
            $model = \app\models\DoctorInsurances::find()
                ->where(['doctor_id' => $id])
                ->all();
            $result = [];
            foreach ($model as $row) {
                $d['insurance_id'] = $row->insurance_id;
                $d['name'] = ($lang == 'en') ? $row->insurance->name_en : $row->insurance->name_ar;
                $d['image'] = (!empty($row->insurance->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->insurance->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
        } elseif ($type == 'C') {
            $model = \app\models\ClinicInsurances::find()
                ->where(['clinic_id' => $id])
                ->all();
            $result = [];
            foreach ($model as $row) {
                $d['insurance_id'] = $row->insurance_id;
                $d['name'] = ($lang == 'en') ? $row->insurance->name_en : $row->insurance->name_ar;
                $d['image'] = (!empty($row->insurance->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->insurance->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
        } elseif ($type == 'L') {
            $model = \app\models\LabInsurances::find()
                ->where(['lab_id' => $id])
                ->all();
            $result = [];
            foreach ($model as $row) {
                $d['insurance_id'] = $row->insurance_id;
                $d['name'] = ($lang == 'en') ? $row->insurance->name_en : $row->insurance->name_ar;
                $d['image'] = (!empty($row->insurance->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->insurance->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
        }
        return $result;
    }

    private function getSymptomsByIds($lang = 'en', $id)
    {
        $model = \app\models\DoctorSymptoms::find()
            ->where(['doctor_id' => $id])
            ->all();
        $result = [];
        foreach ($model as $row) {
            $d['symptom_id'] = $row->symptom_id;
            $d['name'] = ($lang == 'en') ? $row->symptoms->name_en : $row->symptoms->name_ar;
            array_push($result, $d);
        }

        return $result;
    }

    private function getTestsByIds($lang = 'en', $id)
    {
        $model = \app\models\Category::find()
            ->select(['category.category_id', 'category.name_en', 'category.name_ar', 'tests.test_id', 'tests.name_en as test_name_en', 'tests.name_ar as test_name_ar', 'tests.price as test_price', 'tests.is_home_service'])
            ->join('LEFT JOIN', 'test_categories', 'category.category_id=test_categories.category_id')
            ->join('LEFT JOIN', 'tests', 'tests.test_id=test_categories.test_id')
            ->join('LEFT JOIN', 'lab_tests', 'lab_tests.test_id=tests.test_id')
            ->where(['category.type' => 'T', 'tests.is_active' => 1, 'tests.is_deleted' => 0, 'lab_tests.lab_id' => $id])
            ->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $model_lab = \app\models\LabTests::find()
                    ->join('LEFT JOIN', 'tests', 'tests.test_id=lab_tests.test_id')
                    ->join('LEFT JOIN', 'test_categories', 'lab_tests.test_id=test_categories.test_id')
                    ->where(['lab_tests.lab_id' => $id, 'tests.is_active' => 1, 'tests.is_deleted' => 0, 'test_categories.category_id' => $row->category_id])
                    ->all();
                $test_result = [];
                if (!empty($model_lab)) {
                    foreach ($model_lab as $row1) {
                        $test['test_id'] = $row1->test_id;
                        $test['name'] = ($lang == 'en') ? $row1->test->name_en : $row1->test->name_ar;
                        $test['price'] = $row1->test->price;
                        $test['is_home_service'] = $row1->test->is_home_service;
                        array_push($test_result, $test);
                    }
                }

                $d['category_id']   = $row->category_id;
                $d['category_name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['tests']         = $test_result;
                array_push($result, $d);
            }
        }
        return $result;
        /*$model = \app\models\LabTests::find()
                ->where(['lab_id' => $id])
                ->all();
        $result = [];
        foreach ($model as $row) {
            $d['test_id'] = $row->test_id;
            $d['name'] = ($lang == 'en') ? $row->test->name_en : $row->test->name_ar;
            $d['price'] = $row->test->price;
            $d['is_home_service'] = $row->test->is_home_service;
            array_push($result, $d);
        }
        return $d;*/
    }

    public function actionFaqList($lang = 'en')
    {
        $model = \app\models\Faq::find()->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $d['faq_id'] = $row->faq_id;
                $d['question'] = ($lang == 'en') ? $row->question_en : $row->question_ar;
                $d['answer'] = ($lang == 'en') ? $row->answer_en : $row->answer_ar;
                array_push($result, $d);
            }
        }
        $this->data = $result;
        return $this->response();
    }

    public function actionFaqDetails($lang = 'en', $faq_id = "")
    {
        $model = \app\models\Faq::find()->where(['faq_id' => $faq_id])->one();
        if (!empty($model)) {
            $d['faq_id'] = $model->faq_id;
            $d['question'] = ($lang == 'en') ? $model->question_en : $model->question_ar;
            $d['answer'] = ($lang == 'en') ? $model->answer_en : $model->answer_ar;
            $this->data = $d;
            return $this->response();
        } else {
            $this->response_code = 406;
            $this->message = ($lang == "en") ? "No Details Found" : "لم يتم العثور على تفاصيل";
            $this->data = "";
            return $this->response();
        }
    }

    public function actionGetSpecialities($lang = 'en', $type = null)
    {
        $this->data = [];
        $this->message = ($lang == "en") ? "Success" : "نجاح";
        $model = Category::find()->where(['is_deleted' => 0, 'type' => $type, 'is_active' => 1])
            ->all();
        if (!empty($model)) {
            foreach ($model as $row) {
                if ($lang == 'ar') {
                    $icon = ($row->icon_ar != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $row->icon_ar) : '';
                } else {
                    $icon = ($row->icon != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $row->icon) : '';
                }
                $d['id'] = $row->category_id;
                $d['name'] = $row->{'name_' . $lang};
                $d['icon'] = $icon;
                $d['hide_category_in_app'] = $row->hide_category_in_app;
                $d['show_in_home'] = $row->show_in_home;
                array_push($this->data, $d);
            }
        }
        return $this->response();
    }

    public function actionDoctorTimeslot($doctor_id, $date, $consultation_type, $lang = "en")
    {
        $day = date('l', strtotime($date));
        $model = Doctors::find()
            ->where(['doctor_id' => $doctor_id, 'is_active' => 1, 'is_deleted' => 0])
            ->one();
        if (!empty($model)) {
            $duration = ($consultation_type == 'I') ? $model->consultation_time_offline : $model->consultation_time_online;
                $doctorWorkingDay = \app\models\DoctorWorkingDays::find()
                    ->where(['doctor_id' => $model->doctor_id, 'day' => $day])
                    ->one();
                    
            if (!empty($doctorWorkingDay)) {
                $startTime = strtotime($date . ' ' . $doctorWorkingDay->start_time);
                $endTime = strtotime($date . ' ' . $doctorWorkingDay->end_time);
                $timeslot = [];
                $interval = $duration * 60;
                $requestDate = date('Y-m-d', strtotime($date));
                for ($i = $startTime; $i < $endTime; $i += $interval) {
                    $time = date('H:i:s', $i);
                    $slotDate = date('Y-m-d', $i);
                    $isBooked = AppointmentHelper::isBooked($time, $doctor_id, $slotDate, $duration);
                    $t['time'] = $time;
                    $t['is_booked'] = $isBooked['found'];
                    $t['regular_price'] = $model->consultation_price_regular;
                    $t['final_price'] = $model->consultation_price_final;
                    if (strtotime($slotDate) <= strtotime($requestDate)) {
                        array_push($timeslot, $t);
                    }
                }
                $this->data = [
                    'id' => $doctorWorkingDay->doctor_working_day_id,
                    'slot_day' => $doctorWorkingDay->day,
                    'slot_date' => $date,
                    'duration' => $duration,
                    'timeslots' => $timeslot,
                ];
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? 'Timeslot does not exist' : "الفترة الزمنية غير موجودة";
                $this->data = new stdClass();
            }
        } else {
            $this->response_code = 404;
            $this->message = ($lang == "en") ?  'Doctor does not exist' : "دكتور غير موجود";
            $this->data = new stdClass();
        }
        return $this->response();
    }
    public function actionSetTranslatorForAppointment($lang = "en")
    {
        $request = \Yii::$app->request->bodyParams;
        // return json_encode($request);
        $settings_model = Settings::find()->one();
        $translator_price =  $request['translator_required']  == "1" ?  $settings_model->translator_price : 0;
        $model =  DoctorAppointments::find()
            ->where(['doctor_appointment_id' => $request['appointment_id']])
            ->one();
        $model->need_translator = $request['translator_required'];
        $model->amount = $model->sub_total + $translator_price - $model->discount_price;
        $model->save();

        $doctor = Doctors::find()
            ->where(['doctor_id' =>  $model->doctor_id])
            ->one();
        $accepted_payment_method = $doctor->accepted_payment_method;
        $paymentTypes = AppHelper::paymentTypes($lang, $accepted_payment_method);
        $totalAmount = $model->amount;
        $this->data =
            [
                "doctor_appointment_id" =>    $model->doctor_appointment_id,
                "need_translator" =>   $model->need_translator,
                "appointment_amount" =>  $model->sub_total,
                "translator_amount" =>  Yii::$app->formatter->asDecimal($translator_price, 2),
                "total_price" =>   Yii::$app->formatter->asDecimal($totalAmount, 2),
                "payment_methods" =>  $paymentTypes,
            ];
        return $this->response();
    }
    public function actionAddDoctorAppointment($lang = "en")
    {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $doctor = Doctors::find()
                ->where(['doctor_id' => $request['doctor_id'], 'is_active' => 1, 'is_deleted' => 0])
                ->one();
            if (empty($doctor)) {
                $this->response_code = 404;
                $this->message =   ($lang == "en") ? 'Doctor does not exist' : "دكتور غير موجود";
                return $this->response();
            }
            $duration = ($request['consultation_type'] == 'I') ? $doctor->consultation_time_offline : $doctor->consultation_time_online;

            $isBooked = AppointmentHelper::isBooked($request['appointment_time'], $request['doctor_id'], $request['appointment_date'], $duration);

            if ($isBooked['found'] == '1') {
                $this->response_code = 408;
                $this->message =  ($lang == "en") ? 'Requested slot is already booked' : "الفترة الزمنية المطلوبة محجوزة بالفعل";
                return $this->response();
            }

            $day = date('l', strtotime($request['appointment_date']));
            $requestBookingStartDateTime = $request['appointment_date'] . ' ' . $request['appointment_time'];
            $requestDate = new \DateTime($requestBookingStartDateTime);
            $today = new \DateTime(date("Y-m-d H:i:s"));
            $today->setTimezone(new \DateTimeZone('Asia/Kuwait'));
            $currentDateTime = new \DateTime($today->format('Y-m-d H:i:s'));
            if ($requestDate < $currentDateTime) {
                $this->response_code = 405;
                $this->message =  ($lang == "en") ?  'Requested datetime is invalid' : 'التاريخ والوقت المطلوب غير صالح';
                return $this->response();
            }
            $doctorSlot = \app\models\DoctorWorkingDays::find()
                ->where(['doctor_id' => $request['doctor_id'], 'day' => $day])
                ->one();
            if (!empty($doctorSlot)) {
                $slotAvailable = 0;
                $startTime = strtotime($request['appointment_date'] . ' ' . $doctorSlot->start_time);
                $endTime = strtotime($request['appointment_date'] . ' ' . $doctorSlot->end_time);
                $timeslot = [];
                $interval = $duration * 60;
                //making timeslot using min interval
                for ($i = $startTime; $i <= $endTime; $i += $interval) {
                    $time = date('H:i:s', $i);
                    $dt = date('Y-m-d', $i);
                    if (strtotime($dt) <= strtotime($request['appointment_date'])) {
                        array_push($timeslot, $dt . ' ' . $time);
                    }
                }
                if (!empty($timeslot)) {
                    //added min to start time to find out end time
                    $dateTime = new \DateTime($requestBookingStartDateTime);
                    $dateTime->modify('+' . $duration . ' minutes');
                    $requestBookingEndDateTime = $dateTime->format('Y-m-d H:i:s');
                    //change datetime format
                    $time1 = new \DateTime($requestBookingStartDateTime);
                    $time2 = new \DateTime($requestBookingEndDateTime);
                    //calculate min time n max time to compare request time is valid or not
                    $first = new \DateTime($timeslot[0]);
                    $lastTime = new \DateTime(end($timeslot));
                    //added min to last time to find out maxlast time
                    $lastTime->modify('+' . $duration . ' minutes');
                    $lastDateTime = $lastTime->format('Y-m-d H:i:s');
                    //change datetime format
                    $last = new \DateTime($lastDateTime);
                    //if request time >= first time slot and less then last time slot
                    //also booking end time is less then or equal to max last time
                    //then request slot is valid
                    if (($time1 >= $first && $time1 <= $last) && $time2 <= $last) {
                        $slotAvailable = 1;
                    }
                    if ($slotAvailable == 0) {
                        $this->response_code = 404;
                        $this->message = ($lang == "en") ? 'Requested timeslot does not exist' : "المهلة الزمنية المطلوبة غير موجودة";
                        return $this->response();
                    }
                } else {
                    $this->response_code = 407;
                    $this->message = ($lang == "en") ? 'Timeslot creation failed due to unexpected error' : 'فشل إنشاء المهلة الزمنية بسبب خطأ غير متوقع';
                    return $this->response();
                }
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? 'No available timeslot for appointment' : 'لا يوجد جدول زمني متاح للتعيين';
                return $this->response();
            }
            //checking duplicate
            $exist = 1;
            $requestDatetime = $request['appointment_date'] . ' ' . $request['appointment_time'];
            //
            $booking = \app\models\DoctorAppointments::find()
                ->where(['appointment_datetime' => $requestDatetime])
                ->andWhere(['doctor_id' => $request['doctor_id']])
                ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($booking)) {
                $createDate = new \DateTime($booking->created_at, new \DateTimeZone(date_default_timezone_get()));
                $createDate->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                $createTime = new \DateTime($createDate->format('Y-m-d H:i:s'));
                //
                $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
                $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                $timeFromCreate = $createTime->diff(new \DateTime($currentTime->format('Y-m-d H:i:s')));
                $minutes = $timeFromCreate->days * 24 * 60;
                $minutes += $timeFromCreate->h * 60;
                $minutes += $timeFromCreate->i;
                if ($minutes > 10) {
                    $exist = 0;
                } elseif ($minutes < 10) {
                    $exist = 1;
                }
            } else {
                $exist = 0;
            }
            $accepted_payment_method = $doctor->accepted_payment_method;
            $paymentTypes = AppHelper::paymentTypes($lang, $accepted_payment_method);
            //print_r($paymentTypes);die;
            //create new
            if ($exist == 0) {
                $settings = \app\models\Settings::find()
                    ->where(['setting_id' => 1])
                    ->asArray()
                    ->one();
                $translator_price = 0;
                if (!empty($request['need_translator'])) {
                    $translator_price = $settings['translator_price'];
                }

                $appointmentPrice = $doctor->consultation_price_final;
                $model = new \app\models\DoctorAppointments();
                $model->user_id = $request['user_id'];
                $model->name = $request['name'];
                $model->email = $request['email'];
                $model->phone_number = $request['phone_number'];
                $model->consultation_type = $request['consultation_type'];
                $model->consultation_fees = $appointmentPrice;
                $model->appointment_datetime = $requestDatetime;
                $model->doctor_id = $doctor->doctor_id;
                $model->kid_id = !empty($request['kid_id']) ? $request['kid_id'] : '';
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->is_cancelled = 0;
                $model->is_paid = 0;
                $model->duration = $duration;
                $discount = 0;
                $discount_price = 0;
                $model->discount = $discount;
                $model->discount_price = $discount_price;
                $model->sub_total = $appointmentPrice;
                $model->amount = $appointmentPrice - $discount_price + $translator_price;
                $model->need_translator = !empty($request['need_translator']) ? $request['need_translator'] : "";
                $model->admin_commission = (!empty($doctor->clinic)) ? $doctor->clinic->admin_commission : 0;

                if ($model->isNewRecord) {
                    $model->appointment_number = \app\helpers\AppHelper::getNextBookingNumber('doctor');
                }
                if ($model->save(false)) {
                    $kids = \app\models\Kids::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0])
                        ->all();
                    $kidsList = [];
                    if (!empty($kids)) {
                        foreach ($kids as $kid) {
                            $age = '';
                            if ($kid->dob != null) {
                                $dateOfBirth = $kid->dob;
                                $today = date("Y-m-d");
                                $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                $age = $diff->format('%y');
                            }
                            $k = [
                                'id' => $kid->kid_id,
                                'name' => $kid->{"name_" . $lang},
                                'civil_id' => $kid->civil_id,
                                'dob' => $kid->dob,
                                'age' => $age,
                                'gender' => $kid->gender,
                                'blood_group' => $kid->blood_group,
                                'relation' => $kid->relation,
                            ];
                            array_push($kidsList, $k);
                        }
                    }
                    $this->message = ($lang == "en") ? 'Appointment successfully saved.' : 'تم حفظ الموعد بنجاح.';
                    $doctor_image = (isset($model->doctor) && !empty($model->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');

                    $this->data = [
                        'appointment_details' => [
                            'id' => $model->doctor_appointment_id,
                            'appointment_number' => $model->appointment_number,
                            'name' => $model->name,
                            'email' => $model->email,
                            'consultation_type' => $model->consultation_type,
                            'consultation_fees' => $model->consultation_fees,
                            'appointment_datetime' => $model->appointment_datetime,
                            'duration' => $model->duration,
                            'user_id' => $model->user_id,
                            'doctor_id' => $model->doctor_id,
                            'doctor_name' => $model->doctor->{"name_" . $lang},
                            'doctor_image' => $doctor_image,
                            'admin_commission' => (string) $model->admin_commission,
                            'doctor_categories' => $this->getCategoriesByIds($lang, $model->doctor_id, 'D'),
                            'created_at' => $model->created_at,
                            'updated_at' => $model->updated_at,
                            'kid_id' => $model->kid_id,
                            'is_cancelled' => $model->is_cancelled,
                            'is_paid' => $model->is_paid,
                            // 'discount' => $model->discount_price,
                            // 'sub_total' => $model->sub_total,
                            'amount' => $model->amount,
                            // 'translator_price' => $translator_price,
                        ],
                        'kids_list' => $kidsList,
                        'payment_types' => $paymentTypes,
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 406;
                $this->message = ($lang == "en") ? 'Appointment is not available for this timeslot' : 'الموعد غير متاح لهذه الفترة الزمنية';
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }
    public function actionDoctorAppointmentPayment($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;

        $settings = \app\models\Settings::find()
            ->where(['setting_id' => 1])
            // ->asArray()
            ->one();

        if (!empty($request)) {
            $model = \app\models\DoctorAppointments::find()
                ->where(['user_id' => $request['user_id'], 'doctor_appointment_id' => $request['doctor_appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($model)) {
                $src = 'src_card';
                $paymode = 'CC';
                if ($request['paymode'] == 'K') {
                    $src = 'src_kw.knet';
                    $paymode = 'K';
                } elseif ($request['paymode'] == 'W') {
                    $src = '';
                    $paymode = 'W';
                    $model->is_paid = '1';
                }
                $model->payment_initiate_time = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->has_gone_payment = 1;
                $model->kid_id = !empty($request['kid_id']) ? $request['kid_id'] : "";
                $model->need_translator = !empty($request['need_translator']) ? $request['need_translator'] : "";
                $model->save(false);
                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $promoFor = $model->promo_for;
                $promoId = $model->promotion_id;
                $discount = $model->discount;
                $discountPrice = $model->discount_price;
                $subTotal = 0;
                // $discountPrice = 0;
                if (isset($model->sub_total)) {
                    $subTotal = $model->sub_total; // Working
                    if (isset($promoFor) && !empty($promoFor)) {
                        if ($promoFor == 'D') {
                            // if (empty($promotion->promotionDoctors)) {
                            //     $discountPrice += ($discount / 100) * $subTotal;
                            //     $discountPrice = $subTotal - $discount;
                            // } else {
                            //     $hasPromoDoctor = 1;
                            //     $promoDoctors = \app\models\PromotionDoctors::find()
                            //         ->where(['promotion_id' => $promoId, 'doctor_id' => $model->doctor_id])
                            //         ->one();
                            //     if (!empty($promoDoctors)) {
                            //         // $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            //         $discountPrice += ($discount / 100) * $subTotal;
                            //         $hasPromotionFor[] = $model->doctor_id;
                            //     } else {
                            //         // $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            //         $discountPrice += ($discount / 100) * $subTotal;
                            //     }
                            // }
                        }
                    } else {
                        // $discountPrice = 0;
                    }
                }


                // if ($request['need_translator'] == 1) {
                //     $amount = $model->sub_total + $settings->translator_price;
                // } else {
                //     $amount = $model->sub_total;
                // }
                // $amount = $amount - $discountPrice;
                // $model->amount = $amount;
                // $model->discount_price = $discountPrice;
                // $model->save(false);

                $transactionNumber = uniqid('DA-');
                //$paymentResponse = \app\helpers\PaymentHelper::tapPayment('DA', $model->doctor_appointment_id, $model->user_id, $model->amount, $transactionNumber, '', $lang, $src, $paymode);
                if ($request['paymode'] == 'C' || $request['paymode'] == 'W') {
                    $src = '';
                    $paymode = $request['paymode'];
                    $model->is_paid = '1';
                    $model->save(false);
                    $paymentModel = \app\models\Payment::find()
                        ->where(['type_id' => $model->doctor_appointment_id, 'type' => 'DA'])
                        ->one();
                    if (empty($paymentModel)) {
                        $paymentModel = new \app\models\Payment();
                    }
                    $trackID = date("YmdHis") . time() . rand();
                    $paymentModel->transaction_id = $transactionNumber;
                    $paymentModel->type_id = $model->doctor_appointment_id;
                    $paymentModel->type = 'DA';
                    $paymentModel->paymode = $request['paymode'];
                    $paymentModel->gross_amount = $model->amount;
                    $paymentModel->net_amount = $model->amount;
                    $paymentModel->TrackID = $trackID;
                    $paymentModel->currency_code = "KWD";
                    $paymentModel->result = 'CAPTURED';
                    $paymentModel->payment_date = date("Y-m-d H:i:s");
                    $paymentModel->save(false);
                    $paymentResponse = [
                        'status' => 200,
                        'url' => "",
                        'success' => "",
                        'error' => "",
                        'gateway_response' => ""
                    ];
                    Yii::$app->mailer->compose('@app/mail/doctor-appointment', [
                        'model' => $model,
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->user->email)
                        ->setSubject('Doctor Appointment Booked ')
                        ->send();
                } elseif ($request['paymode'] == 'K' || $request['paymode'] == 'CC') {
                    $myfatorah = \app\helpers\PaymentHelper::payThroughMyfatoorahExecutePayment('DA', $model->doctor_appointment_id, $model->user_id, $model->amount, '', $lang, $paymode, 'KWD', $transactionNumber, '');
                    $paymentResponse = [
                        'status' => 200,
                        'url' => $myfatorah['payment_url'],
                        'success' => $myfatorah['success_url'],
                        'error' => $myfatorah['error_url'],
                        'gateway_response' => $myfatorah['gateway_response']
                    ];
                }
                $this->message = ($lang == "en") ?  'Payment information updated.' : 'تم تحديث معلومات الدفع';
                $doctor_image = (isset($model->doctor) && !empty($model->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');

                $kids = \app\models\Kids::find()
                    ->where(['kid_id' => $model->kid_id, 'is_deleted' => 0])
                    ->one();
                $kid_data = new stdClass();
                if (!empty($kids)) {
                    $age = '';
                    if ($kids->dob != null) {
                        $dateOfBirth = $kids->dob;
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age = $diff->format('%y');
                    }
                    $kid_data = [
                        'id' => $kids->kid_id,
                        'name' => $kids->{"name_" . $lang},
                        'civil_id' => $kids->civil_id,
                        'dob' => $kids->dob,
                        'age' => $age,
                        'gender' => $kids->gender,
                        'blood_group' => $kids->blood_group,
                        'relation' => $kids->relation,
                    ];
                }
                $this->data = [
                    'appointment_details' => [
                        'id' => $model->doctor_appointment_id,
                        'appointment_number' => $model->appointment_number,
                        'name' => $model->name,
                        'email' => $model->email,
                        'consultation_type' => $model->consultation_type,
                        'consultation_fees' => $model->consultation_fees,
                        'appointment_datetime' => $model->appointment_datetime,
                        'duration' => $model->duration,
                        'user_id' => $model->user_id,
                        'doctor_id' => $model->doctor_id,
                        'doctor_name' => $model->doctor->{"name_" . $lang},
                        'doctor_image' => $doctor_image,
                        'admin_commission' => (string)$model->admin_commission,
                        'doctor_categories' => $this->getCategoriesByIds($lang, $model->doctor_id, 'D'),
                        'created_at' => $model->created_at,
                        'updated_at' => $model->updated_at,
                        'kid_id' => !empty($model->kid_id) ? $model->kid_id : '',
                        'kid_details' => $kid_data,
                        'is_cancelled' => $model->is_cancelled,
                        'is_paid' => $model->is_paid,
                        'discount_price' => $model->discount_price,
                        'discount' => $model->discount,
                        'sub_total' => $model->sub_total,
                        // 'amount' => $model->amount,
                        'need_translator' =>   $model->need_translator,
                        'translator_price' => $model->need_translator == "1" ? $settings->translator_price : 0,
                        'total_amount' =>  $model->amount,
                        'paymode' => $paymode,
                    ],
                    'payment_url' => (!empty($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['url'] : '',
                    'success_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['success'] : "",
                    'error_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['error'] : "",
                    'paymode' => $paymode, //($paymode == 'W') ? 'W' : '',
                ];
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? "Appointment doesn't exist." : "الموعد غير موجود";
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }
    public function actionDoctorAppointmentPaymentV1($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\DoctorAppointments::find()
                ->where(['user_id' => $request['user_id'], 'doctor_appointment_id' => $request['doctor_appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($model)) {
                $src = 'src_card';
                $paymode = 'CC';
                if ($request['paymode'] == 'K') {
                    $src = 'src_kw.knet';
                    $paymode = 'K';
                } elseif ($request['paymode'] == 'W') {
                    $src = '';
                    $paymode = 'W';
                    $model->is_paid = '1';
                }
                $settings = \app\models\Settings::find()
                    ->where(['setting_id' => 1])
                    ->asArray()
                    ->one();
                $translator_price = 0;
                if (!empty($request['need_translator'])) {
                    $translator_price = $settings->translator_price;
                }

                $model->payment_initiate_time = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->has_gone_payment = 1;
                $model->kid_id = !empty($request['kid_id']) ? $request['kid_id'] : "";
                $model->need_translator = !empty($request['need_translator']) ? $request['need_translator'] : "";
                $model->save(false);

                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $promoFor = $model->promo_for;
                $promoId = $model->promotion_id;
                $discount = $model->discount;
                $subTotal = 0;
                $discountPrice = 0;
                if (isset($model->sub_total)) {
                    $subTotal = $model->sub_total;
                    if (isset($promoFor) && !empty($promoFor)) {
                        if ($promoFor == 'D') {
                            if (empty($promotion->promotionDoctors)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromoDoctor = 1;
                                $promoDoctors = \app\models\PromotionDoctors::find()
                                    ->where(['promotion_id' => $promoId, 'doctor_id' => $model->doctor_id])
                                    ->one();
                                if (!empty($promoDoctors)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $hasPromotionFor[] = $model->doctor_id;
                                } else {
                                    //$discountPrice = 0;
                                }
                            }
                        } elseif ($promoFor == 'L') {
                            //here for lab
                        }
                    }
                }
                //echo $discountPrice;die;
                $total = ($model->sub_total - $discountPrice + $translator_price);
                $model->amount = $total;
                $model->discount_price = $discountPrice;
                $model->save(false);

                $transactionNumber = uniqid('DA-');
                $paymentResponse = \app\helpers\PaymentHelper::tapPayment('DA', $model->doctor_appointment_id, $model->user_id, $model->amount, $transactionNumber, '', $lang, $src, $paymode);
                $this->message = ($lang == "en") ?  'Payment information updated.' : 'تم تحديث معلومات الدفع';
                $doctor_image = (isset($model->doctor) && !empty($model->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');

                $kids = \app\models\Kids::find()
                    ->where(['kid_id' => $model->kid_id, 'is_deleted' => 0])
                    ->one();
                $kid_data = new stdClass();
                if (!empty($kids)) {
                    $age = '';
                    if ($kids->dob != null) {
                        $dateOfBirth = $kids->dob;
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age = $diff->format('%y');
                    }
                    $kid_data = [
                        'id' => $kids->kid_id,
                        'name' => $kids->{"name_" . $lang},
                        'civil_id' => $kids->civil_id,
                        'dob' => $kids->dob,
                        'age' => $age,
                        'gender' => $kids->gender,
                        'blood_group' => $kids->blood_group,
                        'relation' => $kids->relation,
                    ];
                }


                $this->data = [
                    'appointment_details' => [
                        'id' => $model->doctor_appointment_id,
                        'appointment_number' => $model->appointment_number,
                        'name' => $model->name,
                        'email' => $model->email,
                        'consultation_type' => $model->consultation_type,
                        'consultation_fees' => $model->consultation_fees,
                        'appointment_datetime' => $model->appointment_datetime,
                        'duration' => $model->duration,
                        'user_id' => $model->user_id,
                        'doctor_id' => $model->doctor_id,
                        'doctor_name' => $model->doctor->{"name_" . $lang},
                        'doctor_image' => $doctor_image,
                        'admin_commission' => (string)$model->admin_commission,
                        'doctor_categories' => $this::getCategoriesByIds($lang, $model->doctor_id, 'D'),
                        'created_at' => $model->created_at,
                        'updated_at' => $model->updated_at,
                        'kid_id' => !empty($model->kid_id) ? $model->kid_id : '',
                        'kid_details' => $kid_data,
                        'is_cancelled' => $model->is_cancelled,
                        'is_paid' => $model->is_paid,
                        'discount_price' => $model->discount_price,
                        'discount' => $model->discount,
                        'sub_total' => $model->sub_total,
                        'translator_price' => $translator_price,
                        'amount' => $model->amount,
                        'paymode' => $paymode,
                    ],
                    'payment_url' => (!empty($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['url'] : '',
                    'success_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['success'] : "",
                    'error_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['error'] : "",
                    'paymode' => ($paymode == 'W') ? 'W' : '',
                ];
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? "Appointment doesn't exist." : 'الموعد غير موجود';
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionDeleteDoctorAppointmentSlot($lang = "en")
    {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $user_id = $request['user_id'];
            $doctor_appointment_id = $request['doctor_appointment_id'];
            $booking = \app\models\DoctorAppointments::find()
                ->where(['doctor_appointment_id' => $doctor_appointment_id, 'user_id' => $user_id])
                ->andWhere(['is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($booking)) {
                if ($booking->delete()) {
                    $this->message = ($lang == "en") ? 'Slot successfully cleared' : "تم مسح الفتحة بنجاح";
                }
            } else {
                $this->response_code = 404;
                $this->message = ($lang == "en") ? 'Appointment does not exist' : "الموعد غير موجود";
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionAllSymptoms($lang = 'en')
    {
        $model = \app\models\Symptoms::find()
            ->where(['is_active' => 1, 'is_deleted' => 0])
            ->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $d['symptom_id'] = $row->symptom_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['image'] = (!empty($row->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No symptoms list found' : 'لم يتم العثور على قائمة أعراض';
            $this->data = "";
            return $this->response_array();
        }
        $this->data = $result;
        return $this->response_array();
    }

    public function actionAllTestCategories($lang = "en")
    {
        $result = [];
        $query = Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'type' => 'T', 'show_in_home' => 1]);
        $model = $query->all();
        if (!empty($model)) {
            foreach ($model as $row) {
                $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
                if ($lang == 'en' && !empty($row->icon)) {
                    $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon);
                } elseif ($lang == 'ar' && !empty($row->icon_ar)) {
                    $category_icon = Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon_ar);
                } else {
                    $category_icon = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }

                $d['id'] = $row->category_id;
                $d['name'] = (!empty($name)) ? $name : "";
                $d['image'] = $category_icon;
                array_push($result, $d);
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No test categories list found' : "لم يتم العثور على قائمة فئات الاختبار";
            $this->data = "";
            return $this->response_array();
        }
        $this->data = $result;
        return $this->response_array();
    }
    public function actionMyAppointments($lang = 'en', $user_id = '')
    {
        $query   = \app\models\DoctorAppointments::find()
            ->join('LEFT JOIN', 'payment', 'payment.type_id = doctor_appointments.doctor_appointment_id')
            ->andwhere(['doctor_appointments.is_deleted' => 0, 'doctor_appointments.user_id' => $user_id, 'payment.result' => 'CAPTURED'])
            ->andWhere(['!=', 'doctor_appointments.is_paid', 0])
            ->orderby('doctor_appointments.appointment_datetime ASC');
        $model  = $query->all();
        $result = [];
        $upcoming_list = [];
        $cancelled_list = [];
        $completed_list = [];
        $today_date = strtotime(date('Y-m-d h:i:s'));
        // debugPrint($query);
        // die;
        if (!empty($model)) {
            foreach ($model as $row) {
                $appointment_datetime = strtotime($row->appointment_datetime);
                $d['appointment_id'] = $row->doctor_appointment_id;
                $d['appointment_number'] = $row->appointment_number;
                $d['appointment_datetime'] = $row->appointment_datetime;
                $d['consultation_type'] = $row->consultation_type;
                $d['appointment_type'] = ($row->consultation_type == 'I') ? 'In Person' : 'Video';
                $d['appointment_for'] = ($row->kid_id == null) ? 'Self' : $row->kid->{'name_' . $lang};
                $d['need_translator'] = $row->need_translator;
                $d['doctor_id'] = $row->doctor_id;
                $d['doctor_name'] = $row->doctor->{'name_' . $lang};
                $d['doctor_image'] = (!empty($row->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                $d['doctor_categories'] = $this::getCategoriesByIds($lang, $row->doctor_id, 'D');
                $d['payment_details'] = $this->getPaymentDetails($row, 'D');
                if ($row->uploaded_report != '') {
                    $d['pdf_report'] = (!empty($row->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->uploaded_report) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['pdf_report'] = "";
                }
                if ($row->is_paid == 1 && $row->is_cancelled == 0 && $row->is_completed == 0 && $appointment_datetime > $today_date) {
                    array_push($upcoming_list, $d);
                } elseif (($row->is_paid == 1 || $row->is_paid == 0) && $row->is_cancelled == 1 && $row->is_completed == 0) {
                    array_push($cancelled_list, $d);
                } elseif ($row->is_paid == 1 && $row->is_cancelled == 0 && $row->is_completed == 1) {
                    array_push($completed_list, $d);
                }
            }
            $temp['upcoming_list'] = $upcoming_list;
            $temp['cancelled_list'] = $cancelled_list;
            $temp['completed_list'] = $completed_list;

            $this->data = $temp;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No appointments found' : "لم يتم العثور على مواعيد";
            $this->data = new stdClass();
            return $this->response_array();
        }
    }

    public function actionAppointmentDetails($lang = 'en', $doctor_appointment_id = '')
    {
        $query   = \app\models\DoctorAppointments::find()
            ->join('LEFT JOIN', 'payment', 'payment.type_id = doctor_appointments.doctor_appointment_id')
            ->andwhere(['doctor_appointments.is_deleted' => 0, 'doctor_appointments.doctor_appointment_id' => $doctor_appointment_id, 'payment.result' => 'CAPTURED'])
            ->andWhere(['!=', 'doctor_appointments.is_paid', 0])
            ->orderby('doctor_appointments.doctor_appointment_id desc');
        $model  = $query->all();
        $result = [];
        //        if ( date_default_timezone_get() == 'UTC') //default timezone
        //        {
        //            $today_date = strtotime(date('Y-m-d H:i:s', strtotime('+2 hour +00 minutes')));
        //            // $today_date = strtotime(date('Y-m-d H:i:s'));
        //
        //        } else {
        date_default_timezone_set("Asia/Kuwait");
        $serverTime = date('Y-m-d H:i:s');
        date_default_timezone_set("Asia/Kuwait");
        $today_date = strtotime(date('Y-m-d H:i:s'));

        $appointment_status = 'upcoming';
        if (!empty($model)) {
            foreach ($model as $row) {
                /*echo $today_date.date_default_timezone_get();
                echo "<br>".*/
                $holdPeriod = (int) $row->duration + 60;
                $appointment_datetime_with_duration = strtotime(date('Y-m-d H:i:s', strtotime('+' . $holdPeriod . ' minutes', strtotime($row->appointment_datetime))));
                $appointment_datetime = strtotime($row->appointment_datetime);

                if ($appointment_datetime_with_duration < $today_date) {
                    $row->is_call_initiated = 2;
                    $row->save(false);
                }
                //                debugPrint($appointment_datetime);
                //                debugPrint($today_date);
                //                exit;
                if ($row->is_paid == 1 && $row->is_cancelled == 0 && $row->is_completed == 0 && $appointment_datetime > $today_date) {
                    $appointment_status = 'Upcoming';
                } elseif (($row->is_paid == 1 || $row->is_paid == 0) && $row->is_cancelled == 1 && $row->is_completed == 0) {
                    $appointment_status = 'Completed';
                } elseif ($row->is_paid == 1 && $row->is_cancelled == 0 && $row->is_completed == 1) {
                    $appointment_status = 'Expired';
                } elseif ($appointment_datetime_with_duration < $today_date) {
                    $appointment_status = 'No Show';
                }

                $appointment_datetime = strtotime($row->appointment_datetime);
                $d['server_time'] = date("Y-m-d H:i:s");
                //                $d['server_time2'] = $serverTime;
                $d['appointment_id'] = $row->doctor_appointment_id;
                $d['appointment_number'] = $row->appointment_number;
                $d['appointment_datetime'] = $row->appointment_datetime;
                $d['need_translator'] = $row->need_translator;
                $d['duration'] = $row->duration;
                $d['is_call_initiated'] = $row->is_call_initiated;
                $d['consultation_type'] = $row->consultation_type;
                $d['appointment_type'] = ($row->consultation_type == 'I') ? 'In Person' : 'Video';
                $d['appointment_for'] = ($row->kid_id == null) ? 'Self' : $row->kid->{'name_' . $lang};
                $d['doctor_id'] = $row->doctor_id;
                $d['doctor_name'] = $row->doctor->{'name_' . $lang};
                $d['doctor_image'] = (!empty($row->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                $d['doctor_categories'] = $this::getCategoriesByIds($lang, $row->doctor_id, 'D');
                $d['payment_details'] = $this->getPaymentDetails($row, 'D');
                if ($row->uploaded_report != '') {
                    $d['pdf_report'] = (!empty($row->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->uploaded_report) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['pdf_report'] = "";
                }
                $d['status'] = $appointment_status;
                $d['holdPeriod'] = $holdPeriod;
            }
            $this->data = $d;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No appointments detail found' : "لم يتم العثور على تفاصيل المواعيد";
            $this->data = new stdClass();
            return $this->response_array();
        }
    }








    public function actionLabAppointmentDetails($lang = 'en', $lab_appointment_id = '')
    {

        $query  = \app\models\LabAppointments::find()
            ->join('LEFT JOIN', 'payment', 'payment.type_id = lab_appointments.lab_appointment_id')
            ->where(['lab_appointment_id' => $lab_appointment_id])
            ->andwhere(['lab_appointments.is_deleted' => 0,  'payment.result' => 'CAPTURED'])
            ->andWhere(['!=', 'lab_appointments.is_paid', 0])
            ->orderby('lab_appointments.appointment_datetime ASC');
        $model  = $query->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $lab_tests = [];
                $lab_address = [];
                $user_address = [];
                if (!empty($row->labAppointmentTests)) {
                    foreach ($row->labAppointmentTests as $lab_row) {
                        $l['lab_appointment_test_id'] = $lab_row->lab_appointment_test_id;
                        $l['test_id'] = $lab_row->test_id;
                        $l['test_name'] = $lab_row->test->{'name_' . $lang};
                        $l['is_home_service'] = ($lab_row->test->is_home_service == 1) ? 'Yes' : 'No';
                        array_push($lab_tests, $l);
                    }
                }
                $country_name = (isset($row->lab->area)) ? $row->lab->area->state->country->name_en : '';
                $lab_address = [
                    'country_name' => $country_name,
                    'governorate' => (isset($row->lab->governorate)) ? $row->lab->governorate->name_en : '',
                    'area' => (isset($row->lab->area)) ? $row->lab->area->name_en : '',
                    'block' => (isset($row->lab->block)) ? $row->lab->block : '',
                    'street' => (isset($row->lab->street)) ? $row->lab->street : '',
                    'building' => (isset($row->lab->building)) ? $row->lab->building : '',
                    'latlon' => (isset($row->lab->latlon)) ? $row->lab->latlon : '',
                ];

                $user_address = new stdClass();
                if ($row->type == 'H') {
                    $userAddresses = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $row->user_id, 'shipping_address_id' => $row->user_address_id, 'is_deleted' => 0])
                        ->one();

                    if (!empty($userAddresses)) {
                        $user_address = [
                            'first_name'    => $userAddresses->first_name,
                            'shipping_address_id' => $userAddresses->shipping_address_id,
                            'area_id' => $userAddresses->area_id,
                            'area_name' => !empty($userAddresses->area) ? (($lang == 'en') ? $userAddresses->area->name_en : $userAddresses->area->name_ar) : "",
                            'governorate_id' => !empty($userAddresses->state) ? $userAddresses->state_id : "",
                            'governorate_name' => !empty($userAddresses->state) ? (($lang == 'en') ? $userAddresses->state->name_en : $userAddresses->state->name_ar) : "",
                            'country_id' => !empty($userAddresses->country_id) ? $userAddresses->country_id : "",
                            'country_name' => !empty($userAddresses->country_id) ? (($lang == 'en') ? $userAddresses->country->name_en : $userAddresses->country->name_ar) : "",
                            'block_id' => (!empty($userAddresses->block_id)) ? $userAddresses->block_id : '',
                            'block_name' => (!empty($userAddresses->block_id)) ? \app\helpers\AppHelper::getBlockNameById($userAddresses->block_id, $lang) : '',
                            'avenue' => $userAddresses->avenue,
                            'street' => $userAddresses->street,
                            'building_number' => (string) $userAddresses->building,
                            'flat_number' => (string) $userAddresses->flat,
                            'floor_number' => (string) $userAddresses->floor,
                            'addressline_1' => $userAddresses->addressline_1,
                            'mobile_number' => $userAddresses->mobile_number,
                            'alt_phone_number' => $userAddresses->alt_phone_number,
                            'location_type' => $userAddresses->location_type,
                            'notes' => $userAddresses->notes,
                            'is_default' => ($userAddresses->is_default == '0') ? "No" : "Yes",
                            'phonecode' => !empty($userAddresses->country) ? $userAddresses->country->phonecode : "",
                        ];
                    }
                }

                $appointment_datetime = strtotime($row->appointment_datetime);
                $d['appointment_id'] = $row->lab_appointment_id;
                $d['appointment_number'] = $row->appointment_number;
                $d['appointment_datetime'] = $row->appointment_datetime;
                $d['appointment_for'] = ($row->kid_id == null) ? 'Self' : $row->kid->{'name_' . $lang};
                $d['type'] = $row->type;
                $d['user_id'] = $row->user_id;
                $d['user_address'] = $user_address;
                $d['lab_id'] = $row->lab_id;
                $d['lab_name'] = $row->lab->{'name_' . $lang};
                if ($lang == 'ar') {
                    $d['lab_image'] = (!empty($row->lab->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->lab->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['lab_image'] = (!empty($row->lab->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->lab->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }

                if ($row->uploaded_report != '') {
                    $d['pdf_report'] = (!empty($row->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->uploaded_report) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['pdf_report'] = "";
                }

                $d['lab_address'] = ($country_name != '') ? $lab_address : new stdClass();
                $d['lab_test'] = $lab_tests;
                $d['payment_details'] = $this->getPaymentDetails($row, 'L');
                array_push($result, $d);
            }
            $this->data =  $result[0];
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No lab appointments found' : 'لم يتم العثور على مواعيد معملية';
            $this->data = new stdClass();
            return $this->response_array();
        }
    }

    public function actionLabReports($lang = 'en', $user_id = '')
    {
        $query  = \app\models\LabAppointments::find()
            ->join('LEFT JOIN', 'payment', 'payment.type_id = lab_appointments.lab_appointment_id')
            ->andwhere(['lab_appointments.is_deleted' => 0, 'lab_appointments.user_id' => $user_id, 'payment.result' => 'CAPTURED'])
            ->andWhere(['!=', 'lab_appointments.is_paid', 0])
            ->orderby('lab_appointments.appointment_datetime ASC');
        $model  = $query->all();
        $result = [];
        $upcoming_list = [];
        $cancelled_list = [];
        $completed_list = [];
        $today_date = strtotime(date('Y-m-d h:i:s'));
        /*$lab_tests = [];
        $lab_address = [];
        $user_address = [];*/
        if (!empty($model)) {
            foreach ($model as $row) {
                $lab_tests = [];
                $lab_address = [];
                $user_address = [];
                if (!empty($row->labAppointmentTests)) {
                    foreach ($row->labAppointmentTests as $lab_row) {
                        $l['lab_appointment_test_id'] = $lab_row->lab_appointment_test_id;
                        $l['test_id'] = $lab_row->test_id;
                        $l['test_name'] = $lab_row->test->{'name_' . $lang};
                        $l['is_home_service'] = ($lab_row->test->is_home_service == 1) ? 'Yes' : 'No';
                        array_push($lab_tests, $l);
                    }
                }
                $country_name = (isset($row->lab->area)) ? $row->lab->area->state->country->name_en : '';
                $lab_address = [
                    'country_name' => $country_name,
                    'governorate' => (isset($row->lab->governorate)) ? $row->lab->governorate->name_en : '',
                    'area' => (isset($row->lab->area)) ? $row->lab->area->name_en : '',
                    'block' => (isset($row->lab->block)) ? $row->lab->block : '',
                    'street' => (isset($row->lab->street)) ? $row->lab->street : '',
                    'building' => (isset($row->lab->building)) ? $row->lab->building : '',
                    'latlon' => (isset($row->lab->latlon)) ? $row->lab->latlon : '',
                ];

                $user_address = new stdClass();
                if ($row->type == 'H') {
                    $userAddresses = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $row->user_id, 'shipping_address_id' => $row->user_address_id, 'is_deleted' => 0])
                        ->one();

                    if (!empty($userAddresses)) {
                        $user_address = [
                            'first_name'    => $userAddresses->first_name,
                            'shipping_address_id' => $userAddresses->shipping_address_id,
                            'area_id' => $userAddresses->area_id,
                            'area_name' => !empty($userAddresses->area) ? (($lang == 'en') ? $userAddresses->area->name_en : $userAddresses->area->name_ar) : "",
                            'governorate_id' => !empty($userAddresses->state) ? $userAddresses->state_id : "",
                            'governorate_name' => !empty($userAddresses->state) ? (($lang == 'en') ? $userAddresses->state->name_en : $userAddresses->state->name_ar) : "",
                            'country_id' => !empty($userAddresses->country_id) ? $userAddresses->country_id : "",
                            'country_name' => !empty($userAddresses->country_id) ? (($lang == 'en') ? $userAddresses->country->name_en : $userAddresses->country->name_ar) : "",
                            'block_id' => (!empty($userAddresses->block_id)) ? $userAddresses->block_id : '',
                            'block_name' => (!empty($userAddresses->block_id)) ? \app\helpers\AppHelper::getBlockNameById($userAddresses->block_id, $lang) : '',
                            'avenue' => $userAddresses->avenue,
                            'street' => $userAddresses->street,
                            'building_number' => (string) $userAddresses->building,
                            'flat_number' => (string) $userAddresses->flat,
                            'floor_number' => (string) $userAddresses->floor,
                            'addressline_1' => $userAddresses->addressline_1,
                            'mobile_number' => $userAddresses->mobile_number,
                            'alt_phone_number' => $userAddresses->alt_phone_number,
                            'location_type' => $userAddresses->location_type,
                            'notes' => $userAddresses->notes,
                            'is_default' => ($userAddresses->is_default == '0') ? "No" : "Yes",
                            'phonecode' => !empty($userAddresses->country) ? $userAddresses->country->phonecode : "",
                        ];
                    }
                }

                $appointment_datetime = strtotime($row->appointment_datetime);
                $d['appointment_id'] = $row->lab_appointment_id;
                $d['appointment_number'] = $row->appointment_number;
                $d['appointment_datetime'] = $row->appointment_datetime;
                $d['appointment_for'] = ($row->kid_id == null) ? 'Self' : $row->kid->{'name_' . $lang};
                $d['type'] = $row->type;
                $d['user_id'] = $row->user_id;
                $d['user_address'] = $user_address;
                $d['lab_id'] = $row->lab_id;
                $d['lab_name'] = $row->lab->{'name_' . $lang};
                if ($lang == 'ar') {
                    $d['lab_image'] = (!empty($row->lab->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->lab->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['lab_image'] = (!empty($row->lab->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->lab->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }

                if ($row->uploaded_report != '') {
                    $d['pdf_report'] = (!empty($row->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->uploaded_report) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $d['pdf_report'] = "";
                }

                $d['lab_address'] = ($country_name != '') ? $lab_address : new stdClass();
                $d['lab_test'] = $lab_tests;
                $d['payment_details'] = $this->getPaymentDetails($row, 'L');

                if ($row->is_paid == 1 && $row->is_cancelled == 0 && $row->is_completed == 0 && $appointment_datetime > $today_date) {
                    array_push($upcoming_list, $d);
                } elseif (($row->is_paid == 1 || $row->is_paid == 0) && $row->is_cancelled == 1 && $row->is_completed == 0) {
                    array_push($cancelled_list, $d);
                } elseif ($row->is_paid == 1 && $row->is_cancelled == 0 && ($row->is_completed == 1)) {
                    array_push($completed_list, $d);
                }
            }
            $temp['upcoming_list'] = $upcoming_list;
            $temp['cancelled_list'] = $cancelled_list;
            $temp['completed_list'] = $completed_list;
            $this->data = $temp;
            return $this->response_array();
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No lab appointments found' : 'لم يتم العثور على مواعيد معملية';
            $this->data = new stdClass();
            return $this->response_array();
        }
    }

    private function getPaymentDetails($row, $type)
    {
        if ($type == 'D') {
            $appointment_id = $row->doctor_appointment_id;
        } elseif ($type == 'L') {
            $appointment_id = $row->lab_appointment_id;
        }
        $settings = \app\models\Settings::find()
            ->where(['setting_id' => 1])
            ->one();
        if ($type == 'D') {
            $app_type = 'DA';
        } elseif ($type == 'L') {
            $app_type = 'LA';
        }

        $model   = \app\models\Payment::find()->where(['type_id' => $appointment_id, 'result' => 'CAPTURED', 'type' => $app_type])->one();
        if (!empty($model)) {
            $d['payment_id']    = $model->payment_id;
            $d['payment_date']  = $model->payment_date;
            $d['net_amount']    = $model->net_amount;
            $d['payment_method'] = $model->paymode; //($model->paymode == 'CC') ? 'Visa' : 'Knet';
            $d['transaction_id'] = $model->transaction_id;
            $d['auth']          = $model->auth;
            $d['ref']           = $model->ref;
            $d['track_id']      = $model->TrackID;
            $d['discount']      = $row->discount;
            $d['discount_price'] = $row->discount_price;
            $needTranslator = (isset($row->need_translator)) ? $row->need_translator : 0;

            $d['translator_price']   = ($needTranslator == 1) ? $settings->translator_price : 0.00;

            $d['sub_total']     = $row->sub_total;
            if ($type == 'L') {
                $d['home_service_price']     = $row->home_service_price;
            }

            $translatorPrice = ($needTranslator == 1) ? $settings->translator_price : 0;
            $extraPrice = ($type == 'L') ? (int) $row->home_service_price :   $translatorPrice;
            if ($row->discount_price == 0) {
                $d['total'] = $row->sub_total + $extraPrice;
            } else {
                $d['total'] = $row->sub_total + $extraPrice -  $row->discount_price;
            }
            return $d;
        } else {
            return new stdClass();
        }
    }

    public function actionRedeemCoupon($lang = "en", $store = "KW")
    {
        $request = Yii::$app->request->bodyParams;

        if (!empty($request)) {
            $store = $this->getStoreDetails($store);
            // if ($request['coupon_for'] == 'D') {
                $model = \app\models\VendorAppointments::find()
                    ->where(['user_id' => $request['user_id'], 'vendor_appointment_id' => $request['appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                    ->one();
            // } elseif ($request['coupon_for'] == 'L') {
            //     $model = \app\models\LabAppointments::find()
            //         ->where(['user_id' => $request['user_id'], 'lab_appointment_id' => $request['appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
            //         ->one();
            // } elseif ($request['coupon_for'] == 'F') {
            //     $model = \app\models\Orders::find()
            //         ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id']])
            //         ->one();
            // }

            if (!empty($model)) {
                $discount       = 0;
                $discountPrice  = 0;
                $promoId        = 0;
                $minimumOrder   = 0;
                if (isset($request['coupon_code']) && !empty($request['coupon_code'])) {
                    $promotionModelQuery = \app\models\Promotions::find()
                        ->join('LEFT JOIN', 'user_promotions', 'promotions.promotion_id = user_promotions.promotion_id')
                        ->where(['code' => $request['coupon_code'], 'is_active' => '1', 'is_deleted' => 0]);
                    $promotionModel = $promotionModelQuery->one();
                    if (!empty($promotionModel)) {
                        if ($request['coupon_for'] == 'L') {
                            $checkMultipleCodeUsedCount = \app\models\LabAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } elseif ($request['coupon_for'] == 'D') {
                            $checkMultipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } elseif ($request['coupon_for'] == 'F') {
                            $checkMultipleCodeUsedCount = \app\models\Orders::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } else {
                            $checkLabMultipleCodeUsedCount = \app\models\LabAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                            $checkDoctorMultipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                            $checkMultipleCodeUsedCount = $checkLabMultipleCodeUsedCount + $checkDoctorMultipleCodeUsedCount;
                        }

                        if (!empty($promotionModel->promo_count) && $checkMultipleCodeUsedCount >= $promotionModel->promo_count && $promotionModel->promo_type == 'S') {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? "Coupon limit exceeded." : "تم تجاوز حد القسيمة.";
                            return $this->response();
                        } elseif ($promotionModel->promo_type == 'S' && $checkMultipleCodeUsedCount > 0) {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? "This code has already been redeemed." : "وقد تم بالفعل استبدال هذا الرمز.";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                        $allowedUser = false;
                        $isValidCoupon = false;
                        if (!empty($promotionModel->promotionUsers)) {
                            $userPromo = \app\models\PromotionUsers::find()
                                ->where(['user_id' => $model->user_id, 'promotion_id' => $promotionModel->promotion_id])
                                ->one();
                            if (!empty($userPromo)) {
                                $allowedUser = true;
                            }
                        } else {
                            $allowedUser = true;
                        }
                        $validDate = false;
                        if ($promotionModel->start_date != null && $promotionModel->end_date != null) {
                            $today = date('Y-m-d');
                            if (strtotime($today) >= strtotime($promotionModel->start_date) && strtotime($today) <= strtotime($promotionModel->end_date)) {
                                $validDate = true;
                            } else {
                                $validDate = false;
                            }
                        } else {
                            $validDate = true;
                        }
                        if ($allowedUser && $validDate) {
                            $isValidCoupon = true;
                        }

                        if ($isValidCoupon) {
                            $promotion = \app\models\Promotions::find()
                                ->where(['promotion_id' => $promotionModel->promotion_id])
                                ->asArray()
                                ->one();
                            if ($promotion['promo_type'] == 'M') {
                                if ($request['coupon_for'] == 'D') {
                                    $multipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                        ->where(['promotion_id' => $promotion['promotion_id']])
                                        ->count();
                                } elseif ($request['coupon_for'] == 'L') {
                                    $multipleCodeUsedCount = \app\models\LabAppointments::find()
                                        ->where(['promotion_id' => $promotion['promotion_id']])
                                        ->count();
                                } elseif ($request['coupon_for'] == 'F') {
                                    $multipleCodeUsedCount = \app\models\Orders::find()
                                        ->where(['promotion_id' => $promotion['promotion_id'], 'is_paid' => 1])
                                        ->count();
                                }

                                if (!empty($promotion['promo_count'])) {
                                    if ($multipleCodeUsedCount < $promotion['promo_count']) {
                                        $discount = $promotion['discount'];
                                        $promoFor = $promotion['promo_for'];
                                        $promoId = $promotion['promotion_id'];
                                        $minimumOrder = $promotion['minimum_order'];
                                        $model->promotion_id = $promotion['promotion_id'];
                                        $model->promo_for = $promotion['promo_for'];
                                        $model->discount = $discount;
                                    } else {
                                        $this->response_code = 500;
                                        $this->message = ($lang == "en") ? "Coupon code already redeemed" : "تم استرداد رمز القسيمة بالفعل";
                                        $this->data = new stdClass();
                                        return $this->response();
                                    }
                                } else {
                                    $discount = $promotion['discount'];
                                    $promoFor = $promotion['promo_for'];
                                    $promoId = $promotion['promotion_id'];
                                    $minimumOrder = $promotion['minimum_order'];
                                    $model->promotion_id = $promotion['promotion_id'];
                                    $model->promo_for = $promotion['promo_for'];
                                    $model->discount = $discount;
                                }
                            } else {
                                $discount = $promotion['discount'];
                                $promoFor = $promotion['promo_for'];
                                $promoId = $promotion['promotion_id'];
                                $minimumOrder = $promotion['minimum_order'];
                                $model->promotion_id = $promotion['promotion_id'];
                                $model->promo_for = $promotion['promo_for'];
                                $model->discount = $discount;
                            }
                        } else {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? 'Invalid coupon code' : "رقم قسيمه غير صالح";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                    } else {
                        $this->response_code = 404;
                        $this->message = ($lang == "en") ? "Coupon code does not exist" : "رمز القسيمة غير موجود";
                        $this->data = new stdClass();
                        return $this->response();
                    }
                } else {
                    $oldPromoId = $model->promotion_id;
                    $model->promotion_id = null;
                    $model->promo_for = null;
                    $model->discount = null;
                    $model->discount_price = 0;
                    if ($request['coupon_for'] == 'D') {
                        $settings = Settings::find()->one();
                        $translator_price = $model->need_translator == "1" ? $settings['translator_price'] : 0;
                        $model->amount = $model->sub_total + $translator_price  - $discountPrice;
                    } elseif ($request['coupon_for'] == 'L') {
                        $model->amount = $model->sub_total + ($model->type == "H" ? (float) $model->home_service_price : 0) - $model->discount_price;
                    }
                    // else {
                    //     $model->amount = $model->sub_total;
                    // }
                    //
                    $userPromotionModel = \app\models\UserPromotions::find()
                        ->where(['user_id' => $model->user_id, 'promotion_id' => $oldPromoId])
                        ->one();
                    if (!empty($userPromotionModel)) {
                        $userPromotionModel->status = 0;
                        $userPromotionModel->save();
                    }
                }
                $model->save(false);

                $subTotal = 0;
                $totalItems = 0;
                $hasPromotionShopProduct = [];
                if ($request['coupon_for'] == 'F') {
                    $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                    if (isset($cartDetails['items'])) {
                        foreach ($cartDetails['items'] as $item) {
                            $subTotal += ($item['final_price'] * $item['quantity']);
                            $totalItems += $item['quantity'];
                            if (isset($promoFor) && !empty($promoFor)) {
                                if ($promoFor == 'F') {
                                    if (isset($item['pharmacy_id']) && $item['pharmacy_id'] != null) {
                                        if (empty($promotion->promotionPharmacy)) {
                                            $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                        } else {
                                            $hasPromoShop = 1;
                                            $promoShops = \app\models\promotionPharmacy::find()
                                                ->where(['promotion_id' => $promoId, 'pharmacy_id' => $item['pharmacy_id']])
                                                ->one();
                                            if (!empty($promoShops)) {
                                                $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                                $hasPromotionShopProduct[] = $item['shop_id'];
                                            } else {
                                                //$discountPrice = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($request['coupon_for'] == 'L') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                } elseif ($request['coupon_for'] == 'D') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                }
                if (isset($minimumOrder)) {
                    if ($request['coupon_for'] == 'F') {
                        $msg = 'orders';
                    } else {
                        $msg = 'appointment';
                    }
                    $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                    $currencyModel = Currencies::find()->where(['currency_id' => $store['currency_id']])->one();
                    if ($subTotal < $minimumOrderConverted) {
                        $currencyCode = (!empty($currencyModel->code)) ? $currencyModel->code : "";
                        $this->response_code = 201;
                        if ($lang == "en") {
                            $this->message = 'This promo is not valid for ' . $msg . ' below ' . $minimumOrderConverted . ' ' . $currencyCode;
                        } else {
                            $this->message = 'هذا العرض غير صالح ' . $msg . ' أقل من ' . $minimumOrderConverted . ' ' . $currencyCode;
                        }
                        $this->data = new stdClass();
                        return $this->response();
                    }
                }

                $promotionSql = \app\models\Promotions::find()
                    ->where(['code' => $request['coupon_code'], 'is_active' => '1', 'is_deleted' => 0]);
                $promotion = $promotionSql->one();
                $total =  0;
                $hasPromo = 0;
                if (isset($subTotal)) {
                    if (isset($promoFor) && !empty($promoFor)) {
                        if ($promoFor == 'D' && $request['coupon_for'] == 'D') {
                            //print_r($promotion->promotionDoctors);die;
                            if (empty($promotion->promotionDoctors)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                // custom 
                                $model->discount_price = $discountPrice;
                                $settings = Settings::find()->one();
                                $translator_price = $model->need_translator == "1"  ? $settings['translator_price'] : 0;
                                $model->amount = $model->sub_total + $translator_price -  $discountPrice;
                                $model->save(false);
                                // custom 
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\PromotionDoctors::find()
                                    ->where(['promotion_id' => $promoId, 'doctor_id' => $model->doctor_id])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $hasPromotionFor[] = $model->doctor_id;
                                    // custom 
                                    $model->discount_price = $discountPrice;
                                    $settings = Settings::find()->one();
                                    $translator_price = $model->need_translator == "1"  ? $settings['translator_price'] : 0;
                                    $model->amount = $model->sub_total + $translator_price -  $discountPrice;
                                    $model->save(false);
                                    // custom 
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ? 'This promocode not applicable to this doctor appointment' : "هذا الرمز الترويجي لا ينطبق على موعد الطبيب هذا";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } elseif ($promoFor == 'L' && $request['coupon_for'] == 'L') {
                            if (empty($promotion->promotionLabs)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                $model->amount = $model->sub_total + ($model->type == "H" ?  (float) $model->home_service_price : 0) - $discountPrice;
                                $model->save(false);
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\PromotionLabs::find()
                                    ->where(['promotion_id' => $promoId, 'lab_id' => $model->lab_id])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $model->amount = $model->sub_total + ($model->type == "H" ?  (float) $model->home_service_price : 0) - $discountPrice;
                                    $model->save(false);
                                    $hasPromotionFor[] = $model->lab_id;
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ?  'This promocode not applicable to this lab appointment' : "هذا الرمز الترويجي لا ينطبق على موعد المختبر هذا";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } elseif ($promoFor == 'F' && $request['coupon_for'] == 'F') {
                            if (empty($promotion->promotionPharmacy)) {
                                $discountPrice = ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\promotionPharmacy::find()
                                    ->where(['promotion_id' => $promoId, 'pharmacy_id' =>  $item['pharmacy_id']])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                    $hasPromotionFor[] = $item['pharmacy_id'];
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ? 'This promocode not applicable to this pharmacy' : "هذا الرمز الترويجي لا ينطبق على هذه الصيدلية";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } else {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? 'Invalid coupon code' : "رقم قسيمه غير صالح";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                    } else {
                        $discountPrice = ((($subTotal * $discount) / 100) * 1);
                        // $model->discount_price = $discountPrice;
                        // $model->save(false);
                    }
                } elseif ($request['coupon_for'] == 'L') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                } elseif ($request['coupon_for'] == 'D') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                }
                if ($hasPromo == 1 && empty($hasPromotionFor)) {

                    if ($promoFor == 'L' && $request['coupon_for'] == 'L') {
                        $labAppointmentModel = LabAppointments::findOne($model->lab_appointment_id);
                        $labAppointmentModel->promotion_id = null;
                        $labAppointmentModel->promo_for = null;
                        $labAppointmentModel->discount = null;
                        $labAppointmentModel->save(false);
                        $this->response_code = 201;
                        $this->message = ($lang == "en") ? 'Sorry, this promo code is not applicable to any of the Lab you selected.' : "عذرًا ، لا ينطبق هذا الرمز الترويجي على أي من المختبرات التي حددتها";
                    } elseif ($promoFor == 'D' && $request['coupon_for'] == 'D') {
                        $appointmentModel = DoctorAppointments::findOne($model->doctor_appointment_id);
                        $appointmentModel->promotion_id = null;
                        $appointmentModel->promo_for = null;
                        $appointmentModel->discount = null;
                        $appointmentModel->save(false);
                        $this->response_code = 201;
                        $this->message = ($lang == "en") ? 'Sorry, this promo code is not applicable to any of the doctor you selected.' : "عذرًا ، هذا الرمز الترويجي لا ينطبق على أي طبيب اخترته.";
                    }
                    return $this->response();
                }
                $total = ($subTotal - $discountPrice);
                $paymentTypes = AppHelper::paymentTypes($lang);

                if ($model->promotion_id != null) {
                    $couponModel = \app\models\Promotions::find()
                        ->where(['promotion_id' => $model->promotion_id])
                        ->one();
                    $is_coupon_applied = 1;
                    $coupon = [
                        'title' => (string) $couponModel->{"title_" . $lang},
                        'code' => (string) $couponModel->code,
                        'discount' => (string) $couponModel->discount . '%',
                    ];
                } else {
                    $is_coupon_applied = 0;
                    $coupon = new stdClass();
                }
                $settings = \app\models\Settings::find()
                    ->where(['setting_id' => 1])
                    // ->asArray()
                    ->one();

                $home_test_charge = 0;
                if (isset($model->doctor_appointment_id)) {
                    $appointmentModel = DoctorAppointments::findOne($model->doctor_appointment_id);
                }
                if (isset($model->lab_appointment_id)) {
                    $appointmentModel = LabAppointments::findOne($model->lab_appointment_id);
                    $home_test_charge  = $appointmentModel->type == "H" ? $appointmentModel->home_service_price : 0;
                }


                //                if ($appointmentModel->need_translator == 1) {
                $this->data = [
                    'payment_types' => $paymentTypes,
                    'sub_total'     => $subTotal,
                    'is_coupon_applied' => $is_coupon_applied,
                    'coupon'        => $coupon,
                    'discount_price' => $discountPrice,
                    'tr' => $discountPrice,
                    'home_test_charge' =>  $home_test_charge,
                    'total' =>  $request['coupon_for'] == 'F' ? $total : $appointmentModel->amount,

                    // 'total'         => (empty($isTranslator)) ? (string) $total : $total + $settings->translator_price - $discountPrice,
                    //                    'total2'         =>   (!empty($isTranslator)) ? "Yes" : "No",
                ];

                //                else {
                //                    $this->data = [
                //                        'payment_types' => $paymentTypes,
                //                        'sub_total'     => (string) $subTotal,
                //                        'is_coupon_applied' => $is_coupon_applied,
                //                        'coupon'        => $coupon,
                //                        'discount_price' => $discountPrice,
                //                        'tr' => $discountPrice,
                //                        'total'         => (string) $total,
                //                    ];
                //                }
            } else {
                if ($request['coupon_for'] == 'F') {
                    $msg = 'orders';
                } else {
                    $msg = 'appointment';
                }
                $this->response_code = 404;
                if ($lang == "en") {
                    $this->message = 'Requested ' . $msg . ' does not exist';
                } else {
                    $this->message = 'طلبت  ' . $msg . ' غير متوفر';
                }
                $this->data = new stdClass();
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
            $this->data = new stdClass();
        }

        return $this->response();
    }

    public function actionRedeemCoupon1($lang = "en", $store = "KW")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $store = $this->getStoreDetails($store);
            if ($request['coupon_for'] == 'D') {
                $model = \app\models\DoctorAppointments::find()
                    ->where(['user_id' => $request['user_id'], 'doctor_appointment_id' => $request['appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                    ->one();
            } elseif ($request['coupon_for'] == 'L') {
                $model = \app\models\LabAppointments::find()
                    ->where(['user_id' => $request['user_id'], 'lab_appointment_id' => $request['appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                    ->one();
            } elseif ($request['coupon_for'] == 'F') {
                $model = \app\models\Orders::find()
                    ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id']])
                    ->one();
            }

            if (!empty($model)) {
                $discount       = 0;
                $discountPrice  = 0;
                $promoId        = 0;
                $minimumOrder   = 0;
                if (isset($request['coupon_code']) && !empty($request['coupon_code'])) {
                    $promotionModelQuery = \app\models\Promotions::find()
                        ->join('LEFT JOIN', 'user_promotions', 'promotions.promotion_id = user_promotions.promotion_id')
                        ->where(['code' => $request['coupon_code'], 'is_active' => '1', 'is_deleted' => 0]);
                    $promotionModel = $promotionModelQuery->one();
                    if (!empty($promotionModel)) {
                        if ($request['coupon_for'] == 'L') {
                            $checkMultipleCodeUsedCount = \app\models\LabAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } elseif ($request['coupon_for'] == 'D') {
                            $checkMultipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } elseif ($request['coupon_for'] == 'F') {
                            $checkMultipleCodeUsedCount = \app\models\Orders::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                        } else {
                            $checkLabMultipleCodeUsedCount = \app\models\LabAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();

                            $checkDoctorMultipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                ->where(['is_paid' => [1], 'promotion_id' => $promotionModel->promotion_id])
                                ->count();
                            $checkMultipleCodeUsedCount = $checkLabMultipleCodeUsedCount + $checkDoctorMultipleCodeUsedCount;
                        }

                        if (!empty($promotionModel->promo_count) && $checkMultipleCodeUsedCount >= $promotionModel->promo_count && $promotionModel->promo_type == 'S') {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? "Coupon limit exceeded." : "تم تجاوز حد القسيمة.";
                            return $this->response();
                        } elseif ($promotionModel->promo_type == 'S' && $checkMultipleCodeUsedCount > 0) {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? "This code has already been redeemed." : "وقد تم بالفعل استبدال هذا الرمز.";
                            $this->data = new stdClass();
                            return $this->response();
                        }


                        $allowedUser = false;
                        $isValidCoupon = false;
                        if (!empty($promotionModel->promotionUsers)) {
                            $userPromo = \app\models\PromotionUsers::find()
                                ->where(['user_id' => $model->user_id, 'promotion_id' => $promotionModel->promotion_id])
                                ->one();
                            if (!empty($userPromo)) {
                                $allowedUser = true;
                            }
                        } else {
                            $allowedUser = true;
                        }
                        $validDate = false;
                        if ($promotionModel->start_date != null && $promotionModel->end_date != null) {
                            $today = date('Y-m-d');
                            if (strtotime($today) >= strtotime($promotionModel->start_date) && strtotime($today) <= strtotime($promotionModel->end_date)) {
                                $validDate = true;
                            } else {
                                $validDate = false;
                            }
                        } else {
                            $validDate = true;
                        }
                        if ($allowedUser && $validDate) {
                            $isValidCoupon = true;
                        }

                        if ($isValidCoupon) {
                            $promotion = \app\models\Promotions::find()
                                ->where(['promotion_id' => $promotionModel->promotion_id])
                                ->asArray()
                                ->one();
                            if ($promotion['promo_type'] == 'M') {
                                if ($request['coupon_for'] == 'D') {
                                    $multipleCodeUsedCount = \app\models\DoctorAppointments::find()
                                        ->where(['promotion_id' => $promotion['promotion_id']])
                                        ->count();
                                } elseif ($request['coupon_for'] == 'L') {
                                    $multipleCodeUsedCount = \app\models\LabAppointments::find()
                                        ->where(['promotion_id' => $promotion['promotion_id']])
                                        ->count();
                                } elseif ($request['coupon_for'] == 'F') {
                                    $multipleCodeUsedCount = \app\models\Orders::find()
                                        ->where(['promotion_id' => $promotion['promotion_id'], 'is_paid' => 1])
                                        ->count();
                                }

                                if (!empty($promotion['promo_count'])) {
                                    if ($multipleCodeUsedCount < $promotion['promo_count']) {
                                        $discount = $promotion['discount'];
                                        $promoFor = $promotion['promo_for'];
                                        $promoId = $promotion['promotion_id'];
                                        $minimumOrder = $promotion['minimum_order'];
                                        $model->promotion_id = $promotion['promotion_id'];
                                        $model->promo_for = $promotion['promo_for'];
                                        $model->discount = $discount;
                                    } else {
                                        $this->response_code = 500;
                                        $this->message = ($lang == "en") ? "Coupon code already redeemed" : "تم استرداد رمز القسيمة بالفعل";
                                        $this->data = new stdClass();
                                        return $this->response();
                                    }
                                } else {
                                    $discount = $promotion['discount'];
                                    $promoFor = $promotion['promo_for'];
                                    $promoId = $promotion['promotion_id'];
                                    $minimumOrder = $promotion['minimum_order'];
                                    $model->promotion_id = $promotion['promotion_id'];
                                    $model->promo_for = $promotion['promo_for'];
                                    $model->discount = $discount;
                                }
                            } else {
                                $discount = $promotion['discount'];
                                $promoFor = $promotion['promo_for'];
                                $promoId = $promotion['promotion_id'];
                                $minimumOrder = $promotion['minimum_order'];
                                $model->promotion_id = $promotion['promotion_id'];
                                $model->promo_for = $promotion['promo_for'];
                                $model->discount = $discount;
                            }
                        } else {
                            $this->response_code = 500;
                            $this->message = ($lang == "en") ? 'Invalid coupon code' : "رقم قسيمه غير صالح";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                    } else {
                        $this->response_code = 404;
                        $this->message = ($lang == "en") ? "Coupon code does not exist" : "رمز القسيمة غير موجود";
                        $this->data = new stdClass();
                        return $this->response();
                    }
                } else {
                    $oldPromoId = $model->promotion_id;
                    $model->promotion_id = null;
                    $model->promo_for = null;
                    $model->discount = null;
                    //
                    $userPromotionModel = \app\models\UserPromotions::find()
                        ->where(['user_id' => $model->user_id, 'promotion_id' => $oldPromoId])
                        ->one();
                    if (!empty($userPromotionModel)) {
                        $userPromotionModel->status = 0;
                        $userPromotionModel->save();
                    }
                }
                $model->save(false);
                $subTotal = 0;
                $totalItems = 0;
                $hasPromotionShopProduct = [];
                if ($request['coupon_for'] == 'F') {
                    $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                    if (isset($cartDetails['items'])) {
                        foreach ($cartDetails['items'] as $item) {
                            $subTotal += ($item['final_price'] * $item['quantity']);
                            $totalItems += $item['quantity'];
                            if (isset($promoFor) && !empty($promoFor)) {
                                if ($promoFor == 'F') {
                                    if (isset($item['pharmacy_id']) && $item['pharmacy_id'] != null) {
                                        if (empty($promotion->promotionPharmacy)) {
                                            $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                        } else {
                                            $hasPromoShop = 1;
                                            $promoShops = \app\models\promotionPharmacy::find()
                                                ->where(['promotion_id' => $promoId, 'pharmacy_id' => $item['pharmacy_id']])
                                                ->one();
                                            if (!empty($promoShops)) {
                                                $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                                $hasPromotionShopProduct[] = $item['shop_id'];
                                            } else {
                                                //$discountPrice = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($request['coupon_for'] == 'L') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                } elseif ($request['coupon_for'] == 'D') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                }
                if (isset($minimumOrder)) {
                    if ($request['coupon_for'] == 'F') {
                        $msg = 'orders';
                    } else {
                        $msg = 'appointment';
                    }
                    $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                    $currencyModel = Currencies::find()->where(['currency_id' => $store['currency_id']])->one();
                    if ($subTotal < $minimumOrderConverted) {
                        $this->response_code = 201;
                        $currencyCode = (!empty($currencyModel->code)) ? $currencyModel->code : "";
                        if ($lang == "en") {
                            $this->message = 'This promo is not valid for ' . $msg . ' below ' . $minimumOrderConverted . ' ' . $currencyCode;
                        } else {
                            $this->message = 'هذا العرض غير صالح ' . $msg . ' أقل من ' . $minimumOrderConverted . ' ' . $currencyCode;
                        }


                        return $this->response();
                    }
                }

                $promotionSql = \app\models\Promotions::find()
                    ->where(['code' => $request['coupon_code'], 'is_active' => '1', 'is_deleted' => 0]);
                $promotion = $promotionSql->one();
                $total =  0;
                $hasPromo = 0;
                if (isset($subTotal)) {
                    if (isset($promoFor) && !empty($promoFor)) {
                        if ($promoFor == 'D' && $request['coupon_for'] == 'D') {
                            //print_r($promotion->promotionDoctors);die;
                            if (empty($promotion->promotionDoctors)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\PromotionDoctors::find()
                                    ->where(['promotion_id' => $promoId, 'doctor_id' => $model->doctor_id])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $hasPromotionFor[] = $model->doctor_id;
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ? 'This promocode not applicable to this doctor appointment' : "هذا الرمز الترويجي لا ينطبق على موعد الطبيب هذا";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } elseif ($promoFor == 'L' && $request['coupon_for'] == 'L') {
                            if (empty($promotion->promotionLabs)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\PromotionLabs::find()
                                    ->where(['promotion_id' => $promoId, 'lab_id' => $model->lab_id])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $hasPromotionFor[] = $model->lab_id;
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ? 'This promocode not applicable to this lab appointment' : "هذا الرمز الترويجي لا ينطبق على موعد المختبر هذا";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } elseif ($promoFor == 'F' && $request['coupon_for'] == 'F') {
                            if (empty($promotion->promotionPharmacy)) {
                                $discountPrice = ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromo = 1;
                                $promoFor = \app\models\promotionPharmacy::find()
                                    ->where(['promotion_id' => $promoId, 'pharmacy_id' =>  $item['pharmacy_id']])
                                    ->one();
                                if (!empty($promoFor)) {
                                    $discountPrice = ((($subTotal * $discount) / 100) * $item['quantity']);
                                    $hasPromotionFor[] = $item['pharmacy_id'];
                                } else {
                                    $this->response_code = 404;
                                    $this->message = ($lang == "en") ? 'This promocode not applicable to this pharmacy' : "هذا الرمز الترويجي لا ينطبق على هذه الصيدلية";
                                    $this->data = new stdClass();
                                    return $this->response();
                                }
                            }
                        } else {
                            $this->response_code = 500;
                            $this->message =  ($lang == "en") ? 'Invalid coupon code' : "رقم قسيمه غير صالح";
                            $this->data = new stdClass();
                            return $this->response();
                        }
                    } else {
                        $discountPrice = ((($subTotal * $discount) / 100) * 1);
                    }
                } elseif ($request['coupon_for'] == 'L') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                } elseif ($request['coupon_for'] == 'D') {
                    $subTotal = isset($model->sub_total) ? $model->sub_total : 0;
                }

                if ($hasPromo == 1 && empty($hasPromotionFor)) {

                    if ($promoFor == 'L' && $request['coupon_for'] == 'L') {
                        $labAppointmentModel = LabAppointments::findOne($model->lab_appointment_id);
                        $labAppointmentModel->promotion_id = null;
                        $labAppointmentModel->promo_for = null;
                        $labAppointmentModel->discount = null;
                        $labAppointmentModel->save(false);
                        $this->response_code = 201;
                        $this->message = ($lang == "en") ? 'Sorry, this promo code is not applicable to any of the Lab you selected.' : "عذرًا ، لا ينطبق هذا الرمز الترويجي على أي من المختبرات التي حددتها";
                    } elseif ($promoFor == 'D' && $request['coupon_for'] == 'D') {
                        $appointmentModel = DoctorAppointments::findOne($model->doctor_appointment_id);
                        $appointmentModel->promotion_id = null;
                        $appointmentModel->promo_for = null;
                        $appointmentModel->discount = null;
                        $appointmentModel->save(false);
                        $this->response_code = 201;
                        $this->message = ($lang == "en") ? 'Sorry, this promo code is not applicable to any of the doctor you selected.' : "عذرًا ، هذا الرمز الترويجي لا ينطبق على أي طبيب اخترته.";
                    }

                    return $this->response();
                }
                $total = ($subTotal - $discountPrice);
                $paymentTypes = AppHelper::paymentTypes($lang);

                if ($model->promotion_id != null) {
                    $couponModel = \app\models\Promotions::find()
                        ->where(['promotion_id' => $model->promotion_id])
                        ->one();
                    $is_coupon_applied = 1;
                    $coupon = [
                        'title' => (string) $couponModel->{"title_" . $lang},
                        'code' => (string) $couponModel->code,
                        'discount' => (string) $couponModel->discount . '%',
                    ];
                } else {
                    $is_coupon_applied = 0;
                    $coupon = new stdClass();
                }

                $this->data = [
                    'payment_types' => $paymentTypes,
                    'sub_total'     => (string) $subTotal,
                    'total'         => (string) $total,
                    'is_coupon_applied' => $is_coupon_applied,
                    'coupon'        => $coupon,
                    'discount_price' => $discountPrice,
                ];
            } else {
                if ($request['coupon_for'] == 'F') {
                    $msg = 'orders';
                } else {
                    $msg = 'appointment';
                }
                $this->response_code = 404;

                if ($lang == "en") {
                    $this->message = 'Requested ' . $msg . ' does not exist';
                } else {
                    $this->message = 'أوامر الطلب ' . $msg . 'غير موجود ';
                }

                $this->data = new stdClass();
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
            $this->data = new stdClass();
        }

        return $this->response();
    }

    private function verifyPromotion($promotionId, $userId, $cartDetails, $lang, $store, $shippingAddressId = "")
    {
        $promotionModelQuery = \app\models\Promotions::find()
            ->where(['promotion_id' => $promotionId, 'is_deleted' => 0]);

        $promotionModel = $promotionModelQuery->one();
        if (!empty($promotionModel)) {
            if ($promotionModel->promo_type == 'S') {
                /* $checkUserPromo = \app\models\UserPromotions::find()
                  ->where(['user_id' => $userId, 'status' => 1, 'promotion_id' => $promotionModel->promotion_id])
                  ->one();

                  if (!empty($checkUserPromo)) {
                  return [
                  'code' => 500,
                  'message' => 'Coupon already used once.'
                  ];
                  }
                  else {
                  $discount = $promotionModel->discount;
                  $minimumOrder = $promotionModel->minimum_order;
                  $promoFor = $promotionModel->promo_for;
                  $promoId = $promotionModel->promotion_id;
                  } */
                $discount = $promotionModel->discount;
                $minimumOrder = $promotionModel->minimum_order;
                $promoFor = $promotionModel->promo_for;
                $promoId = $promotionModel->promotion_id;
            } elseif ($promotionModel->promo_type == 'M') {
                $checkMultipleCodeUsedCount = \app\models\Orders::find()
                    ->join('LEFT JOIN', '(
                                                SELECT t1.*
                                                FROM order_status AS t1
                                                LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                        AND (t1.status_date < t2.status_date 
                                                         OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                                WHERE t2.order_id IS NULL
                                                ) as temp', 'temp.order_id = orders.order_id')
                    ->where(['is_processed' => [1, 3], 'promotion_id' => $promotionModel->promotion_id])
                    ->andWhere(['!=', 'temp.status_id', 6])
                    ->count();

                if (!empty($promotionModel->promo_count) && $checkMultipleCodeUsedCount >= $promotionModel->promo_count) {
                    return [
                        'code' => 500,
                        'message' => ($lang == "en") ? "Coupon limit exceeded." : "تم تجاوز حد القسيمة."
                    ];
                } elseif (!empty($promotionModel->promo_count) && $checkMultipleCodeUsedCount < $promotionModel->promo_count) {
                    $discount = $promotionModel->discount;
                    $minimumOrder = $promotionModel->minimum_order;
                    $promoFor = $promotionModel->promo_for;
                    $promoId = $promotionModel->promotion_id;
                } elseif (empty($promotionModel->promo_count)) {
                    $discount = $promotionModel->discount;
                    $minimumOrder = $promotionModel->minimum_order;
                    $promoFor = $promotionModel->promo_for;
                    $promoId = $promotionModel->promotion_id;
                }
            }

            if (!empty($promotionModel->promotionUsers)) {
                $userPromo = \app\models\PromotionUsers::find()
                    ->where(['user_id' => $userId, 'promotion_id' => $promotionModel->promotion_id])
                    ->one();

                if (empty($userPromo)) {
                    return [
                        'code' => 500,
                        'message' => ($lang == "en") ? 'User not allowed.' : 'المستخدم غير مسموح به'
                    ];
                }
            }

            if ($promotionModel->start_date != null && $promotionModel->end_date != null) {
                $today = date('Y-m-d');
                if (strtotime($today) < strtotime($promotionModel->start_date) || strtotime($today) > strtotime($promotionModel->end_date)) {
                    return [
                        'code' => 500,
                        'message' => ($lang == "en") ? 'Date exceeded.' : 'تم تجاوز التاريخ'
                    ];
                }
            }
        } else {
            return [
                'code' => 500,
                'message' => ($lang == "en") ? 'Code does not exist.' : 'الرمز غير موجود'
            ];
        }

        if (!empty($shippingAddressId)) {
            $defaultAddress = $this->getUserDefaultAddress($userId, $lang, $store, $shippingAddressId);
        } else {
            $defaultAddress = $this->getUserDefaultAddress($userId, $lang, $store);
        }

        if (!empty($defaultAddress)) {
            $deliveryCharges = $defaultAddress['shipping_cost'];
            $codCost = ($defaultAddress['is_cod_enable'] == 1) ? $defaultAddress['cod_cost'] : '0';
            $country = $defaultAddress['country_id'];
            $vatPct = $defaultAddress['vat'];
        } else {
            $deliveryCharges = $codCost = 0;
            $vatPct = 0;
        }

        $isFreeShipping = 0;
        if (!empty($promotionModel) && $promotionModel->shipping_included == 1) {
            if (empty($promotionModel->promotionCountries)) {
                $deliveryCharges = 0;
                $isFreeShipping = 1;
            } else {
                if (isset($country) && $country != "") {
                    $promotionCountry = \app\models\PromotionCountries::find()
                        ->where(['promotion_id' => $promotionModel->promotion_id, 'country_id' => $country])
                        ->one();
                    if (!empty($promotionCountry)) {
                        $deliveryCharges = 0;
                        $isFreeShipping = 1;
                    }
                }
            }
        }

        $subTotal = $total = $totalItems = 0;
        $hasPromotionBrandProduct = $hasPromotionShopProduct = [];
        $hasPromoBrand = $hasPromoShop = 0;
        //$discount = 0;
        $discountPrice = 0;

        if (isset($cartDetails['items'])) {
            foreach ($cartDetails['items'] as $item) {
                $subTotal += ($item['final_price'] * $item['quantity']);
                $totalItems += $item['quantity'];

                if (isset($promoFor) && !empty($promoFor)) {
                    if ($promoFor == 'F') {
                        $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                    }
                    if ($promoFor == 'P') {
                        $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                    } elseif ($promoFor == 'B') {
                        if (isset($item['brand_id']) && $item['brand_id'] != null) {
                            if (empty($promotionModel->promotionBrands)) {
                                $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } else {
                                $hasPromoBrand = 1;
                                $promoBrands = \app\models\PromotionBrands::find()
                                    ->where(['promotion_id' => $promotionModel->promotion_id, 'brand_id' => $item['brand_id']])
                                    ->one();
                                if (!empty($promoBrands)) {
                                    $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                    $hasPromotionBrandProduct[] = $item['brand_id'];
                                } else {
                                    $discountPrice = 0;
                                }
                            }
                        }
                    } elseif ($promoFor == 'S') {
                        if (isset($item['shop_id']) && $item['shop_id'] != null) {
                            if (empty($promotionModel->promotionShops)) {
                                $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } else {
                                $hasPromoShop = 1;
                                $promoShops = \app\models\PromotionShops::find()
                                    ->where(['promotion_id' => $promotionModel->promotion_id, 'shop_id' => $item['shop_id']])
                                    ->one();
                                if (!empty($promoShops)) {
                                    $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                    $hasPromotionShopProduct[] = $item['shop_id'];
                                } else {
                                    $discountPrice = 0;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($hasPromoBrand == 1 && empty($hasPromotionBrandProduct)) {
            return [
                'code' => 500,
                'message' => ($lang == "en") ? 'Promotion not applicable to selected brands.' : 'الترويج لا ينطبق على العلامات التجارية المختارة'
            ];
        }

        if ($hasPromoShop == 1 && empty($hasPromotionShopProduct)) {
            return [
                'code' => 500,
                'message' => ($lang == "en") ? 'Promotion not applicable to selected shops.' : 'الترويج لا ينطبق على المحلات المختارة'
            ];
        }

        if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
            $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
            if ($subTotal < $minimumOrderConverted) {
                return [
                    'code' => 500,
                    'message' => ($lang == "en") ? 'Minimum order problem.' : 'الحد الأدنى من مشكلة الطلب'
                ];
            }
        }

        return [
            'code' => 200,
            'message' => ($lang == "en") ? 'Success.' : 'النجاح'
        ];
    }

    public function actionAddUserReport($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = new \app\models\UserReport();
            //echo "<pre>";print_r($request);
            $model->user_id = $request['user_id'];
            $model->title   = $request['title'];
            // echo count($request['user_reports']['image']);die;
            if ($model->save(false)) {
                if (isset($request['user_reports']) && count($request['user_reports'])) {
                    \app\models\UserReportsImages::deleteAll(['report_id' => $model->report_id]);
                    foreach ($request['user_reports'] as $picture) {
                        $report_image = new \app\models\UserReportsImages();
                        $report_image->report_id = $model->report_id;
                        $image = base64_decode($picture['image']);
                        if ($image) {
                            $img = imagecreatefromstring($image);
                            if ($img !== false) {
                                $imageName = time() . '.png';
                                imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                                imagedestroy($img);
                                $report_image->image = $imageName;
                            }
                        }

                        if ($report_image->save(false)) {
                        }
                    }
                }

                $this->message = ($lang == "en") ?  'Report Created Successfully' : "تم إنشاء التقرير بنجاح";

                $report_images = [];
                if (!empty($model->userReportsImages)) {
                    foreach ($model->userReportsImages as $row) {
                        $d['report_id'] = $row->report_id;
                        $d['image_id'] = $row->user_reports_image_id;
                        $d['report_image'] = (!empty($row->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                        array_push($report_images, $d);
                    }
                }

                $this->data = [
                    'report_id' => $model->report_id,
                    'user_name' => (string)(!empty($model->user)) ? $model->user->first_name . ' ' . $model->user->last_name : '',
                    'title' => $model->title,
                    'title' => $model->title,
                    'images' => $report_images
                ];
            } else {
                $this->response_code = 500;
                $this->message = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionEditUserReport($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\UserReport::find()
                ->where(['report_id' => $request['report_id'], 'user_id' => $request['user_id'], 'is_deleted' => 0])->one();
            if (!empty($model)) {
                $model->user_id = $request['user_id'];
                $model->title   = $request['title'];

                if ($model->save(false)) {
                    if (isset($request['user_reports']) && count($request['user_reports'])) {
                        foreach ($request['user_reports'] as $picture) {
                            $report_image = new \app\models\UserReportsImages();
                            $report_image->report_id = $model->report_id;
                            $image = base64_decode($picture['image']);
                            if ($image) {
                                $img = imagecreatefromstring($image);
                                if ($img !== false) {
                                    $imageName = time() . '.png';
                                    imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                                    imagedestroy($img);
                                    $report_image->image = $imageName;
                                }
                            }

                            if ($report_image->save(false)) {
                            }
                        }
                    }

                    $this->message =  ($lang == "en") ?  'Report Created Successfully' : "تم إنشاء التقرير بنجاح";

                    $report_images = [];
                    if (!empty($model->userReportsImages)) {
                        foreach ($model->userReportsImages as $row) {
                            $d['report_id'] = $row->report_id;
                            $d['image_id'] = $row->user_reports_image_id;
                            $d['report_image'] = (!empty($row->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                            array_push($report_images, $d);
                        }
                    }

                    $this->data = [
                        'report_id' => $model->report_id,
                        'user_name' => (string)(!empty($model->user)) ? $model->user->first_name . ' ' . $model->user->last_name : '',
                        'title' => $model->title,
                        'images' => $report_images
                    ];
                } else {
                    $this->response_code = 500;
                    $this->message = $model->errors;
                }
            } else {
                $this->response_code = 500;
                $this->message = ($lang == "en") ? 'Requested report is not found' : 'التقرير المطلوب غير موجود';
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    public function actionUserReports($lang = 'en', $user_id = "")
    {
        $result = [];
        if ($user_id != '') {
            $model = \app\models\UserReport::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])->all();

            if (!empty($model)) {
                foreach ($model as $row) {
                    $report_images = [];
                    if (!empty($row->userReportsImages)) {
                        foreach ($row->userReportsImages as $img) {
                            $temp['report_id'] = $img->report_id;
                            $temp['image_id'] = $img->user_reports_image_id;
                            $temp['report_image'] = (!empty($img->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $img->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                            array_push($report_images, $temp);
                        }
                    }
                    $temp1['report_id'] = $row->report_id;
                    $temp1['user_name'] = (string)(!empty($row->user)) ? $row->user->first_name . ' ' . $row->user->last_name : '';
                    $temp1['title']     = $row->title;
                    $temp1['images']    = $report_images;

                    array_push($result, $temp1);
                }
                $this->data = $result;
                return $this->response_array();
            } else {
                $this->response_code = 200;
                $this->message = ($lang == "en") ? 'No reports found.' : 'لم يتم العثور على تقارير';
                $this->data = $result;
                return $this->response_array();
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'User id required' : 'معرف المستخدم مطلوب';
            $this->data = $result;
            return $this->response_array();
        }
    }

    public function actionDeleteUserReports($lang = 'en', $user_id = "", $report_id = '', $image_id = '')
    {
        if ($user_id != '') {
            $model = \app\models\UserReport::find()
                ->where(['user_id' => $user_id, 'report_id' => $report_id])->one();
            $result = [];
            if (!empty($model)) {
                if ($image_id == '') {
                    $model->is_deleted = 1;
                    $model->save(false);
                } elseif ($image_id != '') {
                    $modelImage = \app\models\UserReportsImages::find()
                        ->where(['report_id' => $report_id, 'user_reports_image_id' => $image_id])->one();
                    if (!empty($modelImage)) {
                        $modelImage->delete();
                    }
                }
                $this->data = new stdClass();
                $this->message = ($lang == "en") ?  'Report successfully deleted' : "تم حذف التقرير بنجاح";
                return $this->response_array();
            } else {
                $this->response_code = 200;
                $this->message = ($lang == "en") ? 'No reports found.' : 'لم يتم العثور على تقارير';
                $this->data = new stdClass();
                return $this->response_array();
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'Report id required' : 'معرّف التقرير مطلوب';
            $this->data = new stdClass();
            return $this->response_array();
        }
    }

    public function actionReportRequestList($lang = 'en', $user_id = '')
    {
        $model = \app\models\DoctorReportRequest::find()
            ->where(['user_id' => $user_id, 'status' => 'P'])
            ->orderBy(['doctor_report_request_id' => SORT_DESC])
            ->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $d['doctor_report_request_id']  = $row->doctor_report_request_id;
                $d['doctor_name']               = (!empty($row->doctorAppointment)) ? $row->doctorAppointment->doctor->{'name_' . $lang} : '';
                $d['doctor_request_for'] = $row->doctor_request_for;
                $d['request_date'] = $row->request_date;
                $d['status'] = $row->status;
                array_push($result, $d);
            }
        } else {
            $this->response_code = 200;
            $this->message = ($lang == "en") ? 'No report request found' : 'لم يتم العثور على طلب تقرير';
            $this->data = "";
            return $this->response_array();
        }
        $this->data = $result;
        return $this->response_array();
    }

    public function actionAssignReportRequest($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        $result = [];
        if (!empty($request)) {
            $doctor_report_request_id = $request['doctor_report_request_id'];

            $model = \app\models\DoctorReportRequest::find()
                ->where(['doctor_report_request_id' => $doctor_report_request_id])
                ->one();
            $is_approved = $request['is_approved'];
            $report_id   = $request['report_id'];
            if (!empty($model)) {
                if ($is_approved == 1) {
                    $model->report_id   = $report_id;
                    $model->status      = 'A';
                    $this->message = ($lang == "en") ? 'Report assigned successfully.' : "تم تعيين التقرير بنجاح";
                } elseif ($is_approved == 0) {
                    $model->report_id   = $report_id;
                    $model->status      = 'R';
                    $this->message = ($lang == "en") ? 'Report rejected successfully.' : 'تم رفض التقرير بنجاح';
                }
                $model->save();

                $modelList = \app\models\DoctorReportRequest::find()
                    ->where(['user_id' => $model->user_id, 'status' => 'P'])
                    ->orderBy(['doctor_report_request_id' => SORT_DESC])
                    ->all();
                $result = [];
                if (!empty($modelList)) {
                    foreach ($modelList as $row) {
                        $d2['doctor_report_request_id']  = $row->doctor_report_request_id;
                        $d2['doctor_name']               = (!empty($row->doctorAppointment)) ? $row->doctorAppointment->doctor->{'name_' . $lang} : '';
                        $d2['doctor_request_for'] = $row->doctor_request_for;
                        $d2['request_date'] = $row->request_date;
                        $d2['status'] = $row->status;
                        array_push($result, $d2);
                    }
                    $this->data = $result;
                } else {
                    $this->response_code = 200;
                    $this->data = "";
                    return $this->response_array();
                }
                /*$d['doctor_report_request_id'] = $model->doctor_report_request_id;
                $d['report_id']                = $model->report_id;
                $d['report_name']              = (!empty($model->userReport)) ? $model->userReport->title:'';
                $d['request_date'] = $model->request_date;
                array_push($result,$d);
                $this->data = $result;*/
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
        }
        return $this->response();
    }

    private function getTotalUesrPrescriptions($user_id)
    {
        $total_prescription = \app\models\DoctorPrescriptions::find()->where(['is_deleted' => 0, 'is_active' => 1, 'user_id' => $user_id])->count();
        return $total_prescription;
    }

    public function actionMarkNotification($lang = "en")
    {
        $request = Yii::$app->request->bodyParams;

        if (!empty($request)) {
            $notification_id = $request['notification_id'];
            $user_id = $request['user_id'];

            $notificationModel = Notifications::find()->where([
                "notification_id" => $notification_id,
                "user_id" => $user_id,
                "is_read" => 0
            ])->one();

            $notificationModelCount = 0;
            if (!empty($user_id)) {
                $notificationModelCount = Notifications::find()->where(['user_id' => $user_id, "is_read" => 0])->count();
            }

            if (!empty($notificationModel)) {
                $notificationModel->is_read = 1;
                $notificationModel->save();
                $this->message = ($lang == "en") ? 'Notification marked as read.' : 'تم وضع علامة على الإخطار كمقروء';

                $this->data = [
                    "notification_id" => $notificationModel->notification_id,
                    "title" => $notificationModel->title,
                    "message" => $notificationModel->message,
                    "user_id" => (!empty($notificationModel->user_id)) ? (string)$notificationModel->user_id : "",
                    "target" => (!empty($notificationModel->target)) ? (string)$notificationModel->target : "",
                    "target_id" => (!empty($notificationModel->target_id)) ? (string)$notificationModel->target_id : "",
                    "posted_date" => (!empty($notificationModel->posted_date)) ? (string)$notificationModel->posted_date : "",
                    "is_read" => (!empty($notificationModel->is_read)) ? (string)$notificationModel->is_read : "",
                    "un_read_notifications" => $notificationModelCount
                ];
            } else {
                $this->response_code = 405;
                $this->message = ($lang == "en") ? 'Notification not found!' : 'لم يتم العثور على الإخطار';
                $this->data = new stdClass();
            }
        } else {
            $this->response_code = 500;
            $this->message = ($lang == "en") ? 'There was an error processing the request. Please try again later.' : "كان هناك خطأ في معالجة الطلب. الرجاء معاودة المحاولة في وقت لاحق.";
            $this->data = new stdClass();
        }
        return $this->response();
    }

    public function actionNotificationList($target = "", $user_id = "", $lang = "en")
    {
        $userModel = "";
        if ($user_id != "") {
            $userModel = Users::find()->where(['user_id' => $user_id])->one();
            if ($target != '') {
                $model = \app\models\Notifications::find()
                    ->where(['target' => $target]);
            } else {
                $model = \app\models\Notifications::find()
                    ->andWhere(['IS', 'user_id', new \yii\db\Expression('NULL')]);
            }

            $model->orwhere(['user_id' => $user_id])->orderby(['notification_id' => SORT_DESC]);
            if (!empty($userModel)) {
                $model->andWhere(['>', 'posted_date', $userModel->create_date]);
            }
            $notification = $model->all();
            //echo $model->createCommand()->rawSql;die;
            $data = array();

            if (!empty($notification)) {
                foreach ($notification as $row) {
                    $d['notification_id'] = $row->notification_id;
                    $d['user_id'] = $row->user_id;
                    //$d['user_name'] = (!empty($row->user->name)) ? $row->user->name : $row->user->email;
                    $d['title'] = $row->title;
                    $d['messgae'] = strip_tags($row->message);
                    $d['target'] = $row->target;
                    $d['target_id'] = $row->target_id;
                    $d['posted_date'] = $row->posted_date;
                    $d['is_read'] = $row->is_read;
                    array_push($data, $d);
                }
                $this->response_code = 200;
                $this->message = ($lang == "en") ? "Notification list" : "قائمة الإخطارات";
                $this->data = [
                    'notification_list' => $data
                ];
            } else {
                $this->response_code = 200;
                $this->message = ($lang == "en") ? "No notification found" : "لم يتم العثور على إخطار";
                $this->data = [
                    'notification_list' => $data
                ];
            }
        } else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "User Id Required" : "معرف المستخدم مطلوب";
            $this->data = [
                'notification_list' => new stdClass()
            ];
        }

        return $this->response();
    }

    public function actionClinicCategory($lang = 'en')
    {
        //$model = \app\models\ClinicCategories::find()->all();
        $model = \app\models\Category::find()
            ->where(['type' => 'C'])
            ->all();
        $result = [];
        foreach ($model as $row) {
            if ($lang == 'ar') {
                $category_icon = !empty($row->icon_ar) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');;
            } else {
                $category_icon = !empty($row->icon) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->icon) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');;
            }

            $d['category_id'] = $row->category_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $d['category_icon'] = $category_icon;
            array_push($result, $d);
        }

        $this->data = $result;
        return $this->response();
    }

    public function actionGetReports($lang = 'en', $user_id = "", $type = "")
    {
        $result = [];
        if ($type == "") {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? 'Type required' : 'النوع مطلوب';
            $this->data = $result;
            return $this->response_array();
        }
        if ($type == 'D') {
            $query   = \app\models\DoctorAppointments::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['!=', 'uploaded_report', "NULL"])
                ->orderby(['report_upload_date' => SORT_DESC]);
            $model  = $query->all();
        } elseif ($type == 'L') {
            $query   = \app\models\LabAppointments::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['!=', 'uploaded_report', "NULL"]);
            $query->orderby(['report_upload_date' => SORT_DESC]);
            $model  = $query->all();
        }

        if (!empty($model)) {
            foreach ($model as $row) {
                $d['id'] = ($type == 'D') ? $row->doctor_appointment_id : $row->lab_appointment_id;
                $d['name'] = (string) ($type == 'D') ? $row->doctor->{'name_' . $lang} : $row->lab->{'name_' . $lang};
                $d['report_title'] = (string)$row->{'report_title_' . $lang};
                $d['upload_report_date'] = (string) $row->report_upload_date;
                $d['uploaded_report'] = (string) (!empty($row->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->uploaded_report) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                array_push($result, $d);
            }

            $this->response_code = 200;
            $this->message = ($lang == "en") ? "Report list" : 'قائمة التقارير';
        } else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "Report list not found" : "قائمة التقارير غير موجودة";
            $this->data = $result;
            return $this->response_array();
        }
        $this->data = $result;
        return $this->response_array();
    }

    public function actionGetClinicSpecialities($lang = 'en', $id = null)
    {
        $this->data = [];
        $this->message = ($lang == "en") ? "Success" : "نجاح";
        $model = Clinics::find()->where(['is_deleted' => 0, 'clinic_id' => $id, 'is_active' => 1])

            ->one();
        $catlist = array();
        // debugPrint($model->clinicCategories);
        // die;
        if (!empty($model)) {
            if (!empty($model->doctors)) {
                foreach ($model->doctors as $doc) {
                    if (!empty($doc->doctorCategories)) {
                        foreach ($doc->doctorCategories as $row) {
                            array_push($catlist, $row->category->category_id);
                        }
                    }
                }
            } else {
                $this->response_code = 201;
                $this->message = ($lang == "en") ? "No specialties found" : " لم يتم العثور على تخصصات";
                return $this->response_array();
            }
        }
        // if (!empty($model)) {
        //     if (!empty($model->clinicCategories)) {
        //         foreach($model->clinicCategories as $row){
        //             array_push($catlist, $row->category_id);
        //         }
        //     }
        // }
        else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "This clinic not found" : "لم يتم العثور على هذه العيادة";
            return $this->response_array();
        }
        $catlist = array_unique($catlist);
        // debugPrint($catlist);
        // die;
        if (!empty($catlist)) {
            foreach ($catlist as $cat) {
                $catModel = \app\models\Category::find()
                    ->where(['category_id' => $cat])
                    ->asArray()
                    ->one();
                if (!empty($catModel)) {
                    if ($lang == 'ar') {
                        $icon = ($catModel['icon_ar'] != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $catModel['icon_ar']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    } else {
                        $icon = ($catModel['icon'] != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $catModel['icon']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    }
                    $d['id'] = $catModel['category_id'];
                    $d['name'] = $catModel['name_' . $lang];
                    $d['icon'] = $icon;
                    $d['hide_category_in_app'] = $catModel['hide_category_in_app'];
                    $d['show_in_home'] = $catModel['show_in_home'];
                    array_push($this->data, $d);
                }
            }
        } else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "No specialties found" : " لم يتم العثور على تخصصات";
            return $this->response_array();
        }
        return $this->response();
    }

    public function actionGetPharmacyCategories($lang = 'en', $pharmacy_id = null)
    {
        $this->data = [];
        $this->message = ($lang == "en") ? "Success" : "نجاح";
        $model = Pharmacies::find()->where(['is_deleted' => 0, 'pharmacy_id' => $pharmacy_id, 'is_active' => 1])
            ->one();
        $catlist = array();
        if (!empty($model)) {
            if (!empty($model->products)) {
                foreach ($model->products as $pro) {
                    if ($pro->is_deleted == 0 && $pro->is_active == 1) {
                        if (!empty($pro->productCategories)) {
                            foreach ($pro->productCategories as $row) {
                                array_push($catlist, $row->category->category_id);
                            }
                        }
                    }
                }
            } else {
                $this->response_code = 201;
                $this->message = ($lang == "en") ? "No categories found" : "لم يتم العثور على فئات";
                return $this->response_array();
            }
        } else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "No categories found" : "لم يتم العثور على فئات";
            return $this->response_array();
        }
        $catlist = array_unique($catlist);
        if (!empty($catlist)) {
            foreach ($catlist as $cat) {
                $catModel = \app\models\Category::find()
                    ->where(['category_id' => $cat])
                    ->asArray()
                    ->one();
                if (!empty($catModel)) {
                    if ($lang == 'ar') {
                        $icon = ($catModel['icon_ar'] != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $catModel['icon_ar']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    } else {
                        $icon = ($catModel['icon'] != null) ? Yii::$app->urlManager->createAbsoluteUrl('/uploads/' . $catModel['icon']) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    }
                    $d['id'] = $catModel['category_id'];
                    $d['name'] = $catModel['name_' . $lang];
                    $d['icon'] = $icon;
                    $d['hide_category_in_app'] = $catModel['hide_category_in_app'];
                    $d['show_in_home'] = $catModel['show_in_home'];
                    array_push($this->data, $d);
                }
            }
        } else {
            $this->response_code = 201;
            $this->message = ($lang == "en") ? "No categories found" : "لم يتم العثور على فئات";
            return $this->response_array();
        }
        return $this->response();
    }




    public function actionDoctorAppointmentCronJobTest()
    {
        date_default_timezone_set('Asia/Kuwait');
        $today = date('Y-m-d H:i:s');
        // echo $today;
        // die;
        $doctorAppointmentModel = DoctorAppointments::find()
            ->where([
                'is_call_initiated' => 0,
                'is_completed' => 0,
                'is_cancelled' => 0,
                'not_show' => 0,
            ])
            ->andWhere(['>=', 'appointment_datetime', $today])
            ->andWhere(['<=', 'appointment_datetime', date('Y-m-d H:i:s', strtotime('+ 15 minute'))])
            ->with(['user', 'doctor']);
        $query = $doctorAppointmentModel->all();
        $data = [];
        foreach ($query as $row) {
            // if ($row['appointment_datetime'] >= date('Y-m-d H:i:s', strtotime('+15 minutes')) && $row['appointment_datetime'] <= date('Y-m-d H:i:s', strtotime('+20 minutes'))) {
            $user_device_token = $row->user->device_token;
            if ($user_device_token != "") {
                $title  = "You have an appointment.";
                $msg = "Your scheduled appointment #" . $row['appointment_number'] . " is about to start in 15 mins.";
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $row->user->user_id;
                $notification->target   = "LA";
                $notification->target_id = $row['doctor_appointment_id'];
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, "LA", $row['doctor_appointment_id'], $title, '', $row['doctor']['name_en'], $row['doctor']['name_ar']);
            }
            // }
            array_push($data, $row);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
        // return $query;
    }

    public function actionLabAppointmentCronJobTest()
    {
        date_default_timezone_set('Asia/Kuwait');
        $today = date('Y-m-d h:i:s');
        $doctorAppointmentModel = LabAppointments::find()
            ->where([
                'is_completed' => 0,
                'is_cancelled' => 0,
                'not_show' => 0,
            ])
            ->andWhere(['>=', 'appointment_datetime', $today])
            ->andWhere(['<=', 'appointment_datetime', date('Y-m-d h:i:s', strtotime('+ 15 minute'))])
            ->with(['user', 'lab']);
        $query = $doctorAppointmentModel->all();
        $count = $doctorAppointmentModel->count();
        $data = [];
        foreach ($query as $row) {
            if ($row['appointment_datetime'] >= date('Y-m-d H:i:s', strtotime('+15 minutes')) && $row['appointment_datetime'] <= date('Y-m-d H:i:s', strtotime('+20 minutes'))) {
                $user_device_token = $row->user->device_token;
                $title  = "You have an Lab appointment.";
                $msg = "Your scheduled appointment #" . $row['appointment_number'] . " is about to start in 15 mins.";
                date_default_timezone_set(Yii::$app->params['timezone']);
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $row->user->user_id;
                $notification->target   = "LA";
                $notification->target_id = $row['lab_appointment_id'];
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                // \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, "LA", $row['lab_appointment_id'], $title, '', $row['lab']['name_en'], $row['lab']['name_ar']);

            }
            array_push($data, $row);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }


    public function actionRunQuery($query)
    {
        if ($query != '') {
            $dbCommand = Yii::$app->db->createCommand("$query");
            $model = $dbCommand->queryAll();
            return 'True';
        } else {
            echo "Please enter query";
        }
    }
}
