<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

use app\models\AddOns;
use app\models\BrowserNotification;
use app\models\AssociatedProducts;
use app\models\Block;
use app\models\Category;
use app\models\Clinics;
use app\models\Doctors;
use app\models\Labs;
use app\models\Pharmacies;
use app\models\Tests;
use app\models\Symptoms;
use app\models\NotifyUsers;
use app\models\OrderItems;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Payment;
use app\models\Product;
use app\models\Insurances;
use app\models\ProductAttributeValues;
use app\models\Settings;
use app\models\ShopOrders;
use app\models\Shops;
use app\models\ShopWarehouse;
use app\models\State;
use app\models\Status;
use app\models\Users;
use app\models\WrappingCategories;
use app\models\WrappingCategoryItems;
use app\models\Wrappings;
use Yii;
use app\models\Area;
use app\models\Currencies;
use yii\helpers\ArrayHelper;
use app\models\Attributes;
use app\models\Country;
use app\models\DoctorAppointments;
use app\models\Translator;
use yii\helpers\Url;

/**
 * Description of AppHelper
 *
 * @author Akram Hossain <akram_cse@yahoo.com>
 */
class AppHelper
{
    public static function pushTargetList()
    {
        return [
            //'CL' => 'Category',
            'P' => 'Product',
            'BR' => 'Brand'
        ];
    }



    public static $payment_mode = [
        'K' => 'K-net',
        'C' => 'Cash on Delivery',
        'CC' => 'Credit Card',
        /*'W' => 'Wallet',
        'M' => 'MyFatoorah',
        'AE' => 'AMEX',
        'S' => 'Sadad',
        'B' => 'Benefit',
        'NP' => 'Qatar Debit Cards',
        'MD' => 'MADA',
        'KF' => 'Kfast',
        'AP' => 'Apple Pay',
        'AF' => 'AFS',
        'STC' => 'STC Pay',
        'UAECC' => 'UAE Debit Cards',*/
    ];

    public static function getPaymodeType($paymode)
    {
        if ($paymode == 'K') {
            return 'K-Net';
        } elseif ($paymode == 'C') {
            return 'Pay Cash At Clinic/Lab';
        } elseif ($paymode == 'CC') {
            return 'Visa Card';
        }
    }

    public static function getAdminCommission($order_id)
    {
        $orderItemModel = OrderItems::find()->where(['order_id' => $order_id])->asArray()->all();
        $commission = 0;
        if (!empty($orderItemModel)) {
            $itemCount = sizeof($orderItemModel);

            foreach ($orderItemModel as $item) {
                $commission = +$item['commission_percent'];
            }
            $commission = $commission / $itemCount;
        }
        return $commission;
    }

    public static function getOrderStatus($status_id)
    {
        $statusModel = Status::find()->where(['status_id' => $status_id])->asArray()->one();
        return $statusModel['name_en'];
    }

    public static function getSettings()
    {
        $cache = \Yii::$app->cache;
        $settings = $cache->get('settings');

        if ($settings === false) {
            $settings = \app\models\Settings::find()->asArray()->one();
            $cache->set('settings', $settings);
        }

        return $settings;
    }

    public static function getAttributeList()
    {
        $model = Attributes::find()
            ->orderBy(['code' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'attribute_id', 'name_en');

        return $list;
    }






    public static function getClinicsAndHospitalList()
    {
        $query = Clinics::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['clinic_id' => SORT_ASC]);
        if (Yii::$app->session['_eyadatAuth'] == 2) {
            $query->andwhere(
                ['=', 'clinic_id', Yii::$app->user->identity->clinic_id]
            );
        }
        $model = $query->all();
        $list = ArrayHelper::map($model, 'clinic_id', 'name_en');

        return $list;
    }
    public static function getClinicsList()
    {
        $query = Clinics::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['clinic_id' => SORT_ASC]);
        if (Yii::$app->session['_eyadatAuth'] == 2) {
            $query->andwhere(
                ['=', 'clinic_id', Yii::$app->user->identity->clinic_id]
            );
        }
        $query->andwhere(['=', 'type', "C"]);
        $model = $query->all();
        $list = ArrayHelper::map($model, 'clinic_id', 'name_en');

