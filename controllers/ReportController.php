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
 * ReportController implements the CRUD actions for Payment model.
 */
class ReportController extends Controller
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
    
    public function actionDoctorAppointment() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->DoctorAppointmentSearch(Yii::$app->request->queryParams);
        return $this->render('doctor_appointment_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLabAppointment() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->LabAppointmentSearch(Yii::$app->request->queryParams);
        return $this->render('lab_appointment_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCommissionReport() {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('commission_report', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
}
