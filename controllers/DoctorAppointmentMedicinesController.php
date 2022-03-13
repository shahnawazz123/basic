<?php

namespace app\controllers;

use Yii;
use app\models\DoctorAppointmentMedicines;
use app\models\DoctorAppointmentMedicinesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DoctorAppointmentMedicinesController implements the CRUD actions for DoctorAppointmentMedicines model.
 */
class DoctorAppointmentMedicinesController extends Controller
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
     * Lists all DoctorAppointmentMedicines models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DoctorAppointmentMedicinesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DoctorAppointmentMedicines model.
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
     * Creates a new DoctorAppointmentMedicines model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DoctorAppointmentMedicines();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if($model->save()){
                Yii::$app->session->setFlash('success', 'DoctorAppointmentMedicines successfully added');
                return $this->redirect(['index']);
            }else{
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
     * Updates an existing DoctorAppointmentMedicines model.
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
            if($model->save()){
                Yii::$app->session->setFlash('success', 'DoctorAppointmentMedicines successfully updated');
                return $this->redirect(['index']);
            }else{
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
     * Deletes an existing DoctorAppointmentMedicines model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
                    $model->delete();
                    Yii::$app->session->setFlash('success', 'DoctorAppointmentMedicines successfully deleted');
        return $this->redirect(['index']);
    }
    

    /**
     * Finds the DoctorAppointmentMedicines model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DoctorAppointmentMedicines the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DoctorAppointmentMedicines::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