        return $list;
    }
    public static function getHospitalList()
    {
        $query = Clinics::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['clinic_id' => SORT_ASC]);
        if (Yii::$app->session['_eyadatAuth'] == 2) {
            $query->andwhere(
                [
                    ['=', 'clinic_id', Yii::$app->user->identity->clinic_id],
                    ['=', 'type', "H"]
                ]
            );
        }
        // $query->andwhere(['=', 'type', "H"]);
        $model = $query->all();
        $list = ArrayHelper::map($model, 'clinic_id', 'name_en');
        return $list;
    }





    public static function getTranslatorList()
    {
        $model = \app\models\Translator::find()

            ->where(['is_deleted' => 0])
            ->where(['is_active' => 1])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();
        $list = ArrayHelper::map($model, 'translator_id', 'name_en');
        return $list;
    }

    public static function getDoctorsList()
    {
        $query = Doctors::find()
            ->where(['is_deleted' => 0]);
        if (Yii::$app->session['_eyadatAuth'] == 3) {
            $query->andwhere(['doctor_id' => Yii::$app->user->identity->doctor_id]);
        }
        $query->orderBy(['doctor_id' => SORT_ASC]);
        $model = $query->all();

        $list = ArrayHelper::map($model, 'doctor_id', 'name_en');

        return $list;
    }

    public static function getLabsList()
    {
        $model = Labs::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['lab_id' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'lab_id', 'name_en');

        return $list;
    }

    public static function getPharmacyList()
    {
        $model = Pharmacies::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['pharmacy_id' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'pharmacy_id', 'name_en');

        return $list;
    }

    public static function getCountryList()
    {
        $model = Country::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['nicename' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'country_id', 'nicename');

        return $list;
    }

    public static function getStateList()
    {
        $model = State::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'state_id', 'name_en');

        return $list;
    }

    public static function getStoreList()
    {
        $model = \app\models\Stores::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'store_id', 'name_en');

        return $list;
    }

    public static function getInsuranceList()
    {
        $model = \app\models\Insurances::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'insurance_id', 'name_en');

        return $list;
    }

    public static function getCategoryList($type)
    {
        $model = \app\models\Category::find()
            ->where(['is_deleted' => 0, 'type' => $type])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'category_id', 'name_en');

        return $list;
    }

    public static function getServicesList($type)
    {
        $model = \app\models\Services::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'service_id', 'name_en');

        return $list;
    }

    public static function getTestsList($type)
    {
        $model = \app\models\Tests::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'test_id', 'name_en');

        return $list;
    }

    public static function getSymptomsList()
    {
        $model = Symptoms::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['symptom_id' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'symptom_id', 'name_en');

        return $list;
    }

    public static function getCurrencyList()
    {
        $model = \app\models\Currencies::find()->all();

        $list = ArrayHelper::map($model, 'currency_id', 'name_en');

        return $list;
    }

    public static function getRecursiveCategory($type = 'P')
    {
        $roots = \app\models\Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'type' => $type])
            ->roots()
            ->all();

        $result = [];
        foreach ($roots as $row) {
            $d['category_id'] = $row->category_id;
            $d['name'] = $row->name_en;
            $d['children'] = self::getChildCategory($row);
            array_push($result, $d);
        }
        //$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($result));
        $nonested = self::makeNonNestedArray($result);
        $list = \yii\helpers\ArrayHelper::map($nonested, 'id', 'name');
        return $list;
    }

    public static function makeNonNestedArray($data, $pass = 0)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $d = [
                'id' => $value['category_id'],
                'name' => $value['name'],
            ];
            array_push($result, $d);

            //debugPrint($result);
            if (array_key_exists('children', $value)) {
                $nested = self::makeNonNestedArray($value['children'], $pass);
                if (!empty($nested)) {
                    $count = $pass + 1;
                    foreach ($nested as $n) {
                        $n = [
                            'id' => $n['id'],
                            'name' => str_repeat("--", $count) . $n['name']
                        ];
                        array_push($result, $n);
                    }
                }
            }
        }
        return $result;
    }

    public static function getChildCategory($parent)
    {
        $children = $parent->children(1)->all();
        $result = [];
        foreach ($children as $row) {
            if ($row->is_deleted == 0) {
                $d['category_id'] = $row->category_id;
                $d['name'] = $row->name_en;
                $count = $row->children()->count();
                if ($count > 0) {
                    $d['children'] = self::getChildCategory($row);
                }
                array_push($result, $d);
            }
        }
        return $result;
    }

    public static function getStates()
    {
        $model = \app\models\State::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'state_id', 'name_en');

        return $list;
    }

    public static function getStatesByCountry($countryId)
    {
        $model = \app\models\State::find()
            ->where(['is_deleted' => 0, 'country_id' => $countryId])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'state_id', 'name_en');

        return $list;
    }

    public static function getAreaByState($state_id)
    {
        $model = \app\models\Area::find()
            ->where(['is_deleted' => 0, 'state_id' => $state_id])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'area_id', 'name_en');

        return $list;
    }

    public static function countFeedbackRequest()
    {
        $count = \app\models\Feedback::find()->where(['is_deleted' => 0])->count();
        return $count;
    }

    public static function countUpcomingAppointment($user_id = '')
    {
        $today_datetime = date('Y-m-d H:i:s');
        $count = $query = \app\models\DoctorAppointments::find()
            ->join('LEFT JOIN', 'payment', 'payment.type_id = doctor_appointments.doctor_appointment_id')
            ->andwhere(['doctor_appointments.is_deleted' => 0, 'doctor_appointments.is_paid' => 1, 'doctor_appointments.user_id' => $user_id, 'payment.result' => 'CAPTURED', 'doctor_appointments.is_completed' => 0])
            ->andWhere(['>=', 'doctor_appointments.appointment_datetime', $today_datetime])
            ->count();
        return $count;
    }

    public static function getAllUser()
    {
        $model = \app\models\Users::find()
            ->select(['user_id', 'CONCAT(`first_name`, " ", `last_name`) AS first_name'])
            ->where(['is_deleted' => 0])
            ->orderBy(['first_name' => SORT_ASC])
            ->all();
        $list = ArrayHelper::map($model, 'user_id', 'first_name');
        return $list;
    }
    public static function getAllKids()
    {
        $model = \app\models\Kids::find()
            ->select(['kid_id', 'name_en'])
            ->where(['is_deleted' => 0])
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'kid_id', 'name_en');
        return array_unique($list);
    }

    public static function getAllUserEmail()
    {
        $model = \app\models\Users::find()
            ->where(['is_deleted' => 0])
            ->all();
        $list = ArrayHelper::map($model, 'user_id', 'email');
        return $list;
    }

    public static function getSelectedInsuranceIds($ids, $type)
    {
        if ($type == 'C') {
            $model = \app\models\ClinicInsurances::find()->where(['clinic_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        } elseif ($type == 'D') {
            $model = \app\models\DoctorInsurances::find()->where(['doctor_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        } elseif ($type == 'L') {
            $model = \app\models\LabInsurances::find()->where(['lab_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        } elseif ($type == 'P') {
            $model = \app\models\ProductInsurances::find()->where(['product_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        } elseif ($type == 'F') {
            $model = \app\models\PharmacyInsurances::find()->where(['pharmacy_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        } elseif ($type == 'T') {
            $model = \app\models\DoctorInsurances::find()->where(['doctor_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'insurance_id', 'insurance_id');
        }
        return $list;
    }

    public static function getSelectedCategoriesIds($ids, $type)
    {
        if ($type == 'C') {
            $model = \app\models\ClinicCategories::find()->where(['clinic_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        } elseif ($type == 'D') {
            $model = \app\models\DoctorCategories::find()->where(['doctor_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        } elseif ($type == 'L') {
            $model = \app\models\DoctorCategories::find()->where(['doctor_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        } elseif ($type == 'P') {
            $model = \app\models\ProductCategories::find()->where(['product_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        } elseif ($type == 'F') {
            $model = \app\models\PharmacyCategories::find()->where(['pharmacy_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        } elseif ($type == 'T') {
            $model = \app\models\TestCategories::find()->where(['test_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'category_id', 'category_id');
        }
        return $list;
    }

    public static function getSelectedTestsIds($ids)
    {
        $model = \app\models\LabTests::find()->where(['lab_id' => $ids])->all();
        $list = ArrayHelper::map($model, 'test_id', 'test_id');
        return $list;
    }

    public static function getSelectedServicesIds($ids, $type)
    {
        if ($type == 'L') {
            $model = \app\models\LabServices::find()->where(['lab_id' => $ids])->all();
            $list = ArrayHelper::map($model, 'service_id', 'service_id');
        }
        return $list;
    }

    public static function getSelectedSymptomsIds($ids)
    {
        $model = \app\models\DoctorSymptoms::find()->where(['doctor_id' => $ids])->all();
        $list = ArrayHelper::map($model, 'symptom_id', 'symptom_id');

        return $list;
    }

    public static function getBlockNameById($block_id, $lang)
    {
        $block_name = '';
        $blockModel = \app\models\Block::find()->where(['block_id' => $block_id])->one();
        if (!empty($blockModel)) {
            $block_name = $blockModel->{'name_' . $lang};
        }
        return $block_name;
    }

    public static function paymentTypes($lang, $accepted_payment_method = "")
    {
        $knet = [
            'type' => ($lang == 'en') ? 'K-Net' : "كي نت",
            'code' => 'K',
            'success_url' => 'http://www.3eyadat.com',
            'fail_url' => 'http://www.3eyadat.com',
            'is_enable' => 1,
            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/knet.png')
        ];
        $cc  = [
            'type' => ($lang == 'en') ? 'Visa/MasterCard' : 'فيزا / ماستر كارد',
            'code' => 'CC',
            'success_url' => 'http://www.3eyadat.com',
            'fail_url' => 'http://www.3eyadat.com',
            'is_enable' => 1,
            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/visa.png')
        ];

        $cod = [
            'type' => ($lang == 'en') ? 'Cash On Delivery' : 'الدفع عند الاستلام',
            'code' => 'C',
            'success_url' => '',
            'fail_url' => '',
            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/cod.png'),
            'is_enable' => 1,
        ];
        $accepted_method = explode(',', $accepted_payment_method);
        $return_paymode = [];
        if (!empty($accepted_payment_method)) {
            if (in_array("K", $accepted_method)) {
                array_push($return_paymode, $knet);
            }

            if (in_array("CC", $accepted_method)) {
                array_push($return_paymode, $cc);
            }

            if (in_array("C", $accepted_method)) {
                array_push($return_paymode, $cod);
            }
        } else {
            array_push($return_paymode, $knet);
            array_push($return_paymode, $cc);
            array_push($return_paymode, $cod);
        }
        $data = $return_paymode;


        return $data;
    }

    public static function getUploadUrl()
    {
        if (Yii::$app->params['is_enable_cdn']) {
            return Yii::$app->params['cdn_url'];
        } else {
            return Url::to(['/uploads'], true) . '/';
        }
    }

    public static function resize($source_image, $destination, $tn_w, $tn_h, $quality = 100)
    {
        $info = getimagesize($source_image);
        $imgtype = image_type_to_mime_type($info[2]);

        #assuming the mime type is correct
        switch ($imgtype) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($source_image);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($source_image);
                break;
            case 'image/png':
                $source = imagecreatefrompng($source_image);
                break;
            default:
                die('Invalid image type.');
        }

        #Figure out the dimensions of the image and the dimensions of the desired thumbnail
        $src_w = imagesx($source);
        $src_h = imagesy($source);

        #Do some math to figure out which way we'll need to crop the image
        #to get it proportional to the new size, then crop or adjust as needed

        $x_ratio = $tn_w / $src_w;
        $y_ratio = $tn_h / $src_h;

        //if (($src_w <= $tn_w) && ($src_h <= $tn_h)) {
        /* $new_w = $src_w;
          $new_h = $src_h; */
        /* } elseif (($x_ratio * $src_h) < $tn_h) {
          $new_h = ceil($x_ratio * $src_h);
          $new_w = $tn_w;
          } else {
          $new_w = ceil($y_ratio * $src_w);
          $new_h = $tn_h;
          } */

        $scale = ($x_ratio < $y_ratio) ? $x_ratio : $y_ratio;
        $new_w = $src_w * $scale;
        $new_h = $src_h * $scale;

        $newpic = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

        //$white = imagecolorallocate($newpic, 255, 255, 255);
        //imagefill($newpic, 0, 0, $white);

        $final = imagecreatetruecolor($tn_w, $tn_h);
        $backgroundColor = imagecolorallocate($final, 255, 255, 255);
        imagefill($final, 0, 0, $backgroundColor);
        // //imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($tn_w / 2)), ($y_mid - ($tn_h / 2)), $tn_w, $tn_h, $tn_w, $tn_h);
        imagecopy($final, $newpic, (($tn_w - $new_w) / 2), (($tn_h - $new_h) / 2), 0, 0, $new_w, $new_h);

        if (imagejpeg($final, $destination, $quality)) {
            return true;
        }
        return false;
    }

    public static function generateBarCodeTemplate($model)
    {
        ob_start();
        /* ---------------------data-------------------- */
        $barcodeTitle = (strlen($model->name_en) > 18) ? substr($model->name_en, 0, 17) . '...' : $model->name_en;
        $finalPrice = 'KD ' . number_format($model->final_price, 3);
        $productAttributeValues = $model->productAttributeValues;
        //debugPrint($productAttributeValues); exit;
        $attributeCanvasStr = '';
        if (!empty($productAttributeValues)) {
            foreach ($productAttributeValues as $row) {
                $attributeCanvasStr .= $row->attributeValue->attribute0->name_en . ' : ' . $row->attributeValue->value_en . '  ';
            }
        }
        $attributeCanvasStr = trim($attributeCanvasStr);
        $barcodeText = $model->barcode;
        $barcodeImage = Yii::getAlias('@webroot') . '/uploads/barcode-' . $model->barcode . '.jpg';
        if (!file_exists($barcodeImage)) {
            return '';
        }
        /* --------------------------------------------- */

        $canvasWidth = 430.8;
        $canvasHeight = 283.5;
        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        $heightIndex = 70;
        // set background to white
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        $fontBold = Yii::getAlias('@webroot') . '/fonts/opensans-bold.ttf';
        $fontRegular = Yii::getAlias('@webroot') . '/fonts/opensans-regular.ttf';

        $text_color = imagecolorallocate($canvas, 0, 0, 0);
        /* -------------------barcodetitle--------------- */
        $text_box = imagettfbbox(24, 0, $fontBold, $barcodeTitle);
        // Get your Text Width and Height
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];
        // Calculate coordinates of the text
        $x = ($canvasWidth / 2) - ($text_width / 2); //hz centered
        //$y = ($canvasHeight/2) - ($text_height/2);//vertically centered
        imagettftext($canvas, 24, 0, $x, $heightIndex, $text_color, $fontBold, $barcodeTitle);

        /* -------------------final price--------------- */
        $heightIndex += 40;
        $text_box = imagettfbbox(18, 0, $fontRegular, $finalPrice);
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];
        $x = ($canvasWidth / 2) - ($text_width / 2); //hz centered
        imagettftext($canvas, 18, 0, $x, $heightIndex, $text_color, $fontBold, $finalPrice);

        /* -------------------attributes--------------- */
        $heightIndex += 35;
        $text_box = imagettfbbox(14, 0, $fontRegular, $attributeCanvasStr);
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];
        $x = ($canvasWidth / 2) - ($text_width / 2); //hz centered
        imagettftext($canvas, 14, 0, $x, $heightIndex, $text_color, $fontBold, $attributeCanvasStr);

        /* -------------------barcode image--------------- */
        $heightIndex += 20;
        //$barcodeImage = \yii\helpers\Url::to(['uploads/barcode-' . $model->barcode . '.png'], true);

        /* $data  = file_get_contents($barcodeImage);
          $size_info2 = getimagesizefromstring($data);
          debugPrint($size_info2); exit; */
        list($width, $height) = getimagesize($barcodeImage);
        $percent = 1;
        if ($width > $canvasWidth) {
            $percent = ($canvasWidth / $width);
        }
        $new_width = ($width > $canvasWidth) ? $width * $percent : $width;
        //echo $width.': ' .$percent; exit;
        $new_height = $height * $percent;
        $marge_right = ($canvasWidth - $new_width) / 2;

        //echo $new_width; exit;
        //$barcode = imagecreatefrompng($barcodeImage);
        $barcode = imagecreatefromjpeg($barcodeImage);
        //imageCopy($canvas, $barcode, 10, $heightIndex, 0, 0, 125, 20);
        imagecopyresampled($canvas, $barcode, $marge_right, $heightIndex, 0, 0, $new_width, $new_height, $width, $height);

        /* -------------------barcode text--------------- */

        $heightIndex += $height + 25;
        $text_box = imagettfbbox(16, 0, $fontBold, $barcodeText);
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];
        $x = ($canvasWidth / 2) - ($text_width / 2); //hz centered
        imagettftext($canvas, 16, 0, $x, $heightIndex, $text_color, $fontBold, $barcodeText);

        /* -------------------Output and free from memory--------------- */
        header('Content-Type: image/jpeg');
        imagejpeg($canvas, null, 100); //bigger image
        //scaled image
        $canvasScaled = imagecreatetruecolor($canvasWidth / 3, $canvasHeight / 3);
        imagecopyresampled($canvasScaled, $canvas, 0, 0, 0, 0, $canvasWidth / 3, $canvasHeight / 3, $canvasWidth, $canvasHeight);
        //imagejpeg($canvasScaled, NULL, 100);

        $rawImageBytes = ob_get_clean();

        $file_name = cleanBarcodeName($model->name_en) . '-' . $model->barcode . '.jpg';
        $path = Yii::getAlias('@webroot') . '/barcode-templates/' . $file_name;

        if (file_exists($path)) {
            unlink($path);
        }
        //imagejpeg($canvas, $path, 100);//store bigger image
        @imagejpeg($canvasScaled, $path, 100); //store scaled image
        //AppHelper::resize($path, $path, $canvasWidth/3, $canvasHeight/3, 100);

        return $rawImageBytes;
    }

    public static function getAssociatedProductsDataProvider($id)
    {
        $query = \app\models\Product::find()
            ->select(['product.*'])
            ->join('LEFT JOIN', 'associated_products', 'product.product_id = associated_products.child_id')
            ->where(['is_deleted' => 0])
            ->andFilterWhere([
                'OR',
                ['=', 'associated_products.parent_id', $id],
                ['=', 'product.product_id', $id]
            ])
            ->orderBy('(CASE WHEN product.product_id = ' . $id . ' THEN 1 ELSE 0 END) DESC');

        return $query;
    }

    public static function adjustStock($product, $quantity, $message)
    {
        $productStockModel = new \app\models\ProductStocks();
        $productStockModel->product_id = $product;
        $productStockModel->quantity = $quantity;
        $productStockModel->message = $message;
        $productStockModel->created_date = date('Y-m-d H:i:s');
        $productStockModel->save(false);
    }

    public static function checkShopOrderNumber($number, $count)
    {
        $pharmaOrderNumber = $number . strtoupper(self::toAlpha($count));
        $count = \app\models\PharmacyOrders::find()->where(['order_number' => $pharmaOrderNumber])->count();
        if ($count > 0) {
            while (true) {
                $pharmaOrderNumber = $number . strtoupper(self::toAlpha($count++));
                $count = \app\models\PharmacyOrders::find()->where(['order_number' => $pharmaOrderNumber])->count();

                if ($count <= 0) {
                    break;
                }
            }
        }
        return $pharmaOrderNumber;
    }

    public static function toAlpha($data)
    {
        $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $alpha_flip = array_flip($alphabet);
        if ($data <= 25) {
            return $alphabet[$data];
        } elseif ($data > 25) {
            $dividend = ($data + 1);
            $alpha = '';
            while ($dividend > 0) {
                $modulo = ($dividend - 1) % 26;
                $alpha = $alphabet[$modulo] . $alpha;
                $dividend = floor((($dividend - $modulo) / 26));
            }
            return $alpha;
        }
    }

    public static function getNextOrderNumber()
    {
        $startsWith = 1;
        $order = \app\models\Orders::find()
            ->select(['MAX(`order_number`) AS order_number'])
            ->where("order_number LIKE '$startsWith%'")
            ->asArray()
            ->one();

        if (!empty($order) && isset($order['order_number']) && $order['order_number'] != 0) {
            return $order['order_number'] + 1;
        } else {
            return 100000001;
        }
    }

    public static function getDeliveryOptions($lang, $country = '', $store = '', $subtotal = '', $totalItems = '')
    {
        $countryDetails = Country::findOne($country);
        $setting = \app\models\Settings::find()->one();
        $result = [];

        if (!empty($countryDetails->shipping_cost)) {
            if ($countryDetails->free_delivery_limit != "") {
                $freeDeliveryLimit = self::convertPriceV2($countryDetails->free_delivery_limit, 82, $store['currency_id']);
                if (isset($subtotal) && $subtotal > $freeDeliveryLimit) {
                    $price = '0';
                    $priceKw = '0';
                } else {
                    $price = (!empty($countryDetails->shipping_cost)) ? (string) self::convertPriceV2($countryDetails->shipping_cost, 82, $store['currency_id']) : '0';
                    $priceKw = (!empty($countryDetails->shipping_cost)) ? (string) $countryDetails->shipping_cost : '0';
                }
            } elseif (!empty($totalItems) && $totalItems > $countryDetails->standard_delivery_items && $countryDetails->standard_delivery_charge > 0) {
                $price = (!empty($countryDetails->shipping_cost)) ? $countryDetails->shipping_cost : '0';
                $price += ($price * $countryDetails->standard_delivery_charge) / 100;
                $price = (string) self::convertPriceV2($price, 82, $store['currency_id']);
                $priceKw = (string) $price;
            } else {
                $price = (!empty($countryDetails->shipping_cost)) ? (string) self::convertPriceV2($countryDetails->shipping_cost, 82, $store['currency_id']) : '0';
                $priceKw = (!empty($countryDetails->shipping_cost)) ? (string) $countryDetails->shipping_cost : '0';
            }
            $result[] = [
                'id' => 1,
                'name' => ($lang == 'en') ? 'Standard Delivery' : 'التوصيل القياسية',
                'price' => $price,
                'price_kw' => $priceKw,
                'days' => ($countryDetails->delivery_interval != "") ? $countryDetails->delivery_interval : $setting->delivery_interval,
            ];
        }

        if (!empty($countryDetails->express_shipping_cost)) {
            $currentTime = new \DateTime(date("H:i:s"));
            $currentTime->setTimezone(new \DateTimeZone("UTC"));
            if ($currentTime->format("H:i:s") >= Yii::$app->params['express_delivery_start_gmt'] && $currentTime->format("H:i:s") <= Yii::$app->params['express_delivery_end_gmt']) {
                $result[] = [
                    'id' => 2,
                    'name' => ($lang == 'en') ? 'Express Delivery' : 'التوصيل سريع',
                    'price' => (!empty($countryDetails->express_shipping_cost)) ? (string) self::convertPriceV2($countryDetails->express_shipping_cost, 82, $store['currency_id']) : '0',
                    'price_kw' => (!empty($countryDetails->express_shipping_cost)) ? (string) $countryDetails->express_shipping_cost : '0',
                    'days' => ($countryDetails->express_delivery_interval != "") ? $countryDetails->express_delivery_interval : $setting->express_delivery_interval
                ];
            }
        }

        return $result;
    }

    public static function convertPriceV2($price, $productCurrency, $storeCurrency)
    {
        if (empty($price)) {
            return 0;
        }
        $ceil = ($storeCurrency == 82 || $storeCurrency == 15) ? 0 : 1; //No ceil for KWD and BHD
        if ($productCurrency == $storeCurrency) {
            return (string) (($ceil) ? ceil($price) : $price);
        }
        $storeCurrencyRate = \app\models\Currencies::getDb()->cache(function ($db) use ($storeCurrency) {
            return \app\models\Currencies::find()->where(['currency_id' => $storeCurrency])->asArray()->one();
        });
        $baseCurrencyRate = \app\models\Currencies::getDb()->cache(function ($db) use ($productCurrency) {
            return \app\models\Currencies::find()->where(['currency_id' => $productCurrency])->asArray()->one();
        });
        $decimals = 2;
        if ($storeCurrencyRate['code'] == 'BHD' || $storeCurrencyRate['code'] == 'KWD') {
            $decimals = 3;
        }
        if ($ceil) {
            $price = ($storeCurrencyRate['currency_rate'] / $baseCurrencyRate['currency_rate']) * $price;
            return (string) round(ceil($price), $decimals);
        } else {
            $price = ($storeCurrencyRate['currency_rate'] / $baseCurrencyRate['currency_rate']) * $price;
            return (string) round(round($price, 0), $decimals);
        }
    }

    public static function formatPrice($amount, $currencyCode = null)
    {
        $decimals = 2;
        if ($currencyCode == 'BHD' || $currencyCode == 'KWD') {
            $decimals = 3;
        }

        return number_format($amount, $decimals);
    }

    public static function totalOrderStatus($status)
    {
        $countQuery = Orders::find();
        if (\Yii::$app->session['_eyadatAuth'] == 1) {
            $countQuery->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp', 'temp.order_id = orders.order_id');
            $countQuery->where(['=', 'temp.status_id', $status]);
        } elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
            $totalBillSql = 'SUM(order_items.price*order_items.quantity) as total_bill';
            $countQuery = Orders::find()
                ->select([
                    'orders.order_id',
                    'orders.order_number',
                    'orders.is_contacted',
                    'orders.recipient_name',
                    'orders.device_type',
                    'orders.payment_mode',
                    'orders.create_date',
                    'orders.delivery_option_id',
                    'orders.user_id',
                    'SUM(order_items.price*order_items.quantity) AS total_amount',
                    'CONCAT(shipping_addresses.first_name, " ", shipping_addresses.last_name) AS user_name',
                    'currencies.code_en As currency_code', 'orders.shipping_address_id', 'orders.shipping_area_id',
                    'orders.delivery_charge',
                    'orders.cod_charge',
                    'orders.vat_charges',
                    $totalBillSql,
                    'SUM((order_items.price * order_items.quantity * pharmacy_orders.pharmacy_commission) / 100) AS admin_commission',
                ])
                ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'users', 'orders.user_id = users.user_id')
                ->join('left join', 'shipping_addresses', 'orders.shipping_address_id = shipping_addresses.shipping_address_id')
                ->join('LEFT JOIN', 'currencies', 'currencies.currency_id = order_items.currency_id')
                ->where(['is_processed' => 1]);

            $countQuery->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp', 'temp.order_id = orders.order_id');

            $countQuery->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM pharmacy_order_status AS t1
                                        LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.pharmacy_order_status_id < t2.pharmacy_order_status_id))
                                        WHERE t2.pharmacy_order_id IS NULL
                                        ) as temp2', 'pharmacy_orders.pharmacy_order_id = temp2.pharmacy_order_id');
            $countQuery->where(['=', 'temp.status_id', $status]);
            $countQuery->andwhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
        }
        $countQuery->groupBy('orders.order_id');
        $count = $countQuery->andWhere(['is_processed' => 1])
            ->count();
        return ($count > 0) ? $count : '0';
    }


    public static function totalPharmacyOrderStatus($status = "")
    {
        $countQuery = Orders::find();
        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $countQuery = \app\models\PharmacyOrders::find()
                ->select([
                    'pharmacy_orders.*',
                    'orders.create_date as purchase_date',
                    'SUM(order_items.quantity) as quantity',
                    'SUM(order_items.price*order_items.quantity) AS total_amount',
                    'SUM(order_items.price * order_items.quantity) AS total_bill'
                ])
                ->join('LEFT JOIN', '(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                ->andwhere(['IS NOT', 'order_items.pharmacy_order_id', new \yii\db\Expression('NULL')]);
            if ($status > 0) {
                $countQuery->andWhere(['IN', 'temp.pharmacy_status_id', [$status]]);
            }


            $countQuery->andwhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
            $countQuery->groupBy('order_items.pharmacy_order_id');
        }

        //echo $countQuery->createCommand()->rawSql;die;
        $count = $countQuery->count();
        return ($count > 0) ? $count : '0';
    }


    public static function totalReadyForDeliveryOrder($act = "")
    {
        if ($act == 1) {
            $countQuery = $query = \app\models\PharmacyOrders::find()
                ->select([
                    'pharmacy_orders.*',
                    'orders.create_date as purchase_date',
                    'SUM(order_items.quantity) as quantity',
                    'SUM(order_items.price*order_items.quantity) AS total_amount',
                    'SUM(order_items.price * order_items.quantity) AS total_bill',
                    'driver_suborders.driver_id as driverId'
                ])
                ->join('LEFT JOIN', '(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('LEFT JOIN', 'driver_suborders', 'driver_suborders.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp1', 'temp1.order_id = orders.order_id')
                ->andWhere(['IN', 'temp.pharmacy_status_id', [4]])
                ->andWhere(['!=', 'temp1.status_id', 5])
                ->andWhere(['IS NOT', 'driver_suborders.driver_id', new \yii\db\Expression('NULL')]);

            $countQuery->groupBy('order_items.pharmacy_order_id');
        } elseif ($act == 0) {
            $countQuery = \app\models\PharmacyOrders::find()
                ->select([
                    'pharmacy_orders.*',
                    'orders.create_date as purchase_date',
                    'SUM(order_items.quantity) as quantity',
                    'SUM(order_items.price*order_items.quantity) AS total_amount',
                    'SUM(order_items.price * order_items.quantity) AS total_bill'
                ])
                ->join('LEFT JOIN', '(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('LEFT JOIN', 'driver_suborders', 'driver_suborders.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->andWhere(['IN', 'temp.pharmacy_status_id', [2]])
                ->andWhere(['IS', 'driver_suborders.driver_id', new \yii\db\Expression('NULL')]);
            $countQuery->groupBy('order_items.pharmacy_order_id');
        }
        //echo $countQuery->createCommand()->rawSql;die;
        $count = $countQuery->count();
        return ($count > 0) ? $count : '0';
    }

    public static function getPharmaStatusList()
    {
        $model = \app\models\PharmacyStatus::find()->all();
        $list = ArrayHelper::map($model, 'pharmacy_status_id', 'name_en');
        return $list;
    }


    public static function getStatusList($current_status_id = '')
    {
        $query = \app\models\Status::find()->where(['<>', 'status_id', 7]);
        //$query = \app\models\Status::find();
        /*if (!empty($current_status_id)) {
            $currentStatusModel = Status::findOne($current_status_id);
            $query->andWhere(['>', 'list_order', $currentStatusModel->list_order]);
        }*/
        if (!empty($current_status_id) && $current_status_id == 1) {
            $query->andWhere(['status_id' => [2, 6]]);
        }
        $model = $query->orderBy(['list_order' => SORT_ASC])->all();

        $list = ArrayHelper::map($model, 'status_id', 'name_en');
        return $list;
    }

    public static function getUserReport($user_id)
    {
        $model = \app\models\UserReport::find()
            ->where(['user_id' => $user_id])
            ->orderBy(['report_id' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'report_id', 'title');

        return $list;
    }

    public static function calculateAdminCommision($pharmaId, $orderId)
    {
        $model = Orders::find()
            ->select([
                'orders.order_id',
                'SUM(order_items.price*order_items.quantity) AS total_amount',
                'SUM((`order_items`.`price` * `order_items`.`quantity` * `pharmacy_orders`.`pharmacy_commission`) / 100) AS admin_commission'
            ])
            ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
            ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
            ->join('left join', 'users', 'orders.user_id = users.user_id')
            ->join('left join', 'shipping_addresses', 'orders.shipping_address_id = shipping_addresses.shipping_address_id')
            ->join('LEFT JOIN', 'currencies', 'currencies.currency_id = order_items.currency_id')
            ->where(['is_processed' => 1, 'orders.order_id' => $orderId, 'pharmacy_orders.pharmacy_id' => $pharmaId])
            ->groupBy('orders.order_id')
            ->asArray()
            ->one();
        if (!empty($model)) {
            return [
                'admin_commision' => $model['admin_commission'],
                'total_item_amt' => $model['total_amount'],
                'pharma_earning' => ($model['total_amount'] - $model['admin_commission']),
            ];
        } else {
            return [
                'admin_commision' => 0,
                'total_item_amt' => 0,
                'pharma_earning' => 0,
            ];
        }
    }

    public static function getParentProducts($attributes)
    {
        $products = [];
        $subQuery = ProductAttributeValues::find()
            ->select(['product_attribute_values.product_id'])
            ->join('INNER JOIN', 'associated_products as child_products', 'child_products.child_id = product_attribute_values.product_id')
            ->andFilterWhere(['IN', 'product_attribute_values.attribute_value_id', $attributes]);

        $model = AssociatedProducts::find()
            ->select(['parent_id'])
            ->join('left JOIN', 'product', 'associated_products.parent_id = product.product_id')
            ->where(['associated_products.child_id' => $subQuery])
            ->asArray()->all();

        if (!empty($model)) {
            $products = array_column($model, 'parent_id');
            $products = array_unique($products);
        }

        return $products;
    }

    public static function totalLabAppointment($key = "")
    {
        $countQuery =  \app\models\LabAppointments::find()
            ->where(['is_deleted' => 0, 'is_paid' => 1]);
        $today_date = date('Y-m-d h:i:s');
        if (!empty($key) && $key == 'U') {
            $countQuery->andWhere(['is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0, 'not_show' => 0]);
            $countQuery->andWhere(['>', 'appointment_datetime', $today_date]);
        }

        if (!empty($key) && $key == 'C') {
            $countQuery->andWhere(['not_show' => 0, 'is_completed' => 1, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($key) && $key == 'F') {
            $countQuery->andWhere(['is_completed' => 0, 'is_cancelled' => 0, 'not_show' => 0]);
            $countQuery->andwhere(['IN', 'is_paid', [0, 1, 2]]);
            $countQuery->andWhere(['<', 'appointment_datetime', $today_date]);
        }

        if (!empty($key) && $key == 'N') {
            $countQuery->andWhere(['not_show' => 1, 'is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 4) {
            $countQuery->andwhere(['lab_id' => Yii::$app->user->identity->lab_id]);
        }
        //return $countQuery->createCommand()->rawSql;die;
        $count = $countQuery->count();

        return ($count > 0) ? $count : '0';
    }

    public static function translatorAppointment($key = "", $consultation_type = "")
    {
        $countQuery =  \app\models\DoctorAppointments::find()
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            //->andWhere(['!=', 'is_paid', 0]);
            ->andWhere(['is_paid' => 1])
            ->andWhere(['translator_id' => Yii::$app->user->identity->translator_id]);
        $today_date = date('Y-m-d H:i:s');
        if (!empty($key) && $key == 'U') {
            $countQuery->andWhere(['is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0, 'not_show' => 0]);
            $countQuery->andWhere(['>', 'appointment_datetime', $today_date]);

            if (!empty($consultation_type) && $consultation_type == 'I') {
                $countQuery->andWhere(['consultation_type' => 'I']);
            }

            if (!empty($consultation_type) && $consultation_type == 'V') {
                $countQuery->andWhere(['consultation_type' => 'V']);
            }
        }

        if (!empty($key) && $key == 'C') {
            $countQuery->andWhere(['is_completed' => 1, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($key) && $key == 'N') {
            $countQuery->andWhere(['not_show' => 1, 'is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($key) && $key == 'F') {
            $countQuery->andWhere(['is_completed' => 0, 'is_cancelled' => 0]);
            $countQuery->andwhere(['IN', 'is_paid', [0, 1, 2]]);
            $countQuery->andWhere(['<', 'appointment_datetime', $today_date]);
        }

        $count = $countQuery->count();

        return ($count > 0) ? $count : '0';
    }



    public static function totalDoctorAppointment($key = "", $consultation_type = "")
    {
        $countQuery =  \app\models\DoctorAppointments::find()
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            //->andWhere(['!=', 'is_paid', 0]);
            ->andWhere(['is_paid' => 1]);
        $today_date = date('Y-m-d H:i:s');
        if (!empty($key) && $key == 'U') {
            $countQuery->andWhere(['is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0, 'not_show' => 0]);
            $countQuery->andWhere(['>', 'appointment_datetime', $today_date]);

            if (!empty($consultation_type) && $consultation_type == 'I') {
                $countQuery->andWhere(['consultation_type' => 'I']);
            }

            if (!empty($consultation_type) && $consultation_type == 'V') {
                $countQuery->andWhere(['consultation_type' => 'V']);
            }
        }

        if (!empty($key) && $key == 'C') {
            $countQuery->andWhere(['is_completed' => 1, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($key) && $key == 'N') {
            $countQuery->andWhere(['not_show' => 1, 'is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($key) && $key == 'F') {
            $countQuery->andWhere(['is_completed' => 0, 'is_cancelled' => 0]);
            $countQuery->andwhere(['IN', 'is_paid', [0, 1, 2]]);
            $countQuery->andWhere(['<', 'appointment_datetime', $today_date]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 3) {
            $countQuery->andwhere(['doctors.doctor_id' => Yii::$app->user->identity->doctor_id]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 2) {
            $countQuery->andwhere(['doctors.clinic_id' => Yii::$app->user->identity->clinic_id]);
        }
        if (\Yii::$app->session['_eyadatAuth'] == 8) {
            $countQuery->andwhere(['doctor_appointments.translator_id' => Yii::$app->user->identity->translator_id]);
        }
        //return $countQuery->createCommand()->rawSql;die;
        $count = $countQuery->count();



        return ($count > 0) ? $count : '0';
    }

    public static function getNextBookingNumber($from)
    {
        if ($from == 'doctor') {
            $booking = \app\models\DoctorAppointments::find()
                ->select(['MAX(`appointment_number`) AS appointment_number'])
                ->asArray()
                ->one();
        } elseif ($from == 'lab') {
            $booking = \app\models\LabAppointments::find()
                ->select(['MAX(`appointment_number`) AS appointment_number'])
                ->asArray()
                ->one();
        }

        if (!empty($booking) && isset($booking['appointment_number']) && $booking['appointment_number'] != 0) {
            return $booking['appointment_number'] + 1;
        } else {
            return 1000001;
        }
    }


    public static function sendPushwoosh($message, $devices, $target = "", $id = "", $push_title = "", $data_title = "", $name_en = '', $name_ar = '')
    {
        $url = 'https://cp.pushwoosh.com/json/1.3/createMessage';
        $app_title = "Eyadat";
        $push_title = !empty($push_title) ? $push_title : ''; // push prompt title
        $data_title = !empty($data_title) ? $data_title : $message; // only for ios push

        if (!empty($devices)) {
            $notificationArray = [
                [
                    'send_date'     => 'now',
                    'content'       => $message,
                    'devices'       => $devices,
                    'ios_title'     => $app_title,
                    'ios_subtitle' => $push_title,
                    'ios_sound'     => 'push.caf',
                    'android_header' => $push_title,
                    'android_sound' => 'push.caf',
                    'data' => [
                        'target' => $target,
                        'target_id' => (int) $id
                    ],
                    'ios_root_params' => [
                        'target'    => $target,
                        'target_id' => (int)$id,
                        'name'      => $name_en,
                        'name_ar'   => $name_ar
                    ]
                ]
            ];
        } else {
            $notificationArray = [
                [
                    'send_date'     => 'now',
                    'content'       => $message,
                    'ios_title'     => $app_title,
                    'ios_subtitle'  => $push_title,
                    'ios_sound'     => 'push.caf',
                    'android_header' => $push_title,
                    'android_sound' => 'push.caf',
                    'data' => [
                        'target' => $target,
                        'target_id' => (int) $id
                    ],
                    'ios_root_params' => [
                        'target'    => $target,
                        'target_id' => (int)$id,
                        'name'      => $name_en,
                        'name_ar'   => $name_ar
                    ]
                ]
            ];
        }
        $data = [
            'application'   => "7F84A-DD046", //"5AEB9-D725A", // live
            'auth'          => "JVSIoOB3yWOYAawju48sy5TuNbcZ2dkYjKxHJkFuVC0H4cAYGwSlAEcoXETSLThXmMCqbMR5n0DsJSZs9tXx",
            //'devices' => $devices,
            'notifications' => $notificationArray
        ];
        $request = json_encode(['request' => $data]);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request),
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
    }
    public static function sendTestPushwoosh($message, $devices, $target = "", $id = "", $push_title = "", $data_title = "", $name_en = '', $name_ar = '')
    {
        $url = 'https://cp.pushwoosh.com/json/1.3/createMessage';

        $push_title = !empty($push_title) ? $push_title : 'Eyadat'; // push prompt title
        $data_title = !empty($data_title) ? $data_title : $message; // only for ios push

        if (!empty($devices)) {
            $notificationArray = [
                [
                    'send_date'     => 'now',
                    'content'       => $message,
                    'devices'       => $devices,
                    'ios_title'     => $push_title,
                    'ios_subtitle' => $message,
                    'ios_sound'     => 'push.caf',
                    'android_header' => $push_title,
                    'android_sound' => 'push.caf',
                    'data' => [
                        'target' => $target,
                        'target_id' => (int) $id
                    ],
                    'ios_root_params' => [
                        'target'    => $target,
                        'target_id' => (int)$id,
                        'name'      => $name_en,
                        'name_ar'   => $name_ar
                    ]
                ]
            ];
        } else {
            $notificationArray = [
                [
                    'send_date'     => 'now',
                    'content'       => $message,
                    'ios_title'     => $push_title,
                    'ios_subtitle'  => $message,
                    'ios_sound'     => 'push.caf',
                    'android_header' => $push_title,
                    'android_sound' => 'push.caf',
                    'data' => [
                        'target' => $target,
                        'target_id' => (int) $id
                    ],
                    'ios_root_params' => [
                        'target'    => $target,
                        'target_id' => (int)$id,
                        'name'      => $name_en,
                        'name_ar'   => $name_ar
                    ]
                ]
            ];
        }
        $host = '';
        if (!empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $data = [
            'application'   => ($host == 'admin.3edyadat.com') ? "7F84A-DD046" : "", //"5AEB9-D725A", // live
            'auth'          => ($host == 'admin.3edyadat.com') ? "JVSIoOB3yWOYAawju48sy5TuNbcZ2dkYjKxHJkFuVC0H4cAYGwSlAEcoXETSLThXmMCqbMR5n0DsJSZs9tXx" : "",
            'devices' => $devices,
            'notifications' => $notificationArray
        ];
        $request = json_encode(['request' => $data]);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request),
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        return $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
    }

    public static function getPaymentStatus()
    {
        $model = \app\models\Payment::find()
            ->select(['DISTINCT(`result`)'])
            ->where('result IS NOT NULL')
            ->all();

        return $list = ArrayHelper::map($model, 'result', 'result');
    }

    public static function getDriverList()
    {
        $model = \app\models\Drivers::find()->where(['is_deleted' => 0, 'is_active' => 1]);
        $model = $model->orderBy(['name_en' => SORT_ASC])->all();

        $list = ArrayHelper::map($model, 'driver_id', 'name_en');
        return $list;
    }

    public static function getCurrentOrderStatus($id)
    {
        $model = \app\models\OrderStatus::find()
            ->select('order_status.status_id as status_id')
            ->join('LEFT JOIN', 'order_status AS t2', 'order_status.order_id = t2.order_id 
                AND (order_status.status_date < t2.status_date 
                 OR (order_status.status_date = t2.status_date AND order_status.order_status_id < t2.order_status_id))')
            ->where('t2.order_id IS NULL')
            ->andWhere(['order_status.order_id' => $id])
            ->one();
        return (!empty($model)) ? $model->status_id : false;
    }

    public static function getCurrentSubOrderStatus($id)
    {
        $model = \app\models\PharmacyOrderStatus::find()
            ->select('pharmacy_order_status.pharmacy_status_id as status_id')
            ->join('LEFT JOIN', 'pharmacy_order_status AS t2', 'pharmacy_order_status.pharmacy_order_id = t2.pharmacy_order_id 
                AND (pharmacy_order_status.status_date < t2.status_date 
                 OR (pharmacy_order_status.status_date = t2.status_date AND pharmacy_order_status.pharmacy_order_status_id < t2.pharmacy_order_status_id))')
            ->where('t2.pharmacy_order_id IS NULL')
            ->andWhere(['pharmacy_order_status.pharmacy_order_id' => $id])
            ->one();
        return (!empty($model)) ? $model->status_id : false;
    }
}
