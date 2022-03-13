<?php

namespace app\controllers;

use Yii;
use app\models\PasswordForm;
use app\models\Admin;
use app\models\Doctor;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

class ProfileController extends \yii\web\Controller {

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['', ''],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(23),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN,
                            /*UserIdentity::ROLE_SHOP,
                            UserIdentity::ROLE_SHOP_ADMIN,*/
                        ]
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionEdit() {
        $model = new PasswordForm();
        if (\Yii::$app->session['_eyadatAuth'] == 1) {
            $model->scenario = 'admin-edit-profile';
            $userModel = Admin::find()
                    ->where(['admin_id' => Yii::$app->user->identity->admin_id, 'is_deleted' => 0, 'is_active' => 1])
                    ->one();
            $model->name = $userModel->name;
            $model->email = $userModel->email;
            $model->phone = $userModel->phone;
            $model->image = $userModel->image;
        } 
        elseif (\Yii::$app->session['_eyadatAuth'] == 2) {
            $model->scenario = 'shop-edit-profile';
            $userModel = \app\models\Pharmacies::find()
                    ->where(['shop_id' => Yii::$app->user->identity->shop_id, 'is_deleted' => 0, 'is_active' => 1])
                    ->one();
            $model->name_en = $userModel->name_en;
            $model->name_ar = $userModel->name_ar;
            $model->image = $userModel->logo;
        }
        elseif (\Yii::$app->session['_eyadatAuth'] == 4) {
            $model->scenario = 'doctor-edit-profile';
            $userModel = \app\models\ShopAdmins::find()
                    ->where(['shop_admin_id' => Yii::$app->user->identity->shop_admin_id, 'is_deleted' => 0, 'is_active' => 1])
                    ->one();
            $model->name_en = $userModel->name_en;
            $model->name_ar = $userModel->name_ar;
        }elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
            $model->scenario = 'store-edit-profile';
            $userModel = \app\models\Pharmacies::find()
                    ->where(['pharmacy_id' => Yii::$app->user->identity->pharmacy_id, 'is_deleted' => 0, 'is_active' => 1])
                    ->one();
            $model->name_en = $userModel->name_en;
            $model->name_ar = $userModel->name_ar;
        }
        if ($model->load(\Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            //debugPrint($request);exit;
            if ($model->validate()) {
                if (isset($model->repeatNewPass) && $model->repeatNewPass != "") {
                    $userModel->password = Yii::$app->security->generatePasswordHash($model->repeatNewPass);
                }
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $userModel->phone = $model->phone;
                    $userModel->image = $request['PasswordForm']['image'];
                    $name = $model->name;
                } elseif (\Yii::$app->session['_eyadatAuth'] == 2) {
                    $userModel->name_en = $model->name_en;
                    $userModel->name_ar = $model->name_ar;
                    //$userModel->country_id = $model->country_id;
                    $userModel->logo = $request['PasswordForm']['image'];
                    $name = $model->name_en;
                }
                elseif (\Yii::$app->session['_eyadatAuth'] == 4) {
                    $userModel->name_en = $model->name_en;
                    $userModel->name_ar = $model->name_ar;
                    $name = $model->name_en;
                }
                if ($userModel->save(false)) {
                    Yii::$app->getSession()->setFlash('success', 'Profile changes saved.');
                    return $this->redirect(['edit']);
                } else {
                    die($userModel->errors);
                }
            } else {
                return $this->render('edit', [
                            'model' => $model
                ]);
            }
        } else {
            return $this->render('edit', [
                        'model' => $model
            ]);
        }
    }

}
