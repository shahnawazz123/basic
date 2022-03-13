<?php

namespace app\controllers;

use Yii;
use app\models\Brands;
use app\models\BrandsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use himiklab\sortablegrid\SortableGridAction;

/**
 * BrandController implements the CRUD actions for Brands model.
 */
class BrandController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'send-push'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(25),
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
                'modelName' => Brands::className(),
            ],
        ];
    }

    /**
     * Lists all Brands models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new BrandsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Brands model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Brands model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Brands();
        if ($model->load(Yii::$app->request->post())) {
            $model->is_active = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Brand successfully added');
                return $this->redirect(['index']);
            } else {
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
     * Updates an existing Brands model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $oldImage = $model->image_name;
        if ($model->load(Yii::$app->request->post())) {
            $newImage = $model->image_name;
            //debugPrint(Yii::$app->request->post());exit;
            if ($model->save()) {
                if (!empty($oldImage) && $newImage != $oldImage && file_exists(Yii::$app->basePath . '/web/uploads/' . $oldImage)) {
                    unlink(Yii::$app->basePath . '/web/uploads/' . $oldImage);
                }
                Yii::$app->session->setFlash('success', 'Brand successfully updated');
                return $this->redirect(['index']);
            } else {
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
     * Activate/Deactivate an existing Brands model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($id) {
        $model = $this->findModel($id);

        if ($model->is_active == 0)
            $model->is_active = 1;
        else
            $model->is_active = 0;

        if ($model->validate() && $model->save()) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionSendPush($id, $msg, $title="") {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {

            $model = $this->findModel($id);

            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Brand does not exist'
                ]);
            } else {

                \app\helpers\AppHelper::sendPushwoosh($msg, '', "BR", $model->brand_id,$title, '', $model->name_en, $model->name_ar);

                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }

    /**
     * Deletes an existing Brands model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $products = \app\models\Product::find()
                ->where(['is_deleted' => 0, 'brand_id' => $model->brand_id])
                ->count();
        if ($products < 1) {
            $model->is_deleted = 1;
            $model->save();
            Yii::$app->session->setFlash('success', 'Brand successfully deleted');
        }else{
            Yii::$app->session->setFlash('error', 'Can\'t delete Brand. There are products linked to that brand');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Brands model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brands the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Brands::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
