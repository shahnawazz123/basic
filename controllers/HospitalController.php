<?php

namespace app\controllers;

use Yii;
use app\models\Clinics;
use app\models\ClinicsSearch;
use app\models\ClinicInsurances;
use app\models\ClinicCategories;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * HospitalController implements the CRUD actions for Clinics model.
 */
class HospitalController extends Controller
{

    /**
     * @inheritdoc
     */
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
                        'actions' => '', //\app\helpers\PermissionHelper::getUserPermissibleAction(14),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Clinics models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClinicsSearch();
        $searchModel->type = 'H';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('//clinic/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Clinics model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('//clinic/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Clinics model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Clinics();
        $model->scenario = 'create';
        $model->scenario = 'create';
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Clinics']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->type = 'H';
            if (isset($request['Clinics']['image_en']) && $request['Clinics']['image_en'] != "") {
                $model->image_en = $request['Clinics']['image_en'];
            }
            if (isset($request['Clinics']['image_ar']) && $request['Clinics']['image_ar'] != "") {
                $model->image_ar = $request['Clinics']['image_ar'];
            }
            if (isset($request['Clinics']['latlon']) && $request['Clinics']['latlon'] != "") {
                $model->latlon = $request['Clinics']['latlon'];
            }
            $model->country_id = $request['Clinics']['country_id'];
            if ($model->save(false)) {

                if (!empty($request['Clinics']['days'])) {
                    foreach ($request['Clinics']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\ClinicWorkingDays();
                            $timeModel->clinic_id = $model->clinic_id;
                            $timeModel->day = $day;
                            $timeModel->start_time = date('H:i:s', strtotime($request['Clinics']['start_time'][$day]));
                            $timeModel->end_time = date('H:i:s', strtotime($request['Clinics']['end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }

                if (!empty($request['Clinics']['insurance_id'])) {
                    foreach ($request['Clinics']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\ClinicInsurances();
                        $insuranceModel->clinic_id = $model->clinic_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                if (!empty($request['Clinics']['category_id'])) {
                    foreach ($request['Clinics']['category_id'] as $category) {
                        $categoryModel = new \app\models\ClinicCategories();
                        $categoryModel->clinic_id = $model->clinic_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Clinics successfully added');
                return $this->redirect(['index']);
            } else {
                return $this->render('//clinic/create', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('//clinic/create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Clinics model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $model->updated_at = date('Y-m-d H:i:s');
            $model->type = 'H';
            if (isset($request['Clinics']['image_en']) && $request['Clinics']['image_en'] != "") {
                $model->image_en = $request['Clinics']['image_en'];
            }
            if (isset($request['Clinics']['image_ar']) && $request['Clinics']['image_ar'] != "") {
                $model->image_ar = $request['Clinics']['image_ar'];
            }
            if (isset($request['Clinics']['latlon']) && $request['Clinics']['latlon'] != "") {
                $model->latlon = $request['Clinics']['latlon'];
            }
            if (isset($request['Clinics']['password_hash']) && $request['Clinics']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Clinics']['password_hash']);
            }

            $model->country_id = $request['Clinics']['country_id'];
            if ($model->save(false)) {

                if (!empty($request['Clinics']['days'])) {
                    $i = 0;
                    \app\models\ClinicWorkingDays::deleteAll(['clinic_id' => $model->clinic_id]);
                    foreach ($request['Clinics']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\CLinicWorkingDays();
                            $timeModel->clinic_id = $model->clinic_id;
                            $timeModel->day = $day;
                            $timeModel->start_time = date('H:i:s', strtotime($request['Clinics']['start_time'][$day]));
                            $timeModel->end_time = date('H:i:s', strtotime($request['Clinics']['end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }

                ClinicInsurances::deleteAll(['clinic_id' => $model->clinic_id]);
                if (!empty($request['Clinics']['insurance_id'])) {
                    foreach ($request['Clinics']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\ClinicInsurances();
                        $insuranceModel->clinic_id = $model->clinic_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                ClinicCategories::deleteAll(['clinic_id' => $model->clinic_id]);
                if (!empty($request['Clinics']['category_id'])) {
                    foreach ($request['Clinics']['category_id'] as $category) {
                        $categoryModel = new \app\models\ClinicCategories();
                        $categoryModel->clinic_id = $model->clinic_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Clinics successfully updated');
                return $this->redirect(['index']);
            } else {
                return $this->render('//clinic/update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('//clinic/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Clinics model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Hospital successfully deleted');
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

    /**
     * Finds the Clinics model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Clinics the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clinics::findOne($id)) !== null) {
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

    public function actionSendPush($id, $msg, $title = "")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Clinic does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = "";
                $notification->target   = "C";
                $notification->target_id = $model->clinic_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, '', "C", $model->clinic_id, $title, '', $model->name_en, $model->name_ar);
                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }
}
