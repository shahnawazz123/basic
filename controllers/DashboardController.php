<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Orders;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use app\models\DoctorAppointments;
use app\models\Translator;
use app\models\Users;

/**
 * Description of DashboardController
 *
 * @author akram
 */
class DashboardController extends \yii\web\Controller
{
    //put your code here
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
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(2),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_CLINIC
                        ],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_DOCTOR
                        ],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_LAB
                        ],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_PHARMACY
                        ],
                    ],
                    [
                        'actions' => ['index'],
                        // 'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(1),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_TRANSLATOR
                        ],
                    ],

                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $yearStart = date("Y-m-d", strtotime('this year January 1st'));
        $yearEnd = date("Y-m-d", strtotime("this year December 31st"));
        $monthStart = date("Y-m-d", strtotime('first day of this month'));
        $monthEnd = date("Y-m-d", strtotime("last day of this month"));
        $weekStart = date("Y-m-d", strtotime('monday this week'));
        $weekEnd = date("Y-m-d", strtotime("sunday this week"));
        $today = date('Y-m-d');
        $weekStart = date("Y-m-d", strtotime($weekStart . "-1 days"));
        $weekEnd = date("Y-m-d", strtotime($weekEnd . "-1 days"));

        $result = [];
        /**** CLINIC EARNING ****/
        $queryClinicEarning = \app\models\DoctorAppointments::find()
            ->select([
                'SUM(doctor_appointments.amount) as total_amount',
                'SUM(doctor_appointments.admin_commission) as commission',
                '(SELECT SUM((doctor_appointments.amount * doctor_appointments.admin_commission) / 100) 
                        FROM doctor_appointments
                        WHERE admin_commission > 0 AND is_paid = 1 AND is_completed = 1) as total_commission'
            ])
            ->where(['doctor_appointments.is_deleted' => 0, 'is_completed' => 1])

            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')

            ->andWhere(['is_paid' => 1]);

        if (Yii::$app->session['_eyadatAuth'] == 2) {
            $queryClinicEarning->andWhere(['doctors.clinic_id' =>  Yii::$app->user->identity->clinic_id]);
        }

        $todayClinicQuery = clone $queryClinicEarning;
        $todayClinic = $todayClinicQuery->andWhere(['=', "DATE(doctor_appointments.appointment_datetime)", $today]);


        $todayEarnResult = $todayClinic->asArray()->all();

        $todayClinicEarn = ['total' => 0, 'commission' => 0];
        foreach ($todayEarnResult as $s) {
            $todayClinicEarn['total_amount'] = $s['total_amount'];
            $todayClinicEarn['total_commission'] =  $this->getTotalClinicCommission('today');
        }

        $weekClinicQuery = clone $queryClinicEarning;
        $weekClinic = $weekClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $weekStart, $weekEnd]);


        $weekEarnResult = $weekClinic->asArray()->all();

        $weekClinicEarn = ['total' => 0, 'commission' => 0];
        foreach ($weekEarnResult as $s) {
            $weekClinicEarn['total_amount'] = $s['total_amount'];
            $weekClinicEarn['total_commission'] =  $this->getTotalClinicCommission('week');
        }

        $monthClinicQuery = clone $queryClinicEarning;
        $monthClinic = $monthClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $monthStart, $monthEnd]);


        $monthEarnResult = $monthClinic->asArray()->all();

        $monthClinicEarn = ['total' => 0, 'commission' => 0];
        foreach ($monthEarnResult as $s) {
            $monthClinicEarn['total_amount'] = $s['total_amount'];
            $monthClinicEarn['total_commission'] =  $this->getTotalClinicCommission('month');
        }

        $yearClinicQuery = clone $queryClinicEarning;
        $yearClinic = $yearClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $yearStart, $yearEnd]);


        $yearEarnResult = $yearClinic->asArray()->all();

        $yearClinicEarn = ['total' => 0, 'commission' => 0];
        foreach ($yearEarnResult as $s) {
            $yearClinicEarn['total_amount'] = $s['total_amount'];
            $yearClinicEarn['total_commission'] =  $this->getTotalClinicCommission('year');
        }
        /**** CLINIC EARNING ****/

        /**** LAB EARNING ****/
        $queryLabEarning = \app\models\LabAppointments::find()
            ->select([
                'SUM(lab_appointments.amount) as total_amount'
            ])
            ->where(['lab_appointments.is_deleted' => 0, 'is_completed' => 1])
            ->andWhere(['lab_appointments.is_paid' => 1]);

        $todayLabQuery = clone $queryLabEarning;
        $todayLab = $todayLabQuery->andWhere(['=', "DATE(lab_appointments.appointment_datetime)", $today]);
        $todayEarnResult = $todayLab->asArray()->all();
        $todayLabEarn = ['total' => 0, 'commission' => 0];
        foreach ($todayEarnResult as $s) {
            $todayLabEarn['total_amount'] = $s['total_amount'];
            $todayLabEarn['total_commission'] =  $this->getTotalLabCommission('today');
        }

        $weekLabQuery = clone $queryLabEarning;
        $weekLab = $weekLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $weekStart, $weekEnd]);


        $weekEarnResult = $weekLab->asArray()->all();

        $weekLabEarn = ['total' => 0, 'commission' => 0];
        foreach ($weekEarnResult as $s) {
            $weekLabEarn['total_amount'] = $s['total_amount'];
            $weekLabEarn['total_commission'] =  $this->getTotalLabCommission('week');
        }

        $monthLabQuery = clone $queryLabEarning;
        $monthLab = $monthLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $monthStart, $monthEnd]);


        $monthEarnResult = $monthLab->asArray()->all();

        $monthLabEarn = ['total' => 0, 'commission' => 0];
        foreach ($monthEarnResult as $s) {
            $monthLabEarn['total_amount'] = $s['total_amount'];
            $monthLabEarn['total_commission'] =  $this->getTotalLabCommission('month');
        }

        $yearLabQuery = clone $queryLabEarning;
        $yearLab = $yearLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $yearStart, $yearEnd]);

        $yearEarnResult = $yearLab->asArray()->all();

        $yearLabEarn = ['total' => 0, 'commission' => 0];
        foreach ($yearEarnResult as $s) {
            $yearLabEarn['total_amount'] = $s['total_amount'];
            $yearLabEarn['total_commission'] =  $this->getTotalLabCommission('year');
        }
        /**** LAB EARNING ****/

        /*** Pharmacy Earning ****/
        $queryPharmacyEarning = Orders::find()
            ->select([
                'SUM(order_items.price*order_items.quantity) AS total_amount',
                'SUM((`order_items`.`price` * `order_items`.`quantity` * `pharmacy_orders`.`pharmacy_commission`) / 100) AS admin_commission'
            ])
            ->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id')
            ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
            ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
            ->join('left join', 'users', 'orders.user_id = users.user_id')
            ->join('left join', 'shipping_addresses', 'orders.shipping_address_id = shipping_addresses.shipping_address_id')
            ->join('LEFT JOIN', 'currencies', 'currencies.currency_id = order_items.currency_id')
            ->where(['is_processed' => 1, 'temp.status_id' => 5]);
        //echo $pharmEarn->createCommand()->rawSql;die;

        $todayPharmQuery = clone $queryPharmacyEarning;
        $todayPharm = $todayPharmQuery->andWhere(['=', "DATE(orders.create_date)", $today]);


        $todayPharmEarnResult = $todayPharm->asArray()->all();

        $todayPharmacyEarn = ['total' => 0, 'commission' => 0];
        foreach ($todayPharmEarnResult as $s) {
            $todayPharmacyEarn['total_amount'] = $s['total_amount'] - $s['admin_commission'];
            $todayPharmacyEarn['total_commission'] = $s['admin_commission'];
        }

        $weekPharmacyQuery = clone $queryPharmacyEarning;
        $weekPharmacy = $weekPharmacyQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $weekStart, $weekEnd]);


        $weekPharmEarnResult = $weekPharmacy->asArray()->all();

        $weekPharmacyEarn = ['total' => 0, 'commission' => 0];
        foreach ($weekPharmEarnResult as $s) {
            $weekPharmacyEarn['total_amount'] = $s['total_amount'] - $s['admin_commission'];
            $weekPharmacyEarn['total_commission'] = $s['admin_commission'];
        }

        $monthPharmacyQuery = clone $queryPharmacyEarning;
        $monthPharmacy = $monthPharmacyQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $monthStart, $monthEnd]);


        $monthPharmEarnResult = $monthPharmacy->asArray()->all();

        $monthPharmacyEarn = ['total' => 0, 'commission' => 0];
        foreach ($monthPharmEarnResult as $s) {
            $monthPharmacyEarn['total_amount'] = $s['total_amount'] - $s['admin_commission'];
            $monthPharmacyEarn['total_commission'] =  $s['admin_commission'];
        }

        $yearPharmacyQuery = clone $queryPharmacyEarning;
        $yearPharmacy = $yearPharmacyQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $yearStart, $yearEnd]);


        $yearPharmEarnResult = $yearPharmacy->asArray()->all();

        $yearPharmacyEarn = ['total' => 0, 'commission' => 0];
        foreach ($yearPharmEarnResult as $s) {
            $yearPharmacyEarn['total_amount'] = $s['total_amount'] - $s['admin_commission'];
            $yearPharmacyEarn['total_commission'] =   $s['admin_commission'];
        }
        /*** Pharmacy Earning ****/

        /*** DOCTOR APPOINTMENT ***/
        $queryApp = \app\models\DoctorAppointments::find()
            //->where(['doctor_appointments.is_deleted' => 0,'is_completed'=>1])
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            //->andWhere(['!=', 'is_paid', 0]);
            ->andWhere(['is_paid' => 1]);
        if (\Yii::$app->session['_eyadatAuth'] == 3) {
            $queryApp->andwhere(['doctor_appointments.doctor_id' => Yii::$app->user->identity->doctor_id]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 2) {
            $queryApp->andwhere(['doctors.clinic_id' => Yii::$app->user->identity->clinic_id]);
        }

        $todayAppointmentsQuery = clone $queryApp;
        $todayAppointments = $todayAppointmentsQuery->andWhere(['=', "DATE(doctor_appointments.appointment_datetime)", $today])
            ->count();

        $thisWeekAppointmentsQuery = clone $queryApp;
        $thisWeekAppointments = $thisWeekAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $weekStart, $weekEnd])->count();

        $thisMonthAppointmentsQuery = clone $queryApp;
        $thisMonthAppointments = $thisMonthAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $monthStart, $monthEnd])->count();

        $thisYearAppointmentsQuery = clone $queryApp;
        $thisYearAppointments = $thisYearAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $yearStart, $yearEnd])->count();

        $totalAppointmentsQuery = clone $queryApp;
        $totalAppointments = $totalAppointmentsQuery->count();

        /*** LAB APPOINTMENT***/
        $queryLabApp = \app\models\LabAppointments::find()
            //->where(['is_deleted' => 0,'is_completed'=>1])
            ->where(['is_deleted' => 0])
            ->andWhere(['is_paid' => 1]);
        //->andWhere(['!=', 'is_paid', 0]);
        if (\Yii::$app->session['_eyadatAuth'] == 4) {
            $queryLabApp->andwhere(['lab_id' => Yii::$app->user->identity->lab_id]);
        }

        $todayLabAppointmentsQuery = clone $queryLabApp;
        $todayLabAppointments = $todayLabAppointmentsQuery->andWhere(['=', "DATE(lab_appointments.appointment_datetime)", $today])
            ->count();

        $thisWeekLabAppointmentsQuery = clone $queryLabApp;
        $thisWeekLabAppointments = $thisWeekLabAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $weekStart, $weekEnd])->count();

        $thisMonthLabAppointmentsQuery = clone $queryLabApp;
        $thisMonthLabAppointments = $thisMonthLabAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $monthStart, $monthEnd])->count();

        $thisYearLabAppointmentsQuery = clone $queryLabApp;
        $thisYearLabAppointments = $thisYearLabAppointmentsQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $yearStart, $yearEnd])->count();
        $totalLabAppointmentsQuery = clone $queryLabApp;
        $totalLabAppointments = $totalLabAppointmentsQuery->count();
        /*** ORDER STATISTIC BY DATE ***/
        $query = \app\models\Orders::find();
        $query->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id');
        $query->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id');
        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM pharmacy_order_status AS t1
                                        LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.pharmacy_order_status_id < t2.pharmacy_order_status_id))
                                        WHERE t2.pharmacy_order_id IS NULL
                                        ) as temp2', 'pharmacy_orders.pharmacy_order_id = temp2.pharmacy_order_id');

        $query->where(['is_processed' => 1]);
        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andWhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
        }
        $query->groupBy('orders.order_id');

        $todayOrdersQuery = clone $query;
        $todayOrders = $todayOrdersQuery->andWhere(['!=', 'temp.status_id', 6])
            ->andWhere(['=', "DATE(orders.create_date)", $today])
            ->count();
        $thisWeekOrdersQuery = clone $query;
        $thisWeekOrders = $thisWeekOrdersQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $weekStart, $weekEnd])->count();
        $thisMonthQuery = clone $query;
        $thisMonthOrders = $thisMonthQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $monthStart, $monthEnd])->count();
        $thisYearQuery = clone $query;
        $thisYearOrders = $thisYearQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $yearStart, $yearEnd])->count();
        $totalOrdersQuery = clone $query;
        $totalOrders = $totalOrdersQuery->count();


        /*** TRANSLATOR APPOINTMENTS WITH DOCTOR AND PATIENT ***/

        $appointments = DoctorAppointments::find()
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            //->andWhere(['!=', 'is_paid', 0]);
            ->andWhere(['is_paid' => 1])
            ->andWhere(['translator_id' => Yii::$app->user->identity->translator_id]);

        $upcomingCount = clone $appointments;
        $notShowCount = clone $appointments;
        $completed = clone $appointments;
        $failed = clone $appointments;

        $upcoming = $upcomingCount->andWhere(['is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0, 'not_show' => 0])
            ->andWhere(['>', 'appointment_datetime', date('Y-m-d h:i:s')])->count();
        $notshow = $notShowCount->andWhere(['not_show' => 1, 'is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0])->count();
        $completed = $completed->andWhere(['is_completed' => 1, 'is_paid' => 1, 'is_cancelled' => 0])->count();
        $failed = $failed
            ->andWhere(['is_completed' => 0, 'is_cancelled' => 0])
            ->andwhere(['IN', 'is_paid', [0, 1, 2]])
            ->andWhere(['<', 'appointment_datetime', date('Y-m-d h:i:s')])->count();

        /*** TRANSLATOR APPOINTMENTS WITH DOCTOR AND PATIENT ***/


        if (\Yii::$app->session['_eyadatAuth'] == 1) {

            // ### Users Counts
            $usersQuery = \app\models\Users::find()->where(['is_deleted' => 0]);
            $usersCountQuery = clone $usersQuery;
            $usersAndroidQuery = clone $usersQuery;
            $usersIosQuery = clone $usersQuery;

            $userCount = $usersCountQuery->count();
            $userAndroid = $usersAndroidQuery->andWhere(['or', ['=', 'users.device_type', 'A'], ['=', 'users.device_type', 'Android']])->count();
            $userIos = $usersIosQuery->andWhere(['device_type' => 'I'])->count();


            // ### Clinic Counts
            $clinicQuery = \app\models\Clinics::find()->where(['is_deleted' => 0, 'type' => 'C']);
            $clinicCountQuery = clone $clinicQuery;
            $clinicActiveQuery = clone $clinicQuery;
            $clinicInActiveQuery = clone $clinicQuery;

            $clinicCount = $clinicCountQuery->count();
            $clinicActive = $clinicActiveQuery->andWhere(['is_active' => 1])->count();
            $clinicInActive = $clinicInActiveQuery->andWhere(['is_active' => 0])->count();


            // ### Hospital Counts
            $hospitalQuery = \app\models\Clinics::find()->where(['is_deleted' => 0, 'type' => 'H']);
            $hospitalCountQuery = clone $hospitalQuery;
            $hospitalActiveQuery = clone $hospitalQuery;
            $hospitalInActiveQuery = clone $hospitalQuery;

            $hospitalCount = $hospitalCountQuery->count();
            $hospitalActive = $hospitalActiveQuery->andWhere(['is_active' => 1])->count();
            $hospitalInActive = $hospitalInActiveQuery->andWhere(['is_active' => 0])->count();


            // ### doctor Counts
            $doctorQuery = \app\models\Doctors::find()->where(['is_deleted' => 0]);
            $doctorCountQuery = clone $doctorQuery;
            $doctorActiveQuery = clone $doctorQuery;
            $doctorInActiveQuery = clone $doctorQuery;

            $doctorCount = $doctorCountQuery->count();
            $doctorActive = $doctorActiveQuery->andWhere(['is_active' => 1])->count();
            $doctorInActive = $doctorInActiveQuery->andWhere(['is_active' => 0])->count();

            // ### labs Counts
            $labQuery = \app\models\Labs::find()->where(['is_deleted' => 0]);
            $labCountQuery = clone $labQuery;
            $labActiveQuery = clone $labQuery;
            $labInActiveQuery = clone $labQuery;

            $labCount = $labCountQuery->count();
            $labActive = $labActiveQuery->andWhere(['is_active' => 1])->count();
            $labInActive = $labInActiveQuery->andWhere(['is_active' => 0])->count();

            // ### Tests Counts
            $testQuery = \app\models\Tests::find()->where(['is_deleted' => 0]);
            $testCountQuery = clone $testQuery;
            $testActiveQuery = clone $testQuery;
            $testInActiveQuery = clone $testQuery;

            $testCount = $testCountQuery->count();
            $testActive = $testActiveQuery->andWhere(['is_active' => 1])->count();
            $testInActive = $testInActiveQuery->andWhere(['is_active' => 0])->count();

            // ### Pharmacies Counts
            $pharmaciesQuery = \app\models\Pharmacies::find()->where(['is_deleted' => 0]);
            $pharmaciesCountQuery = clone $pharmaciesQuery;
            $pharmaciesActiveQuery = clone $pharmaciesQuery;
            $pharmaciesInActiveQuery = clone $pharmaciesQuery;

            $pharmaciesCount = $pharmaciesCountQuery->count();
            $pharmaciesActive = $pharmaciesActiveQuery->andWhere(['is_active' => 1])->count();
            $pharmaciesInActive = $pharmaciesInActiveQuery->andWhere(['is_active' => 0])->count();
            $topSellingProducts = \app\models\Orders::find()
                ->select([
                    'product.name_en AS name',
                    'product.SKU AS sku',
                    'SUM(`price` * `quantity`) AS total_amount',
                    'SUM(order_items.cost_price * quantity) AS total_cost_amount',
                    'SUM((`price` * `quantity` * pharmacies.admin_commission) / 100) AS commission_amount'
                ])
                ->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id')
                ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'product', 'product.product_id = order_items.product_id')
                ->join('left join', 'pharmacies', 'product.pharmacy_id = pharmacies.pharmacy_id')
                ->where(['orders.is_processed' => 1])
                ->andWhere(['!=', 'temp.status_id', 6])
                ->groupBy('order_items.product_id')
                ->orderBy(['total_amount' => SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();

            return $this->render('index-v2', [
                'userCount' => $userCount,
                'userAndroid' => $userAndroid,
                'userIos' => $userIos,
                'clinicCount' => $clinicCount,
                'clinicActive' => $clinicActive,
                'clinicInActive' => $clinicInActive,
                'hospitalCount' => $hospitalCount,
                'hospitalActive' => $hospitalActive,
                'hospitalInActive' => $hospitalInActive,
                'doctorCount' => $doctorCount,
                'doctorActive' => $doctorActive,
                'doctorInActive' => $doctorInActive,
                'labCount' => $labCount,
                'labActive' => $labActive,
                'labInActive' => $labInActive,
                'testCount' => $testCount,
                'testActive' => $testActive,
                'testInActive' => $testInActive,
                'pharmaciesCount' => $pharmaciesCount,
                'pharmaciesActive' => $pharmaciesActive,
                'pharmaciesInActive' => $pharmaciesInActive,
                'topSellingProducts' => $topSellingProducts,
                'todayAppointments' => $todayAppointments,
                'thisWeekAppointments' => $thisWeekAppointments,
                'thisMonthAppointments' => $thisMonthAppointments,
                'thisYearAppointments' => $thisYearAppointments,
                'totalAppointments' => $totalAppointments,
                'todayLabAppointments' => $todayLabAppointments,
                'thisWeekLabAppointments' => $thisWeekLabAppointments,
                'thisMonthLabAppointments' => $thisMonthLabAppointments,
                'thisYearLabAppointments' => $thisYearLabAppointments,
                'totalLabAppointments' => $totalLabAppointments,
                'todayClinicEarn' => $todayClinicEarn,
                'weekClinicEarn' => $weekClinicEarn,
                'monthClinicEarn' => $monthClinicEarn,
                'yearClinicEarn' => $yearClinicEarn,
                'todayLabEarn' => $todayLabEarn,
                'weekLabEarn' => $weekLabEarn,
                'monthLabEarn' => $monthLabEarn,
                'yearLabEarn' => $yearLabEarn,
                'todayPharmacyEarn' => $todayPharmacyEarn,
                'weekPharmacyEarn' => $weekPharmacyEarn,
                'monthPharmacyEarn' => $monthPharmacyEarn,
                'yearPharmacyEarn' => $yearPharmacyEarn,
                'todayOrders' => $todayOrders,
                'thisWeekOrders' => $thisWeekOrders,
                'thisMonthOrders' => $thisMonthOrders,
                'thisYearOrders' => $thisYearOrders,
                'totalOrders' => $totalOrders,
            ]);
        } else if (\Yii::$app->session['_eyadatAuth'] == 2) {
            //echo \Yii::$app->session['_eyadatAuth'];die;

            $doctorQuery = \app\models\Doctors::find()->where([
                'is_deleted' => 0, "clinic_id" => Yii::$app->user->identity->clinic_id
            ]);
            $doctorCountQuery = clone $doctorQuery;
            $doctorActiveQuery = clone $doctorQuery;
            $doctorInActiveQuery = clone $doctorQuery;

            $doctorCount = $doctorCountQuery->count();
            $doctorActive = $doctorActiveQuery->andWhere(['is_active' => 1])->count();
            $doctorInActive = $doctorInActiveQuery->andWhere(['is_active' => 0])->count();


            return $this->render('index-c1', [
                'todayAppointments' => $todayAppointments,
                'thisWeekAppointments' => $thisWeekAppointments,
                'thisMonthAppointments' => $thisMonthAppointments,
                'thisYearAppointments' => $thisYearAppointments,
                'totalAppointments' => $totalAppointments,
                'doctorCount' => $doctorCount,
                'doctorActive' => $doctorActive,
                'doctorInActive' => $doctorInActive,
                'todayClinicEarn' => $todayClinicEarn,
                'weekClinicEarn' => $weekClinicEarn,
                'monthClinicEarn' => $monthClinicEarn,
                'yearClinicEarn' => $yearClinicEarn,
            ]);
        } else if (\Yii::$app->session['_eyadatAuth'] == 3) {
            return $this->render('index-d1', [
                'todayAppointments' => $todayAppointments,
                'thisWeekAppointments' => $thisWeekAppointments,
                'thisMonthAppointments' => $thisMonthAppointments,
                'thisYearAppointments' => $thisYearAppointments,
                'totalAppointments' => $totalAppointments,
            ]);
        } else if (\Yii::$app->session['_eyadatAuth'] == 8) {
            return $this->render('index-t1', [
                'appointments' => $appointments,
                'failed' => $failed,
                'completed' => $completed,
                'notshow' => $notshow,
                'upcoming' => $upcoming,
            ]);
        } else if (\Yii::$app->session['_eyadatAuth'] == 4) {
            return $this->render('index-l1', [
                'todayLabAppointments' => $todayLabAppointments,
                'thisWeekLabAppointments' => $thisWeekLabAppointments,
                'thisMonthLabAppointments' => $thisMonthLabAppointments,
                'thisYearLabAppointments' => $thisYearLabAppointments,
                'totalLabAppointments' => $totalLabAppointments,
            ]);
        } else if (\Yii::$app->session['_eyadatAuth'] == 5) {
            /*$query = \app\models\Orders::find();
            $query->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id');
            $query->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id
                                          AND (t1.status_date < t2.status_date
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id');
            $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM pharmacy_order_status AS t1
                                        LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id
                                                AND (t1.status_date < t2.status_date
                                                 OR (t1.status_date = t2.status_date AND t1.pharmacy_order_status_id < t2.pharmacy_order_status_id))
                                        WHERE t2.pharmacy_order_id IS NULL
                                        ) as temp2', 'pharmacy_orders.pharmacy_order_id = temp2.pharmacy_order_id');

            $query->where(['is_processed' => 1]);
            $query->andWhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
            $query->groupBy('orders.order_id');*/

            $pendingQuery = clone $query;
            $pending = $pendingQuery->andWhere(['=', 'temp2.pharmacy_status_id', 5])->count();

            $acceptedQuery = clone $query;
            $accepted = $acceptedQuery->andWhere(['=', 'temp2.pharmacy_status_id', 1])->count();

            $readyToDeliverQuery = clone $query;
            $readyToDeliver = $readyToDeliverQuery->andWhere(['=', 'temp2.pharmacy_status_id', 2])->count();

            $notAcceptedQuery = clone $query;
            $notAccepted = $notAcceptedQuery->andWhere(['=', 'temp2.pharmacy_status_id', 3])->count();

            $deliveredQuery = clone $query;
            $delivered = $deliveredQuery->andWhere(['=', 'temp2.pharmacy_status_id', 4])->count();

            $salesStatisticsQuery = clone $query;
            $salesStatisticsQuery->select([
                'SUM(order_items.price*order_items.quantity) AS total_amount',
                'SUM(order_items.price*order_items.quantity) AS total_order_amount',
                //'SUM(order_items.admin_commission_amt) AS total_admin_sale_commission',
                'SUM((`price` * `quantity` * pharmacies.admin_commission) / 100) AS commission_amount'
            ]);
            $salesStatisticsQuery->join('LEFT JOIN', 'pharmacies', 'pharmacy_orders.pharmacy_id = pharmacies.pharmacy_id');
            $salesStatisticsQuery->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id');
            $salesStatisticsQuery->andWhere(['!=', 'temp.status_id', 6]);
            $salesStatisticsQuery->andWhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
            $salesStatisticsQuery->groupBy(['orders.order_id']);

            $todaySalesStatisticsQuery = clone $salesStatisticsQuery;

            $salesStatisticsResult = $salesStatisticsQuery->asArray()->all();

            $salesStatistics = ['total' => 0, 'commission' => 0];
            foreach ($salesStatisticsResult as $s) {
                $salesStatistics['total'] += $s['total_amount'];
                //$salesStatistics['commission'] += $s['commission_amount'];
                $salesStatistics['commission'] += 0;
                //$salesStatistics['commission'] += $s['total_admin_sale_commission'];
            }

            $orders = Orders::find()
                ->select(['orders.order_id', 'orders.create_date', 'CONCAT(`users`.`first_name`, " ", `users`.`last_name`) AS name', 'SUM(convertPrice(`order_items`.`price`, `product`.`base_currency_id`, 82) * `order_items`.`quantity`) AS total_amount', 'currencies.name_en AS currency_code'])
                ->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id')
                ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'product', 'product.product_id = order_items.product_id')
                ->join('left join', 'users', 'orders.user_id = users.user_id')
                ->join('left join', 'stores', 'orders.store_id = stores.store_id')
                ->join('left join', 'currencies', 'stores.currency_id = currencies.currency_id')
                ->where(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id, 'orders.is_processed' => 1])
                ->andWhere(['!=', 'temp.status_id', 6])
                ->groupBy(['orders.order_id'])
                ->orderBy(['order_id' => SORT_DESC])
                ->limit(10)
                ->all();

            $topSellingProducts = \app\models\Orders::find()
                ->select([
                    'product.name_en AS name',
                    'product.SKU AS sku',
                    'SUM(`price` * `quantity`) AS total_amount',
                    'SUM(order_items.cost_price * quantity) AS total_cost_amount',
                    'SUM((`price` * `quantity` * pharmacies.admin_commission) / 100) AS commission_amount'
                ])
                ->join('LEFT JOIN', '(SELECT t1.*
                                    FROM order_status AS t1
                                    LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                    WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id')
                ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'product', 'product.product_id = order_items.product_id')
                ->join('left join', 'pharmacies', 'product.pharmacy_id = pharmacies.pharmacy_id')
                ->where(['orders.is_processed' => 1, 'pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id])
                ->andWhere(['!=', 'temp.status_id', 6])
                ->groupBy('order_items.product_id')
                ->orderBy(['total_amount' => SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();

            $todaySalesStatisticsQuery->andWhere([
                'DATE(orders.create_date)' => date('Y-m-d')
            ]);
            $todaySalesStatisticsResult = $todaySalesStatisticsQuery->asArray()
                ->all();
            $todaySalesStatistics = ['total' => 0, 'commission' => 0, 'total_order_amount' => 0];
            if (!empty($todaySalesStatisticsResult)) {
                foreach ($todaySalesStatisticsResult as $s1) {
                    $todaySalesStatistics['total'] += $s1['total_amount'];
                    $todaySalesStatistics['commission'] += $s1['commission_amount'];
                    $todaySalesStatistics['total_order_amount'] += $s1['total_order_amount'];
                }
            }

            $weeklySalesStatisticsQuery = clone $salesStatisticsQuery;
            $weeklySalesStatisticsQuery->andWhere(['BETWEEN', 'DATE(orders.create_date)', $weekStart, $weekEnd]);
            $weeklySalesStatisticsResult = $weeklySalesStatisticsQuery->asArray()->all();

            $weeklySalesStatistics = ['total' => 0, 'commission' => 0, 'total_order_amount' => 0];
            if (!empty($weeklySalesStatisticsResult)) {
                foreach ($weeklySalesStatisticsResult as $s1) {
                    $weeklySalesStatistics['total'] += $s1['total_amount'];
                    $weeklySalesStatistics['commission'] += $s1['commission_amount'];
                    $weeklySalesStatistics['total_order_amount'] += $s1['total_order_amount'];
                }
            }

            /*$todayOrdersQuery = clone $query;
            $todayOrders = $todayOrdersQuery->andWhere(['!=', 'temp.status_id', 6])
                    ->andWhere(['=', "DATE(orders.create_date)", $today])
                    ->count();

            $thisWeekOrdersQuery = clone $query;
            $thisWeekOrders = $thisWeekOrdersQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $weekStart, $weekEnd])->count();

            $thisMonthQuery = clone $query;
            $thisMonthOrders = $thisMonthQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $monthStart, $monthEnd])->count();

            $thisYearQuery = clone $query;
            $thisYearOrders = $thisYearQuery->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $yearStart, $yearEnd])->count();

            $totalOrdersQuery = clone $query;
            $totalOrders = $totalOrdersQuery->count();*/


            return $this->render('index-p1', [
                'salesStatistics' => $salesStatistics,
                'topSellingProducts' => $topSellingProducts,
                'todaySalesStatistics' => $todaySalesStatistics,
                'weeklySalesStatistics' => $weeklySalesStatistics,
                'todayOrders' => $todayOrders,
                'thisWeekOrders' => $thisWeekOrders,
                'thisMonthOrders' => $thisMonthOrders,
                'thisYearOrders' => $thisYearOrders,
                'totalOrders' => $totalOrders,
            ]);
        }
    }

    private function getTotalClinicCommission($act)
    {
        $yearStart = date("Y-m-d", strtotime('this year January 1st'));
        $yearEnd = date("Y-m-d", strtotime("this year December 31st"));
        $monthStart = date("Y-m-d", strtotime('first day of this month'));
        $monthEnd = date("Y-m-d", strtotime("last day of this month"));
        $weekStart = date("Y-m-d", strtotime('monday this week'));
        $weekEnd = date("Y-m-d", strtotime("sunday this week"));
        $today = date('Y-m-d');
        $weekStart = date("Y-m-d", strtotime($weekStart . "-1 days"));
        $weekEnd = date("Y-m-d", strtotime($weekEnd . "-1 days"));
        $queryClinicEarning = \app\models\DoctorAppointments::find()
            ->select([
                'SUM((doctor_appointments.amount * doctor_appointments.admin_commission) / 100) as total_commission'
            ])
            ->where(['doctor_appointments.is_deleted' => 0, 'is_completed' => 1])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            ->andWhere(['is_paid' => 1]);


        if ($act == 'today') {
            $todayClinicQuery = clone $queryClinicEarning;
            $todayClinic = $todayClinicQuery->andWhere(['=', "DATE(doctor_appointments.appointment_datetime)", $today]);


            $todayEarnResult = $todayClinic->asArray()->all();
            $commission = 0;
            foreach ($todayEarnResult as $s) {
                $commission =  $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'week') {
            $weekClinicQuery = clone $queryClinicEarning;
            $weekClinic = $weekClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $weekStart, $weekEnd]);


            $weekEarnResult = $weekClinic->asArray()->all();

            $commission = 0;
            foreach ($weekEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'month') {
            $monthClinicQuery = clone $queryClinicEarning;
            $monthClinic = $monthClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $monthStart, $monthEnd]);


            $monthEarnResult = $monthClinic->asArray()->all();

            $commission = 0;
            foreach ($monthEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'year') {
            $yearClinicQuery = clone $queryClinicEarning;
            $yearClinic = $yearClinicQuery->andWhere(['BETWEEN', 'DATE(`doctor_appointments`.`appointment_datetime`)', $yearStart, $yearEnd]);


            $yearEarnResult = $yearClinic->asArray()->all();

            $commission = 0;
            foreach ($yearEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
    }

    private function getTotalLabCommission($act)
    {
        $yearStart = date("Y-m-d", strtotime('this year January 1st'));
        $yearEnd = date("Y-m-d", strtotime("this year December 31st"));
        $monthStart = date("Y-m-d", strtotime('first day of this month'));
        $monthEnd = date("Y-m-d", strtotime("last day of this month"));
        $weekStart = date("Y-m-d", strtotime('monday this week'));
        $weekEnd = date("Y-m-d", strtotime("sunday this week"));
        $today = date('Y-m-d');
        $weekStart = date("Y-m-d", strtotime($weekStart . "-1 days"));
        $weekEnd = date("Y-m-d", strtotime($weekEnd . "-1 days"));
        $queryLabEarning = \app\models\LabAppointments::find()
            ->select([
                'SUM((lab_appointments.amount * lab_appointments.admin_commission) / 100) as total_commission'
            ])
            ->where(['lab_appointments.is_deleted' => 0, 'is_completed' => 1])
            ->andWhere(['is_paid' => 1]);


        if ($act == 'today') {
            $todayLabQuery = clone $queryLabEarning;
            $todayLab = $todayLabQuery->andWhere(['=', "DATE(lab_appointments.appointment_datetime)", $today]);


            $todayEarnResult = $todayLab->asArray()->all();
            $commission = 0;
            foreach ($todayEarnResult as $s) {
                $commission =  $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'week') {
            $weekLabQuery = clone $queryLabEarning;
            $weekLab = $weekLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $weekStart, $weekEnd]);


            $weekEarnResult = $weekLab->asArray()->all();

            $commission = 0;
            foreach ($weekEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'month') {
            $monthLabQuery = clone $queryLabEarning;
            $monthLab = $monthLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $monthStart, $monthEnd]);


            $monthEarnResult = $monthLab->asArray()->all();

            $commission = 0;
            foreach ($monthEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
        if ($act == 'year') {
            $yearLabQuery = clone $queryLabEarning;
            $yearLab = $yearLabQuery->andWhere(['BETWEEN', 'DATE(`lab_appointments`.`appointment_datetime`)', $yearStart, $yearEnd]);


            $yearEarnResult = $yearLab->asArray()->all();

            $commission = 0;
            foreach ($yearEarnResult as $s) {
                $commission = $s['total_commission'];
            }
            return $commission;
        }
    }
}
