<?php

namespace app\controllers;

use Yii;
use app\models\AttributeSets;
use app\models\AttributeSetsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\AttributeSetGroups;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * AttributeSetController implements the CRUD actions for AttributeSets model.
 */
class AttributeSetController extends Controller
{

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
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(28),
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
     * Lists all AttributeSets models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AttributeSetsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AttributeSets model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AttributeSets model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new AttributeSets();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;

            if ($model->has_size_guide != 1) {
                $model->size_guide_image_en = null;
                $model->size_guide_image_ar = null;
            }

            if ($model->save()) {

                $attributeCode = [];

                foreach ($request['AttributeSets']['attributes_id'] as $attributes) {
                    $attributeSetGroup = new AttributeSetGroups();
                    $attributeSetGroup->attribute_set_id = $model->attribute_set_id;
                    $attributeSetGroup->attribute_id = $attributes;
                    $attributeSetGroup->save();
                    $attributeCode [] = $attributeSetGroup->attribute0->code;
                }

                if (!empty($attributeCode)) {
                    $attributeSetCode = implode('-', $attributeCode);
                    $model->attribute_set_code = $attributeSetCode . '-' . $model->attribute_set_id;
                    $model->save();
                }
                Yii::$app->session->setFlash('success', 'Attribute set successfully added');
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
     * Updates an existing AttributeSets model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $oldImage = $model->size_guide_image_en;
        $oldImageAr = $model->size_guide_image_ar;

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;

            if ($model->has_size_guide != 1) {
                $model->size_guide_image_en = NULL;
                $model->size_guide_image_ar = NULL;
            } else {
                $newImage = $model->size_guide_image_en;
                $newImageAr = $model->size_guide_image_ar;
            }

            if ($model->save()) {
                if (!empty($oldImage) && $newImage != $oldImage && file_exists(Yii::$app->basePath . '/web/uploads/' . $oldImage)) {
                    @unlink(Yii::$app->basePath . '/web/uploads/' . $oldImage);
                }

                if (!empty($oldImageAr) && $newImageAr != $oldImageAr && file_exists(Yii::$app->basePath . '/web/uploads/' . $oldImageAr)) {
                    @unlink(Yii::$app->basePath . '/web/uploads/' . $oldImageAr);
                }

                AttributeSetGroups::deleteAll('attribute_set_id = ' . $model->attribute_set_id);
                foreach ($request['AttributeSets']['attributes_id'] as $attributes) {
                    $attributeSetGroup = new AttributeSetGroups();
                    $attributeSetGroup->attribute_set_id = $model->attribute_set_id;
                    $attributeSetGroup->attribute_id = $attributes;
                    $attributeSetGroup->save();

                    $attributeCode [] = $attributeSetGroup->attribute0->code;
                }


                if (!empty($attributeCode)) {
                    $attributeSetCode = implode('-', $attributeCode);
                    $model->attribute_set_code = $attributeSetCode . '-' . $model->attribute_set_id;
                    $model->save();
                }
                Yii::$app->session->setFlash('success', 'Attribute set successfully updated');
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
     * Deletes an existing AttributeSets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        AttributeSetGroups::deleteAll('attribute_set_id = ' . $id);
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', 'Attribute set successfully deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the AttributeSets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AttributeSets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = AttributeSets::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
