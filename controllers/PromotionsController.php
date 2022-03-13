<?php

namespace app\controllers;

use Yii;
use app\models\Promotions;
use app\models\PromotionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * PromotionsController implements the CRUD actions for Promotions model.
 */
class PromotionsController extends Controller
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
                'only' => ['index', 'view', 'delete', 'update', 'create'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(24),
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
     * Lists all Promotions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Promotions model.
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
     * Creates a new Promotions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promotions();

        if ($model->load(Yii::$app->request->post())) 
        {
            $request = Yii::$app->request->bodyParams;
            $promo_for = $request['Promotions']['promo_for'];
            $model->start_date = (!empty($request['Promotions']['start_date'])) ? $request['Promotions']['start_date'] : date('Y-m-d');
            $model->end_date = (!empty($request['Promotions']['end_date'])) ? $request['Promotions']['end_date'] : date('Y-m-d',strtotime(' + 365 days'));
            if($model->save())
            {
                if($promo_for == 'D')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionDoctors();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->doctor_id = $id;
                            $pModel->save(false);
                        }
                    }
                }else if($promo_for == 'C')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionClinics();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->clinic_id = $id;
                            $pModel->save(false);
                        }
                    }
                }else if($promo_for == 'L')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionLabs();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->lab_id = $id;
                            $pModel->save(false);
                        }
                    }
                }else if($promo_for == 'F')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionPharmacy();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->pharmacy_id = $id;
                            $pModel->save(false);
                        }
                    }
                }

                Yii::$app->session->setFlash('success', 'Promotions successfully added');
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
     * Updates an existing Promotions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) 
        {
            $request = Yii::$app->request->bodyParams;
            $promo_for = $request['Promotions']['promo_for'];
            $model->start_date = (!empty($request['Promotions']['start_date'])) ? $request['Promotions']['start_date'] : date('Y-m-d');
            $model->end_date = (!empty($request['Promotions']['end_date'])) ? $request['Promotions']['end_date'] : date('Y-m-d',strtotime(' + 365 days'));
                if(!empty($model->users_id)){
                    foreach($model->users_id as $user){
                        $userModel = new \app\models\PromotionUsers();
                        $userModel->promotion_id = $model->promotion_id;
                        $userModel->user_id = $user;
                        $userModel->save(false);
                    }
                }

            if($model->save(false))
            {
                \app\models\PromotionClinics::deleteAll('promotion_id = '.$model->promotion_id);
                \app\models\PromotionDoctors::deleteAll('promotion_id = '.$model->promotion_id);
                \app\models\PromotionLabs::deleteAll('promotion_id = '.$model->promotion_id);
                \app\models\PromotionPharmacy::deleteAll('promotion_id = '.$model->promotion_id);

                if($promo_for == 'D')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionDoctors();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->doctor_id = $id;
                            $pModel->save(false);
                        }
                    }
                }else if($promo_for == 'C')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionClinics();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->clinic_id = $id;
                            $pModel->save(false);
                        }
                    }
                }else if($promo_for == 'L')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionLabs();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->lab_id = $id;
                            $pModel->save(false);
                        }
                    }
                }
                else if($promo_for == 'F')
                {
                    if (!empty($request['Promotions']['link_id'])) 
                    {
                        foreach ($request['Promotions']['link_id'] as $id) 
                        {
                            $pModel = new \app\models\PromotionPharmacy();
                            $pModel->promotion_id = $model->promotion_id;
                            $pModel->pharmacy_id = $id;
                            $pModel->save(false);
                        }
                    }
                }

                Yii::$app->session->setFlash('success', 'Promotions successfully updated');
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
     * Deletes an existing Promotions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Promotions successfully deleted');
        return $this->redirect(['index']);
    }
    

    /**
     * Finds the Promotions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promotions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promotions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
