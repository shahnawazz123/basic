<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\AccessRule;
use app\components\UserIdentity;
use app\models\Orders;
use app\models\OrdersSearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\helpers\AppHelper;
use kartik\mpdf\Pdf;
class PharmacyOrderController extends \yii\web\Controller
{

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view'],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_PHARMACY
                        ]
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->pharmacy_order_ready_for_search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReadyForDelivery()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->pharmacy_order_picked_by_driver_search(Yii::$app->request->queryParams);
        return $this->render('picked_orders', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrders()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->pharmacy_order_search(Yii::$app->request->queryParams);
        return $this->render('orders', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) 
    {
        $model = \app\models\PharmacyOrders::findOne($id);
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    public function actionOrderView($id) 
    {
        $model = \app\models\PharmacyOrders::findOne($id);
        return $this->render('order_view', [
                    'model' => $model,
        ]);
    }

}
