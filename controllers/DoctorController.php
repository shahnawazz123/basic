<?php

namespace app\controllers;

use Yii;
use app\models\Doctors;
use app\models\DoctorsSearch;
use app\models\DoctorInsurances;
use app\models\DoctorSymptoms;
use app\models\DoctorCategories;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use himiklab\sortablegrid\SortableGridAction;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;


class DoctorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete', 'update', 'create', 'publish'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(15),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'delete', 'update', 'create', 'publish'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_CLINIC
                        ]
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => \app\models\Doctors::className(),
            ],

        ];
    }


    public function actionIndex()
    {
        $searchModel = new DoctorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreate()
    {
        $model = new Doctors();
        $model->scenario = 'create';
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {

            $request = Yii::$app->request->bodyParams;

            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');

            $password = $request['Doctors']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);

            $model->type       = implode(',', $request['Doctors']['type']);
            if (!empty($request['Doctors']['accepted_payment_method'])) {
                $model->accepted_payment_method       = implode(',', $request['Doctors']['accepted_payment_method']);
            }
            if ($model->save()) {

                if (!empty($request['Doctors']['days'])) {
                    foreach ($request['Doctors']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\DoctorWorkingDays();
                            $timeModel->doctor_id = $model->doctor_id;
                            $timeModel->day = $day;
                            $timeModel->start_time = date('H:i:s', strtotime($request['Doctors']['start_time'][$day]));
                            $timeModel->end_time = date('H:i:s', strtotime($request['Doctors']['end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }
                if (!empty($request['Doctors']['insurance_id'])) {
                    foreach ($request['Doctors']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\DoctorInsurances();
                        $insuranceModel->doctor_id = $model->doctor_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                if (!empty($request['Doctors']['category_id'])) {
                    foreach ($request['Doctors']['category_id'] as $category) {
                        $categoryModel = new \app\models\DoctorCategories();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }
                if (!empty($request['Doctors']['symptom_id'])) {
                    foreach ($request['Doctors']['symptom_id'] as $category) {
                        $categoryModel = new \app\models\DoctorSymptoms();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->symptom_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Doctors successfully added');
                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (isset($request['Doctors']['password_hash']) && $request['Doctors']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Doctors']['password_hash']);
            }

            $model->updated_at = date('Y-m-d H:i:s');
            $model->type = isset($request['Doctors']['type']) ? implode(',', $request['Doctors']['type']) : '';
            if (!empty($request['Doctors']['accepted_payment_method'])) {
                $model->accepted_payment_method = (!empty($request['Doctors']['accepted_payment_method'])) ? implode(',', $request['Doctors']['accepted_payment_method']) : '';
            }
            if ($model->save()) {
                if (!empty($request['Doctors']['days'])) {
                    $i = 0;
                    \app\models\DoctorWorkingDays::deleteAll(['doctor_id' => $model->doctor_id]);
                    foreach ($request['Doctors']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\DoctorWorkingDays();
                            $timeModel->doctor_id = $model->doctor_id;
                            $timeModel->day = $day;
                            $timeModel->start_time = date('H:i:s', strtotime($request['Doctors']['start_time'][$day]));
                            $timeModel->end_time = date('H:i:s', strtotime($request['Doctors']['end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }
                DoctorInsurances::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['insurance_id'])) {
                    foreach ($request['Doctors']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\DoctorInsurances();
                        $insuranceModel->doctor_id = $model->doctor_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }
                DoctorCategories::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['category_id'])) {
                    foreach ($request['Doctors']['category_id'] as $category) {
                        $categoryModel = new \app\models\DoctorCategories();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }

                DoctorSymptoms::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['symptom_id'])) {
                    foreach ($request['Doctors']['symptom_id'] as $category) {
                        $categoryModel = new \app\models\DoctorSymptoms();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->symptom_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Doctors successfully updated');
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        Yii::$app->session->setFlash('success', 'Doctors successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionChangeStatus($id)
    {
        $model = $this->findModel($id);

        if ($model->is_active == 0) {
            $model->is_active = '1';
        } elseif ($model->is_active == 1) {
            $model->is_active = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {
            return '0';
        }
    }


    protected function findModel($id)
    {
        if (($model = Doctors::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        if ($model->is_active == 0) {
            $model->is_active = '1';
        } else {
            $model->is_active = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }
    public function actionFeatured($id)
    {
        $model = $this->findModel($id);
        if ($model->is_featured == 0) {
            $model->is_featured = '1';
        } else {
            $model->is_featured = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }
    public function actionSendPush($id, $msg, $title = "")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Doctor does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = "";
                $notification->target   = "D";
                $notification->target_id = $model->doctor_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, '', "D", $model->doctor_id, $title, '', $model->name_en, $model->name_ar);

                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }

    public function actionProfile()
    {
        if (Yii::$app->session['_eyadatAuth'] == 3) {
            $id = Yii::$app->user->identity->doctor_id;
        } else {
            return $this->redirect(['dashboard/index']);
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (isset($request['Doctors']['password']) && $request['Doctors']['password'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Doctors']['password']);
            }

            $model->updated_at = date('Y-m-d H:i:s');
            if (isset($request['Doctors']['type'])) {
                $model->type = implode(',', $request['Doctors']['type']);
            }
            $model->accepted_payment_method = (!empty($request['Doctors']['accepted_payment_method'])) ? implode(',', $request['Doctors']['accepted_payment_method']) : '';

            if ($model->save()) {
                if (!empty($request['Doctors']['days'])) {
                    $i = 0;
                    \app\models\DoctorWorkingDays::deleteAll(['doctor_id' => $model->doctor_id]);
                    foreach ($request['Doctors']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\DoctorWorkingDays();
                            $timeModel->doctor_id = $model->doctor_id;
                            $timeModel->day = $day;
                            $timeModel->start_time = date('H:i:s', strtotime($request['Doctors']['start_time'][$day]));
                            $timeModel->end_time = date('H:i:s', strtotime($request['Doctors']['end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }
                DoctorInsurances::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['insurance_id'])) {
                    foreach ($request['Doctors']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\DoctorInsurances();
                        $insuranceModel->doctor_id = $model->doctor_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }
                DoctorCategories::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['category_id'])) {
                    foreach ($request['Doctors']['category_id'] as $category) {
                        $categoryModel = new \app\models\DoctorCategories();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }

                DoctorSymptoms::deleteAll(['doctor_id' => $model->doctor_id]);
                if (!empty($request['Doctors']['symptom_id'])) {
                    foreach ($request['Doctors']['symptom_id'] as $category) {
                        $categoryModel = new \app\models\DoctorSymptoms();
                        $categoryModel->doctor_id = $model->doctor_id;
                        $categoryModel->symptom_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Profile successfully updated');
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionGetClinicWorkDays($clinic_id, $id)
    {
        $model = \app\models\Clinics::findOne($clinic_id);
        $days = '';
        if (!empty($model)) {
            $day = [];
            if (!empty($model->clinicWorkingDays)) {
                foreach ($model->clinicWorkingDays as $d) {
                    array_push($day, $d['day']);
                }

                $days = implode(',', $day);
            }
        }


        $modelInsurance = \app\models\Insurances::find()
            ->join('LEFT JOIN', 'clinic_insurances', 'clinic_insurances.insurance_id=insurances.insurance_id')
            ->where(['clinic_insurances.clinic_id' => $clinic_id])
            ->all();

        $insurance = '<option value="">Please select</option>';
        if (!empty($modelInsurance)) {
            foreach ($modelInsurance as $row) {
                $insurance .= '<option value="' . $row->insurance_id . '">' . $row->name_en . '</option>';
            }
        }
        $temp['insurance'] = $insurance;
        $temp['days'] = $days;
        return json_encode($temp);
    }
}
