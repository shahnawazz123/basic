<?php

namespace app\controllers;

use Yii;
use app\models\DoctorPrescriptions;
use app\models\DoctorPrescriptionsSearch;
use app\models\DoctorAppointmentMedicinesSearch;
use app\models\DoctorAppointmentMedicines;
use app\models\DoctorAppointments;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DoctorPrescriptionsController implements the CRUD actions for DoctorPrescriptions model.
 */
class DoctorPrescriptionsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DoctorPrescriptions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DoctorPrescriptionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DoctorPrescriptions model.
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
     * Creates a new DoctorPrescriptions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DoctorPrescriptions();
        $model1 = new ProductSearch();
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->searchPrescriptionMedicine(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $doctor_appointment_id = $request['DoctorPrescriptions']['doctor_appointment_id'];
            $modelApp = \app\models\DoctorAppointments::find()
                ->where(['doctor_appointment_id' => $doctor_appointment_id])
                ->one();
            $model->user_id = (!empty($modelApp)) ? $modelApp->user_id : 0;

            if ($model->save()) {
                //print_r($request['selection']);die;
                if (!empty($request['selection'])) {
                    foreach ($request['selection'] as $product) {
                        $medicineModel = new \app\models\DoctorAppointmentMedicines();
                        $medicineModel->doctor_appointment_prescription_id = $model->doctor_appointment_prescription_id;
                        $medicineModel->product_id = $product;
                        $medicineModel->qty = (!empty($request['qty_' . $product])) ? $request['qty_' . $product] : 1;
                        $medicineModel->instruction = (!empty($request['instruction_' . $product])) ? $request['instruction_' . $product] : '';
                        $medicineModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Doctor Prescriptions successfully added');
                $user_device_token = $modelApp->user->device_token;
                $title  = "Prescription Added";
                $full_name = $modelApp->user->first_name;
                $msg    =  "Doctor added your Prescription.";
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = (!empty($modelApp)) ? $modelApp->user_id : 0;
                $notification->target   = "ER";
                $notification->target_id = (!empty($modelApp)) ? $modelApp->doctor_appointment_id : 0;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);

                if (!empty($user_device_token)) {
                    $this->sendpush($model->doctor_appointment_prescription_id, $msg, $title, $user_device_token, "ER");
                }

                return $this->redirect(['doctor-appointment/view?id=' . $model->doctor_appointment_id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'model1' => $model1,
                    'searchModel' => $searchModel,
                    'dataProvider' => (!empty(Yii::$app->request->queryParams)) ? $dataProvider : $dataProvider,
                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'model1' => $model1,
            'searchModel' => $searchModel,
            'dataProvider' => (!empty(Yii::$app->request->queryParams)) ? $dataProvider : $dataProvider,
        ]);
    }

    /**
     * Updates an existing DoctorPrescriptions model.
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
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'DoctorPrescriptions successfully updated');
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
     * Deletes an existing DoctorPrescriptions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $doctor_appointment_id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        Yii::$app->session->setFlash('success', 'Doctor Prescriptions successfully deleted');
        //return $this->redirect(['index']);
        return $this->redirect(['doctor-appointment/view?id=' . $doctor_appointment_id]);
    }
    public function sendpush($id, $msg, $title = "", $user_device_token = "", $target = "ER")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = DoctorAppointments::findOne($id);

            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Doctor appointment does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);

                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $model->user->user_id;
                $notification->target   = $target;
                $notification->target_id = $model->doctor_appointment_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token,  $target, $model->doctor_appointment_id, $title, '', $model->doctor->name_en, $model->doctor->name_ar);

                /*return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);*/
            }
        }
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
     * Finds the DoctorPrescriptions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DoctorPrescriptions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DoctorPrescriptions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
