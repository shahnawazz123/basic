<?php

namespace app\controllers;

use Yii;
use app\models\Banner;
use app\models\BannerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use himiklab\sortablegrid\SortableGridAction;
use yii\db\Expression;

/**
 * BannerController implements the CRUD actions for Banner model.
 */
class BannerController extends Controller
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
                'only' => ['index', 'view', 'update', 'create', 'delete', 'publish'],
                /*'only' => ['index', 'view', 'create', 'update', 'delete', 'get-list', 'publish'],*/
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(20),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => \app\models\Banner::className(),
            ],

        ];
    }

    /**
     * Lists all Banner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BannerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banner model.
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
     * Creates a new Banner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Banner();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
         
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Banner successfully added');
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
     * Updates an existing Banner model.
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
            //    print_r($request);
            // die;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Banner successfully updated');
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
     * Deletes an existing Banner model.
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
        Yii::$app->session->setFlash('success', 'Banner successfully deleted');
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
     * Finds the Banner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Banner::findOne($id)) !== null) {
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

    public function actionGetList($type)
    {
        if (isset($type) && $type != "") {
            echo '<option value="">Please select</option>';
            if ($type == 'C') {
                $model = \app\models\Clinics::find()
                    ->where(['is_deleted' => 0, 'is_active' => 1, "type" => "C"])
                    ->all();

                if (!empty($model)) {
                    foreach ($model as $row) {
                        echo '<option value="' . $row->clinic_id . '">' . $row->name_en . '</option>';
                    }
                }
            } elseif ($type == 'H') {
                $model = \app\models\Clinics::find()
                    ->where(['is_active' => 1, 'is_deleted' => 0, "type" => "H"])
                    ->all();
                if (!empty($model)) {
                    foreach ($model as $row) {
                        echo '<option value="' . $row->clinic_id . '">' . $row->name_en . '</option>';
                    }
                }
            }  elseif ($type == 'D') {
                $model = \app\models\Doctors::find()
                    ->where(['is_active' => 1, 'is_deleted' => 0])
                    ->all();
                if (!empty($model)) {
                    foreach ($model as $row) {
                        echo '<option value="' . $row->doctor_id . '">' . $row->name_en . '</option>';
                    }
                }
            } elseif ($type == 'L') {
                $model = \app\models\Labs::find()
                    ->where(['is_active' => 1, 'is_deleted' => 0])
                    ->all();
                if (!empty($model)) {
                    foreach ($model as $row) {
                        echo '<option value="' . $row->lab_id . '">' . $row->name_en . '</option>';
                    }
                }
            } elseif ($type == 'F') {
                $model = \app\models\Pharmacies::find()
                    ->where(['is_active' => 1, 'is_deleted' => 0])
                    ->all();
                if (!empty($model)) {
                    foreach ($model as $row) {
                        echo '<option value="' . $row->pharmacy_id . '">' . $row->name_en . '</option>';
                    }
                }
            }
        }
    }
}
