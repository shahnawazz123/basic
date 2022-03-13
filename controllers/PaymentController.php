<?php

namespace app\controllers;

use app\helpers\AppHelper;
use app\models\AssociatedProducts;
use app\models\OrderItems;
use app\models\ProductAttributeValues;
use app\models\ProductSearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use app\models\Payment;
use app\models\VendorPayment;
use app\models\PaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
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
                'only' => ['index', ''],
                'rules' => [
                    [
                        'actions' => '',//\app\helpers\PermissionHelper::getUserPermissibleAction(25),
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
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPharmacySaleReport()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('pharmacy_report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ''
        ]);
    }

    public function actionClinicCommissionReport() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->DoctorAppointmentSearch(Yii::$app->request->queryParams);
        return $this->render('clinic_commission_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLabCommissionReport() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->LabAppointmentSearch(Yii::$app->request->queryParams);
        return $this->render('lab_commission_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPharmacyCommissionReport() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('pharmacy_commission_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
}
