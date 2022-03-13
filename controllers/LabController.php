<?php

namespace app\controllers;

use Yii;
use app\models\Labs;
use app\models\LabsSearch;
use app\models\LabInsurances;
use app\models\LabServices;
use app\models\LabTests;
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
 * LabController implements the CRUD actions for Labs model.
 */
class LabController extends Controller
{
    /**
     * {@inheritdoc}
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
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(17),
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
     * Lists all Labs models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LabsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Labs model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Labs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Labs();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $password = $request['Labs']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            $model->start_time = date('H:i:s', strtotime($model->start_time));
            $model->end_time = date('H:i:s', strtotime($model->end_time));
            $model->accepted_payment_method = (!empty($request['Labs']['accepted_payment_method'])) ? implode(',', $request['Labs']['accepted_payment_method']) : '';
            if (isset($request['Labs']['latlon']) && $request['Labs']['latlon'] != "") {
                $model->latlon = $request['Labs']['latlon'];
            }
            if ($model->save(false)) {
                if (!empty($request['Labs']['days'])) {
                    foreach ($request['Labs']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\LabsWorkingDays();
                            $timeModel->lab_id = $model->lab_id;
                            $timeModel->day = $day;
                            $timeModel->lab_start_time = date('H:i:s', strtotime($request['Labs']['lab_start_time'][$day]));
                            $timeModel->lab_end_time = date('H:i:s', strtotime($request['Labs']['lab_end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }

                if (!empty($request['Labs']['insurance_id'])) {
                    foreach ($request['Labs']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\LabInsurances();
                        $insuranceModel->lab_id = $model->lab_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                if (!empty($request['Labs']['service_id'])) {
                    foreach ($request['Labs']['service_id'] as $service) {
                        $serviceModel = new \app\models\LabServices();
                        $serviceModel->lab_id = $model->lab_id;
                        $serviceModel->service_id = $service;
                        $serviceModel->save(false);
                    }
                }

                if (!empty($request['Labs']['test_id'])) {
                    foreach ($request['Labs']['test_id'] as $test) {
                        $testModel = new \app\models\LabTests();
                        $testModel->lab_id = $model->lab_id;
                        $testModel->test_id = $test;
                        $testModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Labs successfully added');
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

    /**
     * Updates an existing Labs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Labs']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            $model->updated_at = date('Y-m-d H:i:s');
            $model->start_time = date('H:i:s', strtotime($request['Labs']['start_time']));
            $model->end_time = date('H:i:s', strtotime($request['Labs']['end_time']));
            if (isset($request['Labs']['password_hash']) && $request['Labs']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Labs']['password_hash']);
            }
            if (isset($request['Labs']['latlon']) && $request['Labs']['latlon'] != "") {
                $model->latlon = $request['Labs']['latlon'];
            }
            if (isset($request['Labs']['password_hash']) && $request['Labs']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Labs']['password_hash']);
            }

            $model->accepted_payment_method = (!empty($request['Labs']['accepted_payment_method'])) ? implode(',', $request['Labs']['accepted_payment_method']) : '';
            if ($model->save()) {
                if (!empty($request['Labs']['days'])) {
                    $i = 0;
                    \app\models\LabsWorkingDays::deleteAll(['lab_id' => $model->lab_id]);
                    foreach ($request['Labs']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\LabsWorkingDays();
                            $timeModel->lab_id = $model->lab_id;
                            $timeModel->day = $day;
                            $timeModel->lab_start_time = date('H:i:s', strtotime($request['Labs']['lab_start_time'][$day]));
                            $timeModel->lab_end_time = date('H:i:s', strtotime($request['Labs']['lab_end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }

                LabInsurances::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['insurance_id'])) {
                    foreach ($request['Labs']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\LabInsurances();
                        $insuranceModel->lab_id = $model->lab_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                LabServices::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['service_id'])) {
                    foreach ($request['Labs']['service_id'] as $service) {
                        $serviceModel = new \app\models\LabServices();
                        $serviceModel->lab_id = $model->lab_id;
                        $serviceModel->service_id = $service;
                        $serviceModel->save(false);
                    }
                }

                LabTests::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['test_id'])) {
                    foreach ($request['Labs']['test_id'] as $test) {
                        $testModel = new \app\models\LabTests();
                        $testModel->lab_id = $model->lab_id;
                        $testModel->test_id = $test;
                        $testModel->save(false);
                    }
                }

                Yii::$app->session->setFlash('success', 'Labs successfully updated');
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

    /**
     * Deletes an existing Labs model.
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
        Yii::$app->session->setFlash('success', 'Labs successfully deleted');
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
     * Finds the Labs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Labs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Labs::findOne($id)) !== null) {
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
                    'msg' => 'Lab does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = "";
                $notification->target   = "L";
                $notification->target_id = $model->lab_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, '', "L", $model->lab_id, $title, '', $model->name_en, $model->name_ar);

                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }

    public function actionProfile()
    {
        if (Yii::$app->session['_eyadatAuth'] == 4) {
            $id = Yii::$app->user->identity->lab_id;
        } else {
            return $this->redirect(['dashboard/index']);
        }
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;

            $model->updated_at = date('Y-m-d H:i:s');
            if (isset($request['Labs']['start_time'])) {
                $model->start_time = date('H:i:s', strtotime($request['Labs']['start_time']));
            }
            if (isset($request['Labs']['end_time'])) {
                $model->end_time = date('H:i:s', strtotime($request['Labs']['end_time']));
            }
            if (isset($request['Labs']['password_hash']) && $request['Labs']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Labs']['password_hash']);
            }

            $model->accepted_payment_method = (!empty($request['Labs']['accepted_payment_method'])) ? implode(',', $request['Labs']['accepted_payment_method']) : '';
            if ($model->save()) {
                if (!empty($request['Labs']['days'])) {
                    $i = 0;
                    \app\models\LabsWorkingDays::deleteAll(['lab_id' => $model->lab_id]);
                    foreach ($request['Labs']['days'] as $key => $day) {
                        if ($day != '0') {
                            $timeModel = new \app\models\LabsWorkingDays();
                            $timeModel->lab_id = $model->lab_id;
                            $timeModel->day = $day;
                            $timeModel->lab_start_time = date('H:i:s', strtotime($request['Labs']['lab_start_time'][$day]));
                            $timeModel->lab_end_time = date('H:i:s', strtotime($request['Labs']['lab_end_time'][$day]));
                            $timeModel->save(false);
                        }
                    }
                }

                LabInsurances::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['insurance_id'])) {
                    foreach ($request['Labs']['insurance_id'] as $insurance) {
                        $insuranceModel = new \app\models\LabInsurances();
                        $insuranceModel->lab_id = $model->lab_id;
                        $insuranceModel->insurance_id = $insurance;
                        $insuranceModel->save(false);
                    }
                }

                LabServices::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['service_id'])) {
                    foreach ($request['Labs']['service_id'] as $service) {
                        $serviceModel = new \app\models\LabServices();
                        $serviceModel->lab_id = $model->lab_id;
                        $serviceModel->service_id = $service;
                        $serviceModel->save(false);
                    }
                }

                LabTests::deleteAll(['lab_id' => $model->lab_id]);
                if (!empty($request['Labs']['test_id'])) {
                    foreach ($request['Labs']['test_id'] as $test) {
                        $testModel = new \app\models\LabTests();
                        $testModel->lab_id = $model->lab_id;
                        $testModel->test_id = $test;
                        $testModel->save(false);
                    }
                }

                Yii::$app->session->setFlash('success', 'profile successfully updated');
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
}
