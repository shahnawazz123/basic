<?php

namespace app\controllers;

use Yii;
use app\models\PharmacyLocations;
use app\models\PharmacyLocationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PharmacyLocationsController implements the CRUD actions for PharmacyLocations model.
 */
class PharmacyLocationsController extends Controller
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
     * Lists all PharmacyLocations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PharmacyLocationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PharmacyLocations model.
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
     * Creates a new PharmacyLocations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PharmacyLocations();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if($model->save()){
                Yii::$app->session->setFlash('success', 'PharmacyLocations successfully added');
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
     * Updates an existing PharmacyLocations model.
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
                Yii::$app->session->setFlash('success', 'PharmacyLocations successfully updated');
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
     * Deletes an existing PharmacyLocations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
                    $model->is_deleted = 1;
            $model->save();
                    Yii::$app->session->setFlash('success', 'PharmacyLocations successfully deleted');
        return $this->redirect(['index']);
    }
    

    /**
     * Finds the PharmacyLocations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PharmacyLocations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PharmacyLocations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddMoreLocation($num) 
    {
        $model = new \app\models\PharmacyLocations();
        return $this->renderAjax('_add_more_location', [
                    'model' => $model,
                    'num' => $num,
        ]);
    }
}
