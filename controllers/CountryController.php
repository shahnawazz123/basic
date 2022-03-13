<?php

namespace app\controllers;

use Yii;
use app\models\Country;
use app\models\CountrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException; 

/**
 * CountryController implements the CRUD actions for Country model.
 */
class CountryController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'publish', 'activate'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(8),
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
            'editable' => [
                'class' => 'mcms\xeditable\XEditableAction',
                'modelclass' => Country::className(),
            ],
        ];
    }

    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CountrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Country model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Country model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Country();
        //echo "<pre>";print_r(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Country successfully added');
            return $this->redirect(['index']);
        } else {
            //echo json_encode($model->errors);die;
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Country model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Country successfully updated');
                return $this->redirect(['index']);
            } else {
                echo json_encode($model->errors);
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
     * Deletes an existing Country model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        if (!$model->save(false)) {
            die(json_encode($model->errors));
        } else {
            Yii::$app->session->setFlash('success', 'Country successfully deleted');
            return $this->redirect(['index']);
        }
    }

    /**
     * Activate/Deactivate an existing make model.
     * @param integer $id
     * @return json
     */
    public function actionActivate($id) {
        $model = $this->findModel($id);

        if ($model->is_cod_enable == 0)
            $model->is_cod_enable = 1;
        else
            $model->is_cod_enable = 0;

        if ($model->validate() && $model->save()) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionEditShippingCost() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->shipping_cost = $request['value'];
            if ($model->save()) {
                return json_encode([
                    'status' => true,
                    'data' => $model->shipping_cost
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['shipping_cost'][0],
                ]);
            }
        }
    }

    public function actionEditCodCost() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->cod_cost = $request['value'];
            if ($model->save()) {
                return json_encode([
                    'status' => true,
                    'data' => $model->cod_cost
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['cod_cost'][0],
                ]);
            }
        }
    }

    public function actionEditDeliveryInterval() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->delivery_interval = $request['value'];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'data' => $model->delivery_interval
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors,
                ]);
            }
        }
    }

    public function actionEditExpressDeliveryInterval() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->express_delivery_interval = $request['value'];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'data' => $model->express_delivery_interval
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors,
                ]);
            }
        }
    }

    public function actionEditExpressShippingCost() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->express_shipping_cost = $request['value'];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'data' => $model->express_shipping_cost
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors,
                ]);
            }
        }
    }
    
    public function actionEditFreeDeliveryLimit() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->free_delivery_limit = $request['value'];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'data' => $model->free_delivery_limit
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors,
                ]);
            }
        }
    }
    
    public function actionEditVat() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->vat = $request['value'];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'data' => $model->vat
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors,
                ]);
            }
        }
    }

    public function actionEditShippingCostActual() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->standard_shipping_cost_actual = $request['value'];
            if ($model->save()) {
                return json_encode([
                    'status' => true,
                    'data' => $model->standard_shipping_cost_actual
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['standard_shipping_cost_actual'][0],
                ]);
            }
        }
    }
    /**
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Country::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPublish($id) {
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
