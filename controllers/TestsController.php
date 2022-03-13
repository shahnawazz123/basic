<?php

namespace app\controllers;

use Yii;
use app\models\Tests;
use app\models\TestsSearch;
use yii\web\Controller;
use app\models\TestCategories;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * TestsController implements the CRUD actions for Tests model.
 */
class TestsController extends Controller
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
                'only' => ['index', 'view', 'delete','update','create','publish'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'delete','update','create','publish'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN,
                            UserIdentity::ROLE_PHARMACY,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Tests models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TestsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tests model.
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
     * Creates a new Tests model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tests();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if($model->save()){

                if (!empty($request['Tests']['category_id'])) {
                    foreach ($request['Tests']['category_id'] as $category) 
                    {
                        $categoryModel = new \app\models\TestCategories();
                        $categoryModel->test_id = $model->test_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Tests successfully added');
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
     * Updates an existing Tests model.
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

            //print_r($request);die;
            if($model->save()){
                TestCategories::deleteAll(['test_id' => $model->test_id]);
                if (!empty($request['Tests']['category_id'])) {
                    foreach ($request['Tests']['category_id'] as $category) 
                    {
                        $categoryModel = new \app\models\TestCategories();
                        $categoryModel->test_id = $model->test_id;
                        $categoryModel->category_id = $category;
                        $categoryModel->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Tests successfully updated');
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
     * Deletes an existing Tests model.
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
                    Yii::$app->session->setFlash('success', 'Tests successfully deleted');
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
     * Finds the Tests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tests::findOne($id)) !== null) {
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
