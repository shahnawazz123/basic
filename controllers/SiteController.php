<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Clinics;
use app\models\LoginForm;
use app\models\ContactForm;
use app\helpers\AppHelper;

class SiteController extends Controller
{

    use \app\modules\api\traits\EcommerceTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        } else {
            return $this->redirect(['dashboard/index']);
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return "Is Not guest";
            //  return $this->redirect(['dashboard/index']);
        }
        //        exit;
        $model = new LoginForm();
        $domain = strtolower($_SERVER['SERVER_NAME']);
        $position = strrpos($domain, '.' . '3eyadat.com');
        $subdomain = substr($domain, 0, $position);
        if (!empty($subdomain)) {
            $subdomain = $subdomain;
        } else {
            $subdomain = "admin";
        }
        //return $subdomain;
        /* if ($model->load(Yii::$app->request->post()) && $model->login()) {
          return $this->goBack();
          } */
        $subdomain_arr = array("clinic", "doctor", "lab", "store", "translator", "doctor-dev", "lab-dev", "clinic-dev", "translator-dev", "store-dev");
        if (!in_array($subdomain, $subdomain_arr)) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            }
            return $this->renderAjax('login', [
                'model' => $model,
            ]);
        } else {
            if ($model->load(Yii::$app->request->post()) && $subdomain == 'clinic' && $model->login(2)) {
                return $this->goBack();
            } else if ($model->load(Yii::$app->request->post()) && $subdomain == 'doctor' && $model->login(3)) {
                return $this->goBack();
            } else if ($model->load(Yii::$app->request->post()) && $subdomain == 'doctor-dev' && $model->login(3)) {
                return $this->goBack();
            } else if ($model->load(Yii::$app->request->post()) && $subdomain == 'lab' && $model->login(4)) {
                return $this->goBack();
            }else if ($model->load(Yii::$app->request->post()) && $subdomain == 'lab-dev' && $model->login(4)) {
                return $this->goBack();
            }else if ($model->load(Yii::$app->request->post()) && $subdomain == 'clinic-dev' && $model->login(4)) {
                return $this->goBack();
            }else if ($model->load(Yii::$app->request->post()) && $subdomain == 'store-dev' && $model->login(4)) {
                return $this->goBack();
            }else if ($model->load(Yii::$app->request->post()) && $subdomain == 'translator-dev' && $model->login(4)) {
                return $this->goBack();
            } else if ($model->load(Yii::$app->request->post()) && $subdomain == 'store' && $model->login(5)) {
                return $this->goBack();
            } else if ($model->load(Yii::$app->request->post()) &&  $subdomain == 'translator' && $model->login(8)) {
                // return $this->redirect(['translator/index']);
                return $this->goBack();
            }
            return $this->renderAjax('login', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionGetTableInfo()
    {
        $sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = \'eyadat\';';
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        $data = '';
        foreach ($result as $row) {
            $name = explode('_', $row['table_name']);
            $tableName = $row['table_name'];
            $modelName = '';
            $modelNameSearch = '';
            $modelNameController = '';
            foreach ($name as $n) {
                $modelName .= ucfirst($n);
                $modelNameSearch = $modelName . 'Search';
                $modelNameController = $modelName . 'Controller';
            }
            //$data.= ".\yii gii/model --tableName=$tableName --modelClass=$modelName;<br/>";
            $data .= ".\yii gii/crud --modelClass=app\models\\$modelName --searchModelClass=app\models\\$modelNameSearch --controllerClass=app\controllers\\$modelNameController;<br/>";
        }
        return $data;
    }

    public function actionTapResponse()
    {
        $this->layout = false;
        if (isset($_REQUEST['tap_id']) && $_REQUEST['tap_id'] != "") {
            $headers = array(
                'Content-Type: application/json;charset=utf-8',
                //"authorization: Bearer sk_test_lBfNL4SvWXEPqr5RU3oY6aA7",
                "authorization: Bearer sk_test_O1IDmadnBKh7A6MqG32CUu4F",
            );
            $url = 'https://api.tap.company/v2/charges/' . $_REQUEST['tap_id'];
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $res = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $result = json_decode($res, true);
            if ($err) {
                $result_url = Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                $result_params = "?PaymentID={$_REQUEST['paymentid']}&Result=&PostDate=&TranID=&Auth=&Ref=&TrackID=&UDF1=&UDF2=&UDF3=&UDF4=&UDF5=&status_name_en=&status_name_ar=&user_wallet_amount=&order_id=";
                return $this->redirect($result_url . $result_params);
            } else {
                $PaymentID = (!empty($result['reference']['payment'])) ? $result['reference']['payment'] : 'cancel_pay_' . Yii::$app->security->generateRandomString();
                $presult = (!empty($result['InvoiceStatus'])) ? $result['InvoiceStatus'] : '';
                $postdate = (!empty($result['transaction']['created'])) ? date('Y-m-d H:i:s') : '';
                $tranid = (!empty($result['reference']['transaction'])) ? $result['reference']['transaction'] : '';
                $auth = (!empty($result['reference']['gateway'])) ? $result['reference']['gateway'] : '';
                $ref = (!empty($result['reference']['acquirer'])) ? $result['reference']['acquirer'] : '';
                $trackid = (!empty($result['reference']['track'])) ? $result['reference']['track'] : '';
                $udf1 = (isset($result['metadata']['udf1'])) ? $result['metadata']['udf1'] : '';
                $udf2 = (isset($result['metadata']['udf2'])) ? $result['metadata']['udf2'] : '';
                $udf3 = (isset($result['metadata']['udf3'])) ? $result['metadata']['udf3'] : '';
                $udf4 = (isset($result['metadata']['udf4'])) ? $result['metadata']['udf4'] : '';
                $udf5 = (isset($result['metadata']['udf5'])) ? $result['metadata']['udf5'] : '';
                $totalPay = '';
                //
                $paymentModel = \app\models\Payment::find()
                    ->where(['payment_id' => $udf1])
                    ->one();
                if (!empty($paymentModel)) {
                    $paymentModel->payment_date = date("Y-m-d H:i:s");
                    $paymentModel->result = $presult;
                    $paymentModel->transaction_id = $tranid;
                    $paymentModel->auth = $auth;
                    $paymentModel->ref = $ref;
                    $paymentModel->tap_charge_id = $result['id'];
                    $paymentModel->save(false);

                    $totalPay = $paymentModel->udf5;
                    //
                    $redirect_url = '';
                    $payment_type_id = $paymentModel->type_id;
                    $payment_type = $paymentModel->type;
                    //
                    $udf1 = $paymentModel->udf1;
                    $udf2 = $paymentModel->udf2;
                    $udf3 = $paymentModel->udf3;
                    $udf4 = $paymentModel->udf4;
                    $udf5 = $paymentModel->udf5;
                    //
                    if ($paymentModel->type == 'O') {
                        $store = \app\models\Stores::find()
                            ->where(['store_id' => $udf3])
                            ->asArray()
                            ->one();
                        $model = \app\models\Orders::findOne($paymentModel->type_id);
                        $cartDetails = $this->cartDetails($paymentModel->type_id, $udf4, $store, 1, false);
                        if ($model->is_processed == 0) {
                            $this->_deductOrderProductStock($model, $cartDetails);
                        }
                        $cod_cost = (float) (!empty($model->cod_charge)) ? $this->convertPrice($model->cod_charge, 82, $store['currency_id']) : 0;
                        $deliveryCharges = (float) (!empty($model->delivery_charge)) ? $this->convertPrice($model->delivery_charge, 82, $store['currency_id']) : 0;
                        $vatCharges = (float) (!empty($model->vat_charges)) ? $this->convertPrice($model->vat_charges, 82, $store['currency_id']) : 0;
                        $vatPct = !empty($model->shippingAddress->country->vat) ? $model->shippingAddress->country->vat : 0;
                        $discountPrice = (float) (!empty($model->discount_price)) ? $this->convertPrice($model->discount_price, 82, $store['currency_id']) : 0;
                    }
                    if ($paymentModel->result == 'CAPTURED') {
                        $paymentModel->status = 1;
                        $paymentModel->save(false);
                        if ($paymentModel->type == 'DA') {
                            $model = \app\models\DoctorAppointments::findOne($paymentModel->type_id);
                            $model->is_paid = 1;
                            $model->updated_at = date('Y-m-d H:i:s');
                            $model->save(false);

                            Yii::$app->mailer->compose('@app/mail/doctor-appointment', [
                                'model' => $model,
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($model->user->email)
                                ->setSubject('Doctor Appointment Booked ')
                                ->send();
                        }
                        if ($paymentModel->type == 'LA') {
                            $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                            $model->is_paid = 1;
                            $model->updated_at = date('Y-m-d H:i:s');
                            $model->save(false);
                            $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                            $model->is_paid = 1;
                            $model->updated_at = date('Y-m-d H:i:s');
                            $model->save(false);

                            Yii::$app->mailer->compose('@app/mail/lab-appointment', [
                                'model' => $model,
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($model->user->email)
                                ->setSubject('Lab Appointment Booked ')
                                ->send();
                        }
                        if ($paymentModel->type == 'O') {
                            $model->is_processed = 1;
                            $model->is_paid = 1;
                            $model->update_date = date('Y-m-d H:i:s');
                            $model->save(false);
                            $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                            $total = $subTotal = 0;
                            if (isset($cartDetails['items'])) {
                                foreach ($cartDetails['items'] as $item) {
                                    $subTotal += ($item['final_price'] * $item['quantity']);
                                    /*
                                     * Removing stock from the stock movement table
                                     * */
                                    $productStockModel = new \app\models\ProductStocks();
                                    $productStockModel->product_id = $item['id'];
                                    $productStockModel->quantity = -$item['quantity'];
                                    $productStockModel->message = "Removing stock to fulfill the order #{$model->order_number}. Remaining quantity is {$productStockModel->product->remaining_quantity}.";
                                    $productStockModel->created_date = date('Y-m-d H:i:s');
                                    $productStockModel->save(false);
                                    //
                                }
                            }
                            $promotion = \app\models\Promotions::findOne($model->promotion_id);
                            $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                            $discount = 0;
                            $discountPrice = 0;
                            if (isset($model->discount) && !empty($model->discount)) {
                                $discount = $model->discount;
                                $discountPrice = ($subTotal * $model->discount) / 100;
                            }
                            if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                                $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                                if ($subTotal >= $minimumOrderConverted) {
                                    $discountPrice = ($subTotal * $model->discount) / 100;
                                } else {
                                    $discountPrice = 0;
                                }
                            }
                            $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                            $defaultStatus = $this->getDefaultOrderStatus();
                            $orderStatusModel = new \app\models\OrderStatus();
                            $orderStatusModel->order_id = $model->order_id;
                            $orderStatusModel->status_id = 2; //ACCEPTED
                            $orderStatusModel->status_date = date("Y-m-d H:i:s");
                            $orderStatusModel->user_type = 'U';
                            $orderStatusModel->user_id = $model->user_id;
                            $orderStatusModel->comment = 'Initial status after successful payment';
                            $orderStatusModel->save(false);
                            //
                            $subject = 'Thank you! your 3eyadat order #' . $model->order_number . ' has been placed';
                            $baseCurrencyName = $baseCurrency->code_en;
                            try {
                                Yii::$app->mailer->compose('@app/mail/checkout', [
                                    "cartDetails" => $cartDetails,
                                    'baseCurrencyName' => $baseCurrency->code_en,
                                    'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                                    'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                                    'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                                    'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                                    'name' => $model->user->first_name . ' ' . $model->user->last_name,
                                    'order_number' => $model->order_number,
                                    'payment_mode' => $model->payment_mode,
                                    'order_date' => $model->create_date,
                                    'vat_pct' => (string) $vatPct,
                                    'vat_charges' => (string) number_format($vatCharges, $decimals),
                                    'discount_price' => $discountPrice,
                                ])
                                    ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                    ->setTo($model->user->email)
                                    ->setSubject($subject)
                                    ->send();

                                Yii::$app->mailer->compose('@app/mail/order-details', [
                                    "cartDetails" => $cartDetails,
                                    'baseCurrencyName' => $baseCurrency->code_en,
                                    'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                                    'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                                    'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                                    'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                                    'name' => $model->user->first_name . " " . $model->user->last_name,
                                    'user' => $model->user,
                                    'order_number' => $model->order_number,
                                    'order' => $model,
                                    'payment_mode' => $model->payment_mode,
                                    'shippingAddress' => $model->shippingAddress,
                                    'vat_pct' => (string) $vatPct,
                                    'vat_charges' => (string) number_format($vatCharges, $decimals),
                                    'discount_price' => $discountPrice,
                                ])
                                    ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                    ->setTo(Yii::$app->params['adminEmail'])
                                    ->setSubject("3eyadat order Confirmation #{$model->order_number}")
                                    ->send();
                            } catch (\Exception $e) {
                            }
                        }
                        $result_url = (!empty($redirect_url)) ? $redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/success');
                        $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" . $udf2 . "&UDF3=" . $udf3 . "&UDF4=" . $udf4 . "&UDF5=" . $udf5 . "&type=$payment_type&type_id=$payment_type_id";
                    } else {
                        $paymentModel->status = 0;
                        $paymentModel->save(false);
                        //
                        if ($paymentModel->type == 'DA') {
                            $model = \app\models\DoctorAppointments::findOne($paymentModel->type_id);
                            $model->is_paid = 2;
                            $model->updated_at = date('Y-m-d H:i:s');
                            $model->save(false);
                        }
                        if ($paymentModel->type == 'LA') {
                            $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                            $model->is_paid = 2;
                            $model->updated_at = date('Y-m-d H:i:s');
                            $model->save(false);
                        }
                        if ($paymentModel->type == 'O') {
                            $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                            $oldIsProcessed = $model->is_processed;
                            $model->is_processed = 3;
                            $model->update_date = date('Y-m-d H:i:s');
                            $model->save();
                            foreach ($model->pharmacyOrders as $pharmacyOrder) {
                                if (!empty($pharmacyOrder)) {
                                    foreach ($pharmacyOrder->orderItems as $item) {
                                        if ($oldIsProcessed == 2) {
                                            $product = \app\models\Product::findOne($item->product_id);
                                            $product->updateCounters(['remaining_quantity' => $item->quantity]);
                                            AppHelper::adjustStock($item->product_id, 0, "Restoring {$item->quantity} quantity for order #{$model->order_number}. Remaining quantity is {$product->remaining_quantity}. : Ottu Response");
                                        }
                                    }
                                }
                            }
                            $total = $subTotal = 0;
                            if (isset($cartDetails['items'])) {
                                foreach ($cartDetails['items'] as $item) {
                                    $subTotal += ($item['final_price'] * $item['quantity']);
                                }
                            }
                            $promotion = \app\models\Promotions::findOne($model->promotion_id);
                            $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                            $discount = 0;
                            $discountPrice = 0;
                            if (isset($model->discount) && !empty($model->discount)) {
                                $discount = $model->discount;
                                $discountPrice = ($subTotal * $model->discount) / 100;
                            }
                            if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                                $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                                if ($subTotal >= $minimumOrderConverted) {
                                    $discountPrice = ($subTotal * $model->discount) / 100;
                                } else {
                                    $discountPrice = 0;
                                }
                            }
                            $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                            $orderStatusModel = new \app\models\OrderStatus();
                            $orderStatusModel->order_id = $model->order_id;
                            $orderStatusModel->status_id = 6;
                            $orderStatusModel->status_date = date('Y-m-d H:i:s');
                            $orderStatusModel->user_type = 'U';
                            $orderStatusModel->user_id = $model->user_id;
                            $orderStatusModel->comment = 'Payment failure';
                            $orderStatusModel->save(false);
                            //
                            try {
                                Yii::$app->mailer->compose('@app/mail/checkout', [
                                    "payment" => $paymentModel,
                                    "cartDetails" => $cartDetails,
                                    'baseCurrencyName' => $baseCurrency->code_en,
                                    'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrency->code_en),
                                    'total' => (string) AppHelper::formatPrice($total, $baseCurrency->code_en),
                                    'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrency->code_en),
                                    'cod_cost' => (string) $cod_cost,
                                    'name' => $model->user->first_name . " " . $model->user->last_name,
                                    'user' => $model->user,
                                    'order_number' => $model->order_number,
                                    'order' => $model,
                                    'payment_mode' => $model->payment_mode,
                                    'order_date' => $model->create_date,
                                    'vat_pct' => (string) $vatPct,
                                    'vat_charges' => (string) AppHelper::formatPrice($vatCharges, $baseCurrency->code_en),
                                    'discount_price' => $discountPrice,
                                ])
                                    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['appName']])
                                    ->setTo($model->user->email)
                                    ->setSubject("Error while purchasing from 3eyadat")
                                    ->send();
                            } catch (\Exception $e) {
                            }
                        }

                        $result_url = (!empty($redirect_url)) ? $redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                        $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" . $udf2 . "&UDF3=" . $udf3 . "&UDF4=" . $udf4 . "&UDF5=" . $udf4 . "&type=$payment_type&type_id=$payment_type_id";
                    }
                    return $this->redirect($result_url . $result_params);
                } else {
                    $result_url = Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                    $result_params = "?PaymentID={$_REQUEST['paymentid']}&Result=&PostDate=&TranID=&Auth=&Ref=&TrackID=&UDF1=&UDF2=&UDF3=&UDF4=&UDF5=&type=&type_id=";
                    return $this->redirect($result_url . $result_params);
                }
            }
        }
    }

    public function actionSuccess()
    {
        debugPrint($_REQUEST);
    }

    public function actionFailed()
    {
        debugPrint($_REQUEST);
    }

    private function _deductOrderProductStock($order, $cartDetails)
    {
        if ($order->is_processed == 0) {
            $order->is_processed = 2;
            $order->update_date = date('Y-m-d H:i:s');
            $order->save(false);
        }
        foreach ($cartDetails['items'] as $k => $item) {
            $product = \app\models\Product::findOne($item['id']);
            if ((($product->remaining_quantity - $item['quantity']) < Yii::$app->params['bufferQty'])) {
                $product->updateCounters(['remaining_quantity' => $item['quantity']]);
                AppHelper::adjustStock($item['id'], $item['quantity'], "Adjusting " . ($item['quantity']) . " quantity for order #{$order->order_number}. Remaining quantity is {$product->remaining_quantity}. : site/_deductOrderProductStock");
            }
            $product->updateCounters(['remaining_quantity' => -$item['quantity']]);
            AppHelper::adjustStock($item['id'], 0, "Holding " . (-$item['quantity']) . " quantity for order #{$order->order_number}. Remaining quantity is {$product->remaining_quantity}. : site/_deductOrderProductStock");
        }
    }

    public function actionMyfatoorahExecutePaymentResponse($paymentId)
    {
        $token = Yii::$app->params['myfatoorahExecuteToken'];
        $host = Yii::$app->params['myfatoorahUrl'];
        $basURL = "https://$host.myfatoorah.com";
        $_data = [
            'Key' => $paymentId,
            'KeyType' => 'PaymentId',
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($_data),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ),
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($response);
        //echo "<pre>ss";print_r($result);
        //debugPrint($result->Data->InvoiceReference);exit;
        if (!empty($result) && $result->IsSuccess == 1) {
            $paymentModel = \app\models\Payment::find()
                ->where(['ref' => $result->Data->CustomerReference])
                ->one();
            $model = \app\models\Orders::findOne($paymentModel->order_id);
            $presult = ($result->Data->InvoiceStatus == 'Paid') ? 'CAPTURED' : 'NOT CAPTURED';
            $transaction_id = $result->Data->InvoiceReference;
            $paymentId = $result->Data->InvoiceId;
            //
            $paymentModel->payment_date = date("Y-m-d H:i:s");
            $paymentModel->result = $presult;
            $paymentModel->PaymentID = $paymentId;
            $paymentModel->transaction_id = $transaction_id;
            $paymentModel->payment_response = $result->IsSuccess;
            $paymentModel->save(false);
            //
            $PaymentID = $paymentModel->PaymentID;
            $ref = $paymentModel->ref;
            $tranid = $paymentModel->transaction_id;
            $auth = $paymentModel->auth;
            $trackid = $paymentModel->TrackID;
            $postdate = date("Y-m-d H:i:s");
            //
            $udf1 = $paymentModel->udf1;
            $udf2 = $paymentModel->udf2;
            $udf3 = $paymentModel->udf3;
            $udf4 = $paymentModel->udf4;
            $udf5 = $paymentModel->udf5;
            //
            $store = \app\models\Stores::find()->where(['store_id' => $udf3])->asArray()->one();
            $cartDetails = SiteHelper::cartDetails($udf1, $udf4, $store, 1, false);
            if ($model->is_processed == 0) {
                $this->_deductOrderProductStock($model, $cartDetails);
            }
            $cod_cost = (float) (!empty($model->cod_charge)) ? \app\helpers\SiteHelper::convertPrice($model->cod_charge, 82, $store['currency_id']) : 0;
            $deliveryCharges = (float) (!empty($model->delivery_charge)) ? \app\helpers\SiteHelper::convertPrice($model->delivery_charge, 82, $store['currency_id']) : 0;
            $vatCharges = (float) (!empty($model->vat_charges)) ? \app\helpers\SiteHelper::convertPrice($model->vat_charges, 82, $store['currency_id']) : 0;
            $vatPct = !empty($model->shippingAddress->country->vat) ? $model->shippingAddress->country->vat : 0;
            $discountPrice = (float) (!empty($model->discount_price)) ? \app\helpers\SiteHelper::convertPrice($model->discount_price, 82, $store['currency_id']) : 0;
            $walletAmountApplied = (float) (!empty($model->wallet_amount)) ? \app\helpers\SiteHelper::convertPrice($model->wallet_amount, 82, $store['currency_id']) : 0;
            //
            if ($paymentModel->result == 'CAPTURED') {
                $model->is_processed = 1;
                $model->is_paid = 1;
                $model->update_date = date('Y-m-d H:i:s');
                $model->save(false);
                $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                $total = $subTotal = 0;
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        /*
                         * Removing stock from the stock movement table
                         * */
                        $productStockModel = new \app\models\ProductStocks();
                        $productStockModel->product_id = $item['id'];
                        $productStockModel->quantity = -$item['quantity'];
                        $productStockModel->message = "Removing stock to fulfill the order #{$model->order_number}. Remaining quantity is {$productStockModel->product->remaining_quantity}.";
                        $productStockModel->created_date = date('Y-m-d H:i:s');
                        $productStockModel->save(false);
                        //
                        if ($item['is_preorder'] == 1) {
                            if ($item['pre_order_qty'] === abs($item['remaining_quantity'])) {
                                $productModel = Product::findOne($item['id']);
                                $productModel->is_preorder = 0;
                                $productModel->save(false);
                            }
                        }
                    }
                }
                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                $discount = 0;
                $discountPrice = 0;
                if (isset($model->discount) && !empty($model->discount)) {
                    $discount = $model->discount;
                    $discountPrice = ($subTotal * $model->discount) / 100;
                }
                if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                    $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                    if ($subTotal >= $minimumOrderConverted) {
                        $discountPrice = ($subTotal * $model->discount) / 100;
                    } else {
                        $discountPrice = 0;
                    }
                }
                $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                $defaultStatus = AppHelper::getDefaultOrderStatus();
                $orderStatusModel = new \app\models\OrderStatus();
                $orderStatusModel->order_id = $model->order_id;
                //$orderStatusModel->status_id = $defaultStatus->status_id; //PENDING
                $orderStatusModel->status_id = 2; //ACCEPTED
                $orderStatusModel->status_date = date("Y-m-d H:i:s");
                $orderStatusModel->user_type = 'U';
                $orderStatusModel->user_id = $model->user_id;
                $orderStatusModel->comment = 'Initial status after successful payment';
                $orderStatusModel->save(false);
                //
                if ($model->is_wallet_applied == 1) {
                    $wallet = new \app\models\Wallet();
                    $wallet->user_id = $model->user_id;
                    $wallet->amount = -$model->wallet_amount;
                    $wallet->type = 'SUB';
                    $wallet->created_at = date("Y-m-d H:i:s");
                    $wallet->transaction_type = 'O';
                    $wallet->transaction_type_id = $model->order_id;
                    $wallet->save();
                }
                \app\helpers\PromoHelper::checkActivePromoForGift($model, $cartDetails);
                $cartDetails = SiteHelper::cartDetails($udf1, $udf4, $store, 1, false);
                $model = \app\models\Orders::findOne($paymentModel->order_id);
                /*
                 * Adding order to inventory
                 * */
                if (Yii::$app->params['enable_inventory_sync']) {
                    RestHelper::pushOrder($model);
                }
                try {
                    Yii::$app->mailer->compose('@app/mail/checkout', [
                        "payment" => $paymentModel,
                        "cartDetails" => $cartDetails,
                        'baseCurrencyName' => $baseCurrency->code,
                        'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrency->code),
                        'total' => (string) AppHelper::formatPrice($total, $baseCurrency->code),
                        'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrency->code),
                        'cod_cost' => (string) $cod_cost,
                        'name' => $model->user->first_name . " " . $model->user->last_name,
                        'user' => $model->user,
                        'order_number' => $model->order_number,
                        'order' => $model,
                        'payment_mode' => $model->payment_mode,
                        'order_date' => $model->create_date,
                        'vat_pct' => (string) $vatPct,
                        'vat_charges' => (string) AppHelper::formatPrice($vatCharges, $baseCurrency->code),
                        'discount_price' => $discountPrice,
                        'wallet_amount_applied' => $walletAmountApplied,
                        'is_wallet_applied' => $model->is_wallet_applied,
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->user->email)
                        ->setSubject("Thank you for your purchase")
                        ->send();

                    Yii::$app->mailer->compose('@app/mail/order-details', [
                        "payment" => $paymentModel,
                        "cartDetails" => $cartDetails,
                        'baseCurrencyName' => $baseCurrency->code,
                        'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrency->code),
                        'total' => (string) AppHelper::formatPrice($total, $baseCurrency->code),
                        'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrency->code),
                        'cod_cost' => (string) $cod_cost,
                        'name' => $model->user->first_name . " " . $model->user->last_name,
                        'user' => $model->user,
                        'order_number' => $model->order_number,
                        'order' => $model,
                        'payment_mode' => $model->payment_mode,
                        'shippingAddress' => $model->shippingAddress,
                        'vat_pct' => (string) $vatPct,
                        'vat_charges' => (string) AppHelper::formatPrice($vatCharges, $baseCurrency->code),
                        'discount_price' => $discountPrice,
                        'wallet_amount_applied' => $walletAmountApplied,
                        'is_wallet_applied' => $model->is_wallet_applied,
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo(Yii::$app->params['adminEmail'])
                        ->setSubject("The Eyadat order Confirmation #{$model->order_number}")
                        ->send();
                } catch (\Exception $e) {
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/runtime/emailException.txt', "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . PHP_EOL, FILE_APPEND);
                }
                //
                if (!empty($model->shopOrders)) {
                    foreach ($model->shopOrders as $shopOrder) {
                        $shopModel = \app\models\Shops::findOne($shopOrder->shop_id);
                        if ($shopModel->enable_automate_order_status == 1) {
                            $shopStatusCheck = \app\models\ShopOrderStatus::find()
                                ->where(['shop_order_id' => $shopOrder->shop_order_id, 'shop_status_id' => 4])
                                ->one();
                            if (empty($shopStatusCheck)) {
                                //insert new shop status
                                $shopStatus = new \app\models\ShopOrderStatus();
                                $shopStatus->shop_order_id = $shopOrder->shop_order_id;
                                $shopStatus->shop_status_id = 4;
                                $shopStatus->user_type = 'S';
                                $shopStatus->user_id = $shopOrder->shop_id;
                                $shopStatus->status_date = date('Y-m-d H:i:s');
                                $shopStatus->comment = 'Automatically added by system';
                                $shopStatus->save();
                            }
                        }
                        if ($shopModel->enable_notification == '1') {
                            try {
                                Yii::$app->mailer->compose('@app/mail/shop-order-details', [
                                    "cartDetails" => $cartDetails,
                                    'baseCurrencyName' => $baseCurrency->code,
                                    'order_number' => $shopOrder->order_number,
                                    'name' => $shopModel->name_en,
                                    'order' => $model,
                                    'shopOrder' => $shopOrder,
                                    'shopModel' => $shopModel,
                                ])
                                    ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                    ->setTo($shopModel->email)
                                    ->setSubject("The Eyadat order Confirmation #{$shopOrder->order_number}")
                                    ->send();
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
                $result_url = (!empty($model->redirect_url)) ? $model->redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/success');
                $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $model->order_id . "&UDF2=&UDF3=&UDF4=&UDF5=&status_name_en=&status_name_ar=";
            } else {
                $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                $model = \app\models\Orders::findOne($udf1);
                $oldIsProcessed = $model->is_processed;
                $model->is_processed = 3;
                $model->update_date = date('Y-m-d H:i:s');
                $model->save();
                foreach ($model->shopOrders as $shopOrder) {
                    if (!empty($shopOrder)) {
                        foreach ($shopOrder->orderItems as $item) {
                            if ($oldIsProcessed == 2) {
                                $product = \app\models\Product::findOne($item->product_id);
                                $product->updateCounters(['remaining_quantity' => $item->quantity]);
                                AppHelper::adjustStock($item->product_id, 0, "Restoring {$item->quantity} quantity for order #{$model->order_number}. Remaining quantity is {$product->remaining_quantity}. : Ottu Response");
                            }
                        }
                    }
                }
                $total = $subTotal = 0;
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                    }
                }
                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                $discount = 0;
                $discountPrice = 0;
                if (isset($model->discount) && !empty($model->discount)) {
                    $discount = $model->discount;
                    $discountPrice = ($subTotal * $model->discount) / 100;
                }
                if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                    $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                    if ($subTotal >= $minimumOrderConverted) {
                        $discountPrice = ($subTotal * $model->discount) / 100;
                    } else {
                        $discountPrice = 0;
                    }
                }
                $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                $orderStatusModel = new OrderStatus();
                $orderStatusModel->order_id = $model->order_id;
                $orderStatusModel->status_id = 6;
                $orderStatusModel->status_date = date('Y-m-d H:i:s');
                $orderStatusModel->user_type = 'U';
                $orderStatusModel->user_id = $model->user_id;
                $orderStatusModel->comment = 'Payment failure';
                $orderStatusModel->save(false);

                try {
                    Yii::$app->mailer->compose('@app/mail/checkout', [
                        "payment" => $paymentModel,
                        "cartDetails" => $cartDetails,
                        'baseCurrencyName' => $baseCurrency->code,
                        'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrency->code),
                        'total' => (string) AppHelper::formatPrice($total, $baseCurrency->code),
                        'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrency->code),
                        'cod_cost' => (string) $cod_cost,
                        'name' => $model->user->first_name . " " . $model->user->last_name,
                        'user' => $model->user,
                        'order_number' => $model->order_number,
                        'order' => $model,
                        'payment_mode' => $model->payment_mode,
                        'order_date' => $model->create_date,
                        'vat_pct' => (string) $vatPct,
                        'vat_charges' => (string) AppHelper::formatPrice($vatCharges, $baseCurrency->code),
                        'discount_price' => $discountPrice,
                        'wallet_amount_applied' => $walletAmountApplied,
                        'is_wallet_applied' => $model->is_wallet_applied,
                    ])
                        ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->user->email)
                        ->setSubject("Error while purchasing from The Wish List")
                        ->send();
                } catch (\Exception $e) {
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/runtime/emailException.txt', "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . PHP_EOL, FILE_APPEND);
                }
                $result_url = (!empty($model->redirect_url)) ? $model->redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $model->order_id . "&UDF2=&UDF3=&UDF4=&UDF5=&status_name_en=&status_name_ar=";
            }
            $redirectUrl = $result_url . $result_params;
            return $this->redirect($redirectUrl);
        }
    }


    public function actionFathorahTapResponse()
    {
        $this->layout = false;

        $token = Yii::$app->params['myfatoorahExecuteToken'];
        $host = Yii::$app->params['myfatoorahUrl'];
        $basURL = "https://$host.myfatoorah.com";
        $_data = [
            'Key' => $_REQUEST['paymentId'],
            'KeyType' => 'PaymentId',
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($_data),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ),
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($response);

        // echo 'ss'.$result['InvoiceTransactions']['PaymentId'];
        //echo "<pre>";print_r($result);
        //echo $result->Data->InvoiceTransactions[0]->AuthorizationId;die;
        //debugPrint($result->Data->InvoiceReference);exit;
        if (!empty($result) && $result->IsSuccess == 1) {
            $paymentModel = \app\models\Payment::find()
                ->where(['ref' => $result->Data->CustomerReference])
                ->one();
            $model = \app\models\Orders::findOne($paymentModel->type_id);
            $presult = ($result->Data->InvoiceStatus == 'Paid') ? 'CAPTURED' : 'NOT CAPTURED';
            $transaction_id = $result->Data->InvoiceReference;
            $paymentId = $result->Data->InvoiceId;
            $AuthorizationId = $result->Data->InvoiceTransactions[0]->AuthorizationId;
            //
            $paymentModel->payment_date = date("Y-m-d H:i:s");
            $paymentModel->result = $presult;
            $paymentModel->PaymentID = $paymentId;
            $paymentModel->transaction_id = $transaction_id;
            $paymentModel->payment_response = $result->IsSuccess;
            $paymentModel->save(false);
            //
            $PaymentID = $paymentModel->PaymentID;
            $ref = $paymentModel->ref;
            $tranid = $paymentModel->transaction_id;
            $auth = $AuthorizationId;; //$paymentModel->auth;
            $trackid = $paymentModel->TrackID;
            $postdate = date("Y-m-d H:i:s");
            //
            $udf1 = $paymentModel->udf1;
            $udf2 = $paymentModel->udf2;
            $udf3 = $paymentModel->udf3;
            $udf4 = $paymentModel->udf4;
            $udf5 = $paymentModel->udf5;

            $totalPay = '';
            //
            $paymentModel = \app\models\Payment::find()
                //->where(['payment_id' => $udf1])
                ->where(['udf1' => $udf1])
                ->one();
            if (!empty($paymentModel)) {
                $paymentModel->payment_date = date("Y-m-d H:i:s");
                $paymentModel->result = $presult;
                $paymentModel->transaction_id = $tranid;
                $paymentModel->auth = $auth;
                $paymentModel->ref = $ref;
                $paymentModel->tap_charge_id = ''; //$result['id'];
                $paymentModel->save(false);

                $totalPay = $paymentModel->udf5;
                //
                $redirect_url = '';
                $payment_type_id = $paymentModel->type_id;
                $payment_type = $paymentModel->type;
                //
                $udf1 = $paymentModel->udf1;
                $udf2 = $paymentModel->udf2;
                $udf3 = $paymentModel->udf3;
                $udf4 = $paymentModel->udf4;
                $udf5 = $paymentModel->udf5;
                //
                if ($paymentModel->type == 'O') {
                    $store = \app\models\Stores::find()
                        ->where(['store_id' => $udf3])
                        ->asArray()
                        ->one();
                    $model = \app\models\Orders::findOne($paymentModel->type_id);
                    $cartDetails = $this->cartDetails($paymentModel->type_id, $udf4, $store, 1, false);
                    if ($model->is_processed == 0) {
                        $this->_deductOrderProductStock($model, $cartDetails);
                    }
                    $cod_cost = (float) (!empty($model->cod_charge)) ? $this->convertPrice($model->cod_charge, 82, $store['currency_id']) : 0;
                    $deliveryCharges = (float) (!empty($model->delivery_charge)) ? $this->convertPrice($model->delivery_charge, 82, $store['currency_id']) : 0;
                    $vatCharges = (float) (!empty($model->vat_charges)) ? $this->convertPrice($model->vat_charges, 82, $store['currency_id']) : 0;
                    $vatPct = !empty($model->shippingAddress->country->vat) ? $model->shippingAddress->country->vat : 0;
                    $discountPrice = (float) (!empty($model->discount_price)) ? $this->convertPrice($model->discount_price, 82, $store['currency_id']) : 0;
                }
                if ($paymentModel->result == 'CAPTURED') {
                    $paymentModel->status = 1;
                    $paymentModel->save(false);
                    if ($paymentModel->type == 'DA') {
                        $model = \app\models\DoctorAppointments::findOne($paymentModel->type_id);
                        $model->is_paid = 1;
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->save(false);

                        Yii::$app->mailer->compose('@app/mail/doctor-appointment', [
                            'model' => $model,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($model->user->email)
                            ->setSubject('Doctor Appointment Booked ')
                            ->send();
                    }
                    if ($paymentModel->type == 'LA') {
                        $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                        $model->is_paid = 1;
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->save(false);
                        $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                        $model->is_paid = 1;
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->save(false);

                        Yii::$app->mailer->compose('@app/mail/lab-appointment', [
                            'model' => $model,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($model->user->email)
                            ->setSubject('Lab Appointment Booked ')
                            ->send();
                    }
                    if ($paymentModel->type == 'O') {

                        $model->is_processed = 1;
                        $model->is_paid = 1;
                        $model->update_date = date('Y-m-d H:i:s');
                        $model->save(false);
                        $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                        $total = $subTotal = 0;

                        $decimals = 2;
                        if ($baseCurrency->code_en == 'BHD' || $baseCurrency->code_en == 'KWD') {
                            $decimals = 3;
                        }


                        if (isset($cartDetails['items'])) {
                            foreach ($cartDetails['items'] as $item) {
                                $subTotal += ($item['final_price'] * $item['quantity']);
                                /*
                                     * Removing stock from the stock movement table
                                     * */
                                $productStockModel = new \app\models\ProductStocks();
                                $productStockModel->product_id = $item['id'];
                                $productStockModel->quantity = -$item['quantity'];
                                $productStockModel->message = "Removing stock to fulfill the order #{$model->order_number}. Remaining quantity is {$productStockModel->product->remaining_quantity}.";
                                $productStockModel->created_date = date('Y-m-d H:i:s');
                                $productStockModel->save(false);
                                //
                            }
                        }
                        $promotion = \app\models\Promotions::findOne($model->promotion_id);
                        $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                        $discount = 0;
                        $discountPrice = 0;
                        if (isset($model->discount) && !empty($model->discount)) {
                            $discount = $model->discount;
                            $discountPrice = ($subTotal * $model->discount) / 100;
                        }
                        if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                            $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                            if ($subTotal >= $minimumOrderConverted) {
                                $discountPrice = ($subTotal * $model->discount) / 100;
                            } else {
                                $discountPrice = 0;
                            }
                        }
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                        $defaultStatus = $this->getDefaultOrderStatus();
                        $orderStatusModel = new \app\models\OrderStatus();
                        $orderStatusModel->order_id = $model->order_id;
                        $orderStatusModel->status_id = 2; //ACCEPTED
                        $orderStatusModel->status_date = date("Y-m-d H:i:s");
                        $orderStatusModel->user_type = 'U';
                        $orderStatusModel->user_id = $model->user_id;
                        $orderStatusModel->comment = 'Initial status after successful payment';
                        $orderStatusModel->save(false);
                        //
                        $codCost = 0;
                        $subject = 'Thank you! your 3eyadat order #' . $model->order_number . ' has been placed';
                        $baseCurrencyName = $baseCurrency->code_en;
                        try {
                            Yii::$app->mailer->compose('@app/mail/checkout', [
                                "cartDetails" => $cartDetails,
                                'baseCurrencyName' => $baseCurrency->code_en,
                                'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                                'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                                'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                                'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                                'name' => $model->user->first_name . ' ' . $model->user->last_name,
                                'order_number' => $model->order_number,
                                'payment_mode' => $model->payment_mode,
                                'order_date' => $model->create_date,
                                'vat_pct' => (string) $vatPct,
                                'vat_charges' => (string) number_format($vatCharges, $decimals),
                                'discount_price' => $discountPrice,
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($model->user->email)
                                ->setSubject($subject)
                                ->send();

                            Yii::$app->mailer->compose('@app/mail/order-details', [
                                "cartDetails" => $cartDetails,
                                'baseCurrencyName' => $baseCurrency->code_en,
                                'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                                'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                                'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                                'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                                'name' => $model->user->first_name . " " . $model->user->last_name,
                                'user' => $model->user,
                                'order_number' => $model->order_number,
                                'order' => $model,
                                'payment_mode' => $model->payment_mode,
                                'shippingAddress' => $model->shippingAddress,
                                'vat_pct' => (string) $vatPct,
                                'vat_charges' => (string) number_format($vatCharges, $decimals),
                                'discount_price' => $discountPrice,
                                'wallet_amount_applied' => '',
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo(Yii::$app->params['adminEmail'])
                                ->setSubject("3eyadat order Confirmation #{$model->order_number}")
                                ->send();
                        } catch (\Exception $e) {
                        }
                    }
                    $result_url = (!empty($redirect_url)) ? $redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/success');
                    $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" . $udf2 . "&UDF3=" . $udf3 . "&UDF4=" . $udf4 . "&UDF5=" . $udf5 . "&type=$payment_type&type_id=$payment_type_id";
                    return $this->redirect($result_url . $result_params);
                } else {
                    $paymentModel->status = 0;
                    $paymentModel->save(false);
                    //
                    if ($paymentModel->type == 'DA') {
                        $model = \app\models\DoctorAppointments::findOne($paymentModel->type_id);
                        $model->is_paid = 2;
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->save(false);
                    }
                    if ($paymentModel->type == 'LA') {
                        $model = \app\models\LabAppointments::findOne($paymentModel->type_id);
                        $model->is_paid = 2;
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->save(false);
                    }
                    if ($paymentModel->type == 'O') {
                        $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                        $oldIsProcessed = $model->is_processed;
                        $model->is_processed = 3;
                        $model->update_date = date('Y-m-d H:i:s');
                        $model->save(false);
                        foreach ($model->pharmacyOrders as $pharmacyOrder) {
                            if (!empty($pharmacyOrder)) {
                                foreach ($pharmacyOrder->orderItems as $item) {
                                    if ($oldIsProcessed == 2) {
                                        $product = \app\models\Product::findOne($item->product_id);
                                        $product->updateCounters(['remaining_quantity' => $item->quantity]);
                                        AppHelper::adjustStock($item->product_id, 0, "Restoring {$item->quantity} quantity for order #{$model->order_number}. Remaining quantity is {$product->remaining_quantity}. : Ottu Response");
                                    }
                                }
                            }
                        }
                        $total = $subTotal = 0;

                        $decimals = 2;
                        if ($baseCurrency->code_en == 'BHD' || $baseCurrency->code_en == 'KWD') {
                            $decimals = 3;
                        }

                        if (isset($cartDetails['items'])) {
                            foreach ($cartDetails['items'] as $item) {
                                $subTotal += ($item['final_price'] * $item['quantity']);
                            }
                        }
                        $promotion = \app\models\Promotions::findOne($model->promotion_id);
                        $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                        $discount = 0;
                        $discountPrice = 0;
                        if (isset($model->discount) && !empty($model->discount)) {
                            $discount = $model->discount;
                            $discountPrice = ($subTotal * $model->discount) / 100;
                        }
                        if (isset($discount) && !empty($discount) && isset($minimumOrder)) {
                            $minimumOrderConverted = $this->convertPrice($minimumOrder, 82, $store['currency_id']);
                            if ($subTotal >= $minimumOrderConverted) {
                                $discountPrice = ($subTotal * $model->discount) / 100;
                            } else {
                                $discountPrice = 0;
                            }
                        }
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $vatCharges;
                        $orderStatusModel = new \app\models\OrderStatus();
                        $orderStatusModel->order_id = $model->order_id;
                        $orderStatusModel->status_id = 6;
                        $orderStatusModel->status_date = date('Y-m-d H:i:s');
                        $orderStatusModel->user_type = 'U';
                        $orderStatusModel->user_id = $model->user_id;
                        $orderStatusModel->comment = 'Payment failure';
                        $orderStatusModel->save(false);
                        //

                        try {
                            Yii::$app->mailer->compose('@app/mail/checkout', [
                                "payment" => $paymentModel,
                                "cartDetails" => $cartDetails,
                                'baseCurrencyName' => $baseCurrency->code_en,
                                'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrency->code_en),
                                'total' => (string) AppHelper::formatPrice($total, $baseCurrency->code_en),
                                'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrency->code_en),
                                'cod_cost' => (string) $cod_cost,
                                'name' => $model->user->first_name . " " . $model->user->last_name,
                                'user' => $model->user,
                                'order_number' => $model->order_number,
                                'order' => $model,
                                'payment_mode' => $model->payment_mode,
                                'order_date' => $model->create_date,
                                'vat_pct' => (string) $vatPct,
                                'vat_charges' => (string) AppHelper::formatPrice($vatCharges, $baseCurrency->code_en),
                                'discount_price' => $discountPrice,
                            ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['appName']])
                                ->setTo($model->user->email)
                                ->setSubject("Error while purchasing from 3eyadat")
                                ->send();
                        } catch (\Exception $e) {
                        }
                    }

                    $result_url = (!empty($redirect_url)) ? $redirect_url : Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                    $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" . $udf2 . "&UDF3=" . $udf3 . "&UDF4=" . $udf4 . "&UDF5=" . $udf4 . "&type=$payment_type&type_id=$payment_type_id";
                    return $this->redirect($result_url . $result_params);
                }
                return $this->redirect($result_url . $result_params);
            } else {
                $result_url = Yii::$app->urlManager->createAbsoluteUrl('site/failed');
                $result_params = "?PaymentID={$_REQUEST['paymentId']}&Result=&PostDate=&TranID=&Auth=&Ref=&TrackID=&UDF1=&UDF2=&UDF3=&UDF4=&UDF5=&type=&type_id=";
                return $this->redirect($result_url . $result_params);
            }
        }
    }
}
