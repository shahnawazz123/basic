<?php

namespace app\controllers;

use Yii;
use app\models\Symptoms;
use app\models\SymptomsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * SymptomsController implements the CRUD actions for Symptoms model.
 */
class SymptomsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete', 'update', 'create','publish'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(14),
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
     * Lists all Symptoms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SymptomsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Symptoms model.
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
     * Creates a new Symptoms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Symptoms();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if($model->save()){
                Yii::$app->session->setFlash('success', 'Symptoms successfully added');
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
     * Updates an existing Symptoms model.
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
                Yii::$app->session->setFlash('success', 'Symptoms successfully updated');
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
     * Deletes an existing Symptoms model.
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
                    Yii::$app->session->setFlash('success', 'Symptoms successfully deleted');
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
     * Finds the Symptoms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Symptoms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Symptoms::findOne($id)) !== null) {
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
}
