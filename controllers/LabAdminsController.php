<?php

namespace app\controllers;

use Yii;
use app\models\LabAdmins;
use app\models\LabAdminsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * LabAdminsController implements the CRUD actions for LabAdmins model.
 */
class LabAdminsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete', 'update', 'create', 'publish'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'delete', 'update', 'create', 'publish'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN,
                            UserIdentity::ROLE_LAB,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all LabAdmins models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LabAdminsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LabAdmins model.
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
     * Creates a new LabAdmins model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LabAdmins();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['LabAdmins']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'LabAdmins successfully added');
                return $this->redirect(['index']);
            } else {
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
     * Updates an existing LabAdmins model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (isset($request['LabAdmins']['password_hash']) && $request['LabAdmins']['password_hash'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['LabAdmins']['password_hash']);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'LabAdmins successfully updated');
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
     * Deletes an existing LabAdmins model.
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
        Yii::$app->session->setFlash('success', 'LabAdmins successfully deleted');
        return $this->redirect(['index']);
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
     * Finds the LabAdmins model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LabAdmins the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LabAdmins::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
