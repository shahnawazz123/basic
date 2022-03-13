<?php

namespace app\controllers;

use app\models\ProductAttributeValues;
use Yii;
use app\models\Attributes;
use app\models\AttributesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\AttributeValues;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use himiklab\sortablegrid\SortableGridAction;

/**
 * AttributeController implements the CRUD actions for Attributes model.
 */
class AttributeController extends Controller
{

    public function actions() {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => Attributes::className(),
            ],
        ];
    }

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
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(27),
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
     * Lists all Attributes models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AttributesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Attributes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Attributes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Attributes();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;

            if (!empty($request['AttributeValues'])) {
                if (!empty($request['AttributeValues']['value_en'])) {
                    if (array_has_duplicates($request['AttributeValues']['value_en'])) {
                        Yii::$app->session->setFlash('danger', 'Duplicate attribute values not allowed.');
                        return $this->refresh();
                    }
                }
                if (!empty($request['AttributeValues']['value_ar'])) {
                    if (array_has_duplicates($request['AttributeValues']['value_ar'])) {
                        Yii::$app->session->setFlash('danger', 'Duplicate attribute values not allowed.');
                        return $this->refresh();
                    }
                }
            }

            $model->sort_order = Attributes::find()->count() + 1;
            if ($model->save()) {
                if (!empty($request['AttributeValues'])) {
                    $n = sizeof($request['AttributeValues']['value_en']);
                    for ($i = 0; $i < $n; $i++) {
                        $attributeValue = new AttributeValues();
                        $attributeValue->attribute_id = $model->attribute_id;
                        $attributeValue->value_en = $request['AttributeValues']['value_en'][$i];
                        $attributeValue->value_ar = $request['AttributeValues']['value_ar'][$i];
                        $attributeValue->sort_order = AttributeValues::find()->count() + 1;
                        $attributeValue->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Attribute successfully added');
                return $this->redirect(['index']);
            } else {
                //echo json_encode($model->errors);
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
     * Updates an existing Attributes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (!empty($request['AttributeValues'])) {
                if (!empty($request['AttributeValues']['value_en'])) {
                    if (array_has_duplicates($request['AttributeValues']['value_en'])) {
                        Yii::$app->session->setFlash('danger', 'Duplicate attribute values not allowed.');
                        return $this->refresh();
                    }
                }
                if (!empty($request['AttributeValues']['value_ar'])) {
                    if (array_has_duplicates($request['AttributeValues']['value_ar'])) {
                        Yii::$app->session->setFlash('danger', 'Duplicate attribute values not allowed.');
                        return $this->refresh();
                    }
                }
            }
            if ($model->save()) {
                if (!empty($request['AttributeValues'])) {
                    $n = sizeof($request['AttributeValues']['value_en']);
                    for ($i = 0; $i < $n; $i++) {
                        if (isset($request['AttributeValues']['attribute_value_id'][$i]) && !empty($request['AttributeValues']['attribute_value_id'][$i])){
                            $attributeValue = AttributeValues::findOne($request['AttributeValues']['attribute_value_id'][$i]);
                        }
                        else{
                            $attributeValue = new AttributeValues();
                        }
                        $attributeValue->attribute_id = $model->attribute_id;
                        $attributeValue->value_en = $request['AttributeValues']['value_en'][$i];
                        $attributeValue->value_ar = $request['AttributeValues']['value_ar'][$i];
                        if (!$attributeValue->save()) {
                            debugPrint($attributeValue->errors);
                            exit;
                        }
                    }
                }
                Yii::$app->session->setFlash('success', 'Attribute successfully updated');
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
     * Deletes an existing Attributes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $attributeSetGroups = $model->attributeSetGroups;
        $productAttributeValue = \app\models\ProductAttributeValues::find()
                ->join('left join', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->where(['attribute_values.attribute_id' => $model->attribute_values])
                ->count();
        if (empty($attributeSetGroups) && empty($productAttributeValue)) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Attribute successfully deleted');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Can\'t delete attribute.its used by another module');
            return $this->redirect(['index']);
        }
    }

    public function actionAddValue($count) {
        $model = new AttributeValues();

        return $this->renderAjax('add-value', [
                    'count' => $count,
                    'model' => $model,
        ]);
    }

    public function actionRemoveValue($id) {
        $attributeValue = ProductAttributeValues::find()->where(['attribute_value_id' => $id])->count();
        if (empty($attributeValue)) {
            $model = AttributeValues::find()
                    ->where(['attribute_value_id' => $id])
                    ->one();

            $model->delete();
            return json_encode(['status' => 200, 'message' => 'Success']);
        } else {
            return json_encode(['status' => 400, 'message' => "Cannot remove this attribute value because it is being used for {$attributeValue} products."]);
        }
    }

    public function actionEditableField($field) {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['id']);
            $model->$field = $request[$field];
            if ($model->save(false)) {
                return json_encode([
                    'status' => true,
                    'output' => $model->$field,
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'output' => ''
                ]);
            }
        }
    }

    /**
     * Finds the Attributes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attributes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Attributes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
