<?php

namespace app\controllers;

use Yii;
use app\models\Area;
use app\models\AreaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * AreaController implements the CRUD actions for Area model.
 */
class AreaController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'get-states', 'activate', 'get-area', 'get-block'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(10),
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
     * Lists all Area models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AreaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Area model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Area model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Area();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->area_id]);
            Yii::$app->session->setFlash('success', 'Area successfully added');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Area model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Area successfully updated');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Area model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        
        Yii::$app->session->setFlash('success', 'Area successfully deleted');

        return $this->redirect(['index']);
    }

    public function actionGetStates($country_id) {
        $model = \app\models\State::find()
                ->where(['country_id' => $country_id, 'is_deleted' => 0])
                ->all();

        echo '<option value="">Please select</option>';
        if (!empty($model)) {
            foreach ($model as $row) {
                echo '<option value="' . $row->state_id . '">' . $row->name_en . '</option>';
            }
        }
    }
    
    public function actionActivate($id)
    {
        $model = $this->findModel($id);

        if($model->is_active == 0)
            $model->is_active = 1;
        else
            $model->is_active = 0;

        if ($model->validate() && $model->save()) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionGetArea($state) {
        $model = \app\models\Area::find()
            ->where(['state_id' => $state, 'is_deleted' => 0])
            ->all();

        echo '<option value="">Please select</option>';
        if (!empty($model)) {
            foreach ($model as $row) {
                echo '<option value="' . $row->area_id . '">' . $row->name_en . '</option>';
            }
        }
    }

    public function actionGetAreaAjax($state,$selected_id) {
        $model = \app\models\Area::find()
            ->where(['state_id' => $state, 'is_deleted' => 0])
            ->all();

        echo '<option value="">Please select</option>';
        if (!empty($model)) {
            foreach ($model as $row) {
                $sel = ($selected_id == $row->area_id) ? 'selected':'';
                echo '<option value="' . $row->area_id . '" '.$sel.'>' . $row->name_en . '</option>';
            }
        }
    }

    public function actionGetBlock($city) {
        $model = \app\models\Block::find()
            ->where(['area_id' => $city])
            ->all();

        echo '<option value="">Please select</option>';
        if (!empty($model)) {
            foreach ($model as $row) {
                echo '<option value="' . $row->block_id . '">' . $row->name_en . '</option>';
            }
        }
    }

    /**
     * Finds the Area model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Area the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Area::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
