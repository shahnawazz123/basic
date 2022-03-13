<?php

namespace app\controllers;

use Yii;
use app\models\Pharmacies;
use app\models\PharmaciesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class PharmaciesController extends Controller
{
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
                            UserIdentity::ROLE_PHARMACY,
                        ]
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $searchModel = new PharmaciesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionCreate()
    {
        $model = new Pharmacies();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $password = $request['Pharmacies']['password'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            if (!empty($request['Pharmacies']['accepted_payment_method'])) {
                $model->accepted_payment_method = (!empty($request['Pharmacies']['accepted_payment_method'])) ? implode(',', $request['Pharmacies']['accepted_payment_method']) : '';
            }
            $model->country_id = $request['Pharmacies']['country_id'];
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Pharmacies successfully added');
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Pharmacies']['password'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            $model->updated_at = date('Y-m-d H:i:s');
            if (isset($request['Pharmacies']['password']) && $request['Pharmacies']['password'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Pharmacies']['password']);
            }
            if (!empty($request['Pharmacies']['accepted_payment_method'])) {
                $model->accepted_payment_method = (!empty($request['Pharmacies']['accepted_payment_method'])) ? implode(',', $request['Pharmacies']['accepted_payment_method']) : '';
            }
            $model->country_id = $request['Pharmacies']['country_id'];
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Pharmacies successfully updated');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Pharmacies not updated');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Pharmacies successfully deleted');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Exceptions');
            return $this->redirect(['index']);
        }
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


    protected function findModel($id)
    {
        if (($model = Pharmacies::findOne($id)) !== null) {
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

    public function actionSendPush($id, $msg, $title = "")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Pharmacy does not exist'
                ]);
            } else {
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = "";
                $notification->target   = "F";
                $notification->target_id = $model->pharmacy_id;
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, '', "F", $model->pharmacy_id, $title, '', $model->name_en, $model->name_ar);

                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }

    public function actionProfile()
    {
        if (Yii::$app->session['_eyadatAuth'] == 5) {
            $id = Yii::$app->user->identity->pharmacy_id;
        } else {
            return $this->redirect(['dashboard/index']);
        }
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $model->updated_at = date('Y-m-d H:i:s');
            if (isset($request['Pharmacies']['password']) && $request['Pharmacies']['password'] != "") {
                $model->password = Yii::$app->getSecurity()->generatePasswordHash($request['Pharmacies']['password']);
            }
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Profile successfully updated');
                return $this->redirect(['pharmacies/profile']);
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
}
