<?php

namespace app\controllers;

use Yii;
use app\models\Admin;
use app\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Admin();
        $modules = \app\models\AuthModule::find()
                ->orderBy(['auth_module_name' => SORT_ASC])
                ->where(['is_active' => 1])
                ->asArray()
                ->all();
        $result = [];
        foreach ($modules as $row) {
            $row['items'] = $this->getModuleItem($row['auth_module_id']);
            array_push($result, $row);
        }
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Admin']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            $model->is_active = 1;
            if ($model->save()) {
                if (!empty($request['item_list'])) {
                    foreach ($request['item_list'] as $item) {
                        $assignment = new \app\models\AuthAssignment();
                        $assignment->auth_item_id = $item;
                        $assignment->user_id = $model->admin_id;
                        $assignment->user_type = 'A';
                        $assignment->created_at = date('Y-m-d H:i:s');
                        if (!$assignment->save()) {
                            die(json_encode($assignment->errors));
                        }
                    }
                }
                Yii::$app->session->setFlash('success', 'Admin successfully added');
                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                            'model' => $model,
                            'result' => $result,
                            'id' => -1
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'result' => $result,
                        'id' => -1
            ]);
        }
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $modules = \app\models\AuthModule::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->where(['is_active' => 1])
                ->asArray()
                ->all();
        $result = [];
        foreach ($modules as $row) {
            $row['items'] = $this->getModuleItem($row['auth_module_id']);
            array_push($result, $row);
        }
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            //debugPrint($request);exit;
            if (isset($request['Admin']['password_hash']) && $request['Admin']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Admin']['password_hash']);
            }
            if ($model->save()) {
                if (!empty($request['item_list'])) {
                    \app\models\AuthAssignment::deleteAll('user_id = :user_id AND user_type = :user_type', [':user_id' => $model->admin_id, ':user_type' => 'A']);
                    foreach ($request['item_list'] as $item) {
                        $assignment = new \app\models\AuthAssignment();
                        $assignment->auth_item_id = $item;
                        $assignment->user_id = $model->admin_id;
                        $assignment->user_type = 'A';
                        $assignment->created_at = date('Y-m-d H:i:s');
                        if (!$assignment->save()) {
                            die(json_encode($assignment->errors));
                        }
                    }
                }
                Yii::$app->session->setFlash('success', 'Admin successfully updated');
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                            'model' => $model,
                            'result' => $result,
                            'id' => $model->admin_id
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'result' => $result,
                        'id' => $model->admin_id
            ]);
        }
    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Admin successfully deleted');
            return $this->redirect(['index']);
        }
    }

    public function getModuleItem($id) {
        $model = \app\models\AuthItem::find()
                ->where(['auth_module_id' => $id, 'is_active' => 1, 'rule_name' => 'admin'])
                ->orderBy(['auth_item_name' => SORT_ASC])
                ->asArray()
                ->all();

        return $model;
    }

    /**
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Admin::find()->where(['admin_id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
