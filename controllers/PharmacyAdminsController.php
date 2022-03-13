<?php

namespace app\controllers;

use Yii;
use app\models\PharmacyAdmins;
use app\models\PharmacyAdminsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PharmacyAdminsController implements the CRUD actions for PharmacyAdmins model.
 */
class PharmacyAdminsController extends Controller
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
     * Lists all PharmacyAdmins models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PharmacyAdminsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PharmacyAdmins model.
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
     * Creates a new PharmacyAdmins model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PharmacyAdmins();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $model->created_at = date('Y-m-d H:i:s');
            $password = $request['PharmacyAdmins']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            if($model->save()){
                Yii::$app->session->setFlash('success', 'PharmacyAdmins successfully added');
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
     * Updates an existing PharmacyAdmins model.
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
            if (isset($request['PharmacyAdmins']['password_hash']) && $request['PharmacyAdmins']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['PharmacyAdmins']['password_hash']);
            }
            if($model->save()){
                Yii::$app->session->setFlash('success', 'PharmacyAdmins successfully updated');
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
     * Deletes an existing PharmacyAdmins model.
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
                    Yii::$app->session->setFlash('success', 'PharmacyAdmins successfully deleted');
        return $this->redirect(['index']);
    }
    
    public function actionChangeStatus($id) {
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
     * Finds the PharmacyAdmins model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PharmacyAdmins the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PharmacyAdmins::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
