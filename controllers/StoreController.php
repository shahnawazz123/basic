<?php

namespace app\controllers;

use Yii;
use app\models\Stores;
use app\models\StoreSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use himiklab\sortablegrid\SortableGridAction;

/**
 * StoreController implements the CRUD actions for Stores model.
 */
class StoreController extends Controller
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
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(7),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                ],
            ],
        ];
    }
    
    public function actions() {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => Stores::className(),
            ],
        ];
    }

    /**
     * Lists all Stores models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stores model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Stores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stores();
        if ($model->load(Yii::$app->request->post())) {           
            $request = Yii::$app->request->bodyParams;
            $count = Stores::find()
                    ->count();
            $model->sort_order = $count + 1;
            if($model->save()){
                if (!empty($request['p_id'])) {
                    foreach ($request['p_id'] as $row) {
                        $storeProduct = new \app\models\StoreProducts();
                        $storeProduct->store_id = $model->store_id;
                        $storeProduct->product_id = $row;
                        $storeProduct->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Store successfully added');
                return $this->redirect(['index']);
            }
            else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Stores model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if($model->save()) {
                if($model->is_default) {
                    \app\models\Stores::updateAll(['is_default' => 0], ['!=', 'store_id', $id]);
                }
                \app\models\StoreProducts::deleteAll('store_id = ' . $model->store_id);
                if (!empty($request['p_id'])) {
                    foreach ($request['p_id'] as $row) {
                        $storeProduct = new \app\models\StoreProducts();
                        $storeProduct->store_id = $model->store_id;
                        $storeProduct->product_id = $row;
                        $storeProduct->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Store successfully updated');
                return $this->redirect(['index']);
            }
            else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Stores model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        
        Yii::$app->session->setFlash('success', 'Store successfully deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Stores model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stores the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stores::find()->where(['is_deleted' => 0,'store_id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
