<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\AccessRule;
use app\components\UserIdentity;
use app\models\Orders;
use app\models\OrdersSearch;
use app\models\DriverOrders;
use app\models\Drivers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\helpers\AppHelper;
use kartik\mpdf\Pdf;

class OrderController extends Controller
{

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
                'only' => ['index', 'view', 'update-status', 'add-status', 'update-shipping-info'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update-status', 'add-status', 'update-shipping-info'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ], [
                        'actions' => ['index', 'view', 'update-status', 'add-status', 'update-shipping-info'],
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id)
    {
        $html = $this->renderPartial('print-view', [
            'model' => $this->findModel($id),
        ]);
        $pdf = new Pdf([
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'content' => $html,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                .kv-panel-before, .kv-panel-after, .panel-footer {display: none;}
                #order-status .kv-panel-before, #order-status-boutique .kv-panel-before { display: block;}
                #order-status .pull-right, #order-status-boutique .pull-right { float: none !important;}
                .panel-primary {
                    border-color: #d4d4d4;
                }
                .panel-primary > .panel-heading {
                    color: #333;
                    background-color: #d4d4d4;
                    border-color: #d4d4d4;
                }
                td,th{
                    text-align: left;
                }
                
                table{
                    width: 100%;
                    /*font-family: "courier";*/
                    
                    page-break-inside:auto;
                }
            ',
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                //'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionContacted($id)
    {
        $model = $this->findModel($id);
        if ($model->is_contacted == 0) {
            $model->is_contacted = 1;
        } else {
            $model->is_contacted = 0;
        }
        if ($model->save(false)) {
            return '1';
        } else {
            return json_encode($model->errors);
        }
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangeBulkStatus()
    {
        $request = Yii::$app->request->bodyParams;
        if (empty($request)) {
            return json_encode(['status' => 200, 'msg' => 'Error processing the request!!!']);
        }
        $orders = explode(',', $request['order_id']);
        $models = Orders::find()
            ->where(['order_id' => $orders])
            ->all();
        if (!empty($models)) {
            $error = false;
            $errorMessage = '';
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($models as $model) {
                    $check = \app\models\OrderStatus::find()
                        ->where(['order_id' => $model->order_id])
                        ->orderBy(['order_status_id' => SORT_DESC])
                        ->one();
                    if (!empty($check) && $check->status_id == $request['status']) {
                        $errorMessage = 'The order is already in "' . strtoupper($check->status->name_en) . '".';
                        $error = true;
                        break;
                    }
                    $status = new \app\models\OrderStatus();
                    $status->order_id = (int) $model->order_id;
                    $status->status_id = $request['status'];
                    $status->user_type = 'A';
                    $status->user_id = Yii::$app->user->identity->admin_id;
                    $status->comment = $request['comment'];
                    $status->notify_customer = (isset($request['notify']) && !empty($request['notify'])) ? $request['notify'] : 0;
                    $status->status_date = date('Y-m-d H:i:s');
                    if ($status->save()) {
                        if ($status->status_id == 6) {
                            foreach ($model->pharmacyOrders as $pharmacyOrder) {
                                foreach ($pharmacyOrder->orderItems as $item) {
                                    $product = \app\models\Product::findOne($item->product_id);
                                    $product->updateCounters(['remaining_quantity' => $item->quantity]);
                                    AppHelper::adjustStock($product->product_id, $item->quantity, "Adding back the stock after cancelling the order #{$model->order_number}. Remaining quantity is {$product->remaining_quantity}.");
                                }
                            }
                        }

                        if (isset($request['notify']) && $request['notify'] != 0) {
                            $userModel = $model->user;

                            if ($model->user_id == null) {
                                $email = $model->shipping_email;
                                $name = $model->recipient_name;
                            } else {
                                $email = $userModel->email;
                                $name = $userModel->first_name . ' ' . $userModel->last_name;
                            }
                            //Sending Push Notification
                            $msg = empty($request['comment']) ? 'Status for your order #' . $model->order_number . ' has been changed to ' . $status->status->name_en : $request['comment'];
                            if (!empty($userModel->device_token) && $userModel->push_enabled == '1') {
                                $target_id = (string) $model->order_id;
                                \app\helpers\AppHelper::sendPushwoosh($msg, [$userModel->device_token], "O", $target_id);
                            }
                            $subject = 'Status of order #' . $model->order_number . ' has been changed to ' . $status->status->name_en;
                            $store = \app\models\Stores::findOne($model->store_id);
                            Yii::$app->mailer->compose('@app/mail/order-status-change', [
                                'supportEmail' => Yii::$app->params['supportEmail'],
                                'model' => $model,
                                'status' => $status->status->name_en,
                                'name' => $name,
                                'order_number' => $model->order_number,
                                'store' => $store,
                                'comment' => $status->comment,
                                'notify_customer' => $status->notify_customer,
                                'statusModel' => $status,
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($email)
                                ->setSubject($subject)
                                ->send();
                            //get all admin emails
                            $adminEmails = [];
                            $admins = \app\models\Admin::find()
                                ->where(['is_active' => 1, 'is_deleted' => 0])
                                ->all();
                            foreach ($admins as $adm) {
                                $adminEmails[] = $adm->email;
                            }
                            //send email to all admins
                            Yii::$app->mailer->compose('@app/mail/admin-order-status-change', [
                                'supportEmail' => Yii::$app->params['supportEmail'],
                                'model' => $model,
                                'status' => $status->status->name_en,
                                'name' => $model->user->first_name . ' ' . $model->user->last_name,
                                'order_number' => $model->order_number,
                            ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($adminEmails)
                                ->setSubject("Eyadat order status changed")
                                ->send();
                        }
                    } else {
                        $error = true;
                        break;
                    }
                }
            } catch (\Exception $e) {
                $error = true;
                $transaction->rollBack();
                $errorMessage = $e->getMessage() . ' Line Number: ' . $e->getLine();
            }
            if ($error == false) {
                $transaction->commit();
                return json_encode(['status' => 200, 'msg' => 'Order status successfully updated.']);
            } else {
                $transaction->rollBack();
                return json_encode(['status' => 500, 'msg' => $errorMessage]);
            }
        }
    }

    public function actionExport()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->export(Yii::$app->request->queryParams);
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("3eyadat")
            ->setTitle('Sheet1')
            ->setKeywords("phpExcel");
        $objPHPExcel->setActiveSheetIndex(0);
        //excel columns
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 4) {
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order number');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Purchased On');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Quantity');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Order Price');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Admin Commission');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Shop Earning');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Paymode');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Status');
            $n = 2;
            foreach ($dataProvider as $model) {
                $date = date("m/d/Y", strtotime($model->create_date));
                $addressStr = '';
                if ($model->user_id == null) {
                    $username = $model->recipient_name;
                } else {
                    $username = $model->user->first_name . ' ' . $model->user->last_name;
                }
                $qty = 0;
                if (\Yii::$app->session['_eyadatAuth'] == 2) {
                    $pharmacyOrders = $model->getPharmacyOrders()->where(['pharmacy_id' => Yii::$app->user->identity->pharmacy_id])->all();
                    foreach ($pharmacyOrders as $pharmacyOrder) {
                        foreach ($pharmacyOrder->orderItems as $bo) {
                            $qty += $bo->quantity;
                        }
                    }
                } else {
                    foreach ($model->pharmacyOrders as $pharmacyOrder) {
                        foreach ($pharmacyOrder->orderItems as $bo) {
                            $qty += $bo->quantity;
                        }
                    }
                }
                $amt = $model->currency_code . " " . number_format($model->total_amount, 3);
                $pharmaAmt = $model->currency_code . " " . number_format(($model->order_item_amount - $model->admin_commission), 3);
                $orderStatus = \app\models\OrderStatus::find()
                    ->where(['order_id' => $model->order_id])
                    ->orderBy(['order_status_id' => SORT_DESC])
                    ->one();
                $status = (!empty($orderStatus)) ? $orderStatus->status->name_en : "(not set)";

                $paymode = '';
                if ($model->payment_mode == 'K') {
                    $paymode = 'Knet';
                } elseif ($model->payment_mode == 'CC') {
                    $paymode = 'Visa/MasterCard';
                } elseif ($model->payment_mode == 'C') {
                    $paymode = 'Cash on Delivery';
                }
                $pharmaOrderStaus = \app\models\PharmacyOrderStatus::find()
                    ->join('LEFT JOIN', 'pharmacy_orders', 'pharmacy_order_status.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->where(['pharmacy_orders.order_id' => $model->order_id])
                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                    ->one();
                if (!empty($pharmaOrderStaus)) {
                    $pharmaStatus = $pharmaOrderStaus->pharmacyStatus->name_en;
                } else {
                    $pharmaStatus = "";
                }
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->order_number);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $n, $date);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $n, $qty);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $n, $amt);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $n, ($model->currency_code . ' ' . $model->admin_commission));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $n, $pharmaAmt);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $n, $paymode);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $n, $pharmaStatus);
                $n++;
            }
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order number');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Purchased On');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Username');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Shipping Address');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Quantity');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Order Price');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Admin Commission');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Pharmacy Earning');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Paymode');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Pharmacy');
            $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Status');
            $n = 2;
            foreach ($dataProvider as $model) {
                $date = date("m/d/Y", strtotime($model->create_date));
                $addressStr = '';
                if ($model->user_id == null) {
                    $username = $model->recipient_name;
                    $areaModel = \app\models\Area::findOne($model->shipping_area_id);
                    $blockModel = \app\models\Block::findOne($model->shipping_block_id);
                    $addressStr .= $areaModel->state->name_en . ','
                        . $areaModel->name_en . ','
                        . $blockModel->name_en . ','
                        . $model->shipping_street . ','
                        . $model->shipping_addressline_1;
                } else {
                    $username = $model->user->first_name . ' ' . $model->user->last_name;
                    $shipping = $model->shippingAddress;
                    if (!empty($shipping)) {
                        if (!empty($shipping->street)) {
                            $addressStr .= 'Street: ' . $shipping->street . ", ";
                        }

                        if (!empty($shipping->addressline_1)) {
                            $addressStr .= 'Address Line: ' . $shipping->addressline_1 . "<br>";
                        }

                        if (!empty($shipping->block)) {
                            $addressStr .= '' . $shipping->block->name_en . ", ";
                        }

                        if (!empty($shipping->area)) {
                            $addressStr .= '' . $shipping->area->name_en . ", ";
                        }

                        if (!empty($shipping->state)) {
                            $addressStr .= '' . $shipping->state->name_en;
                        }
                    }
                }
                $qty = 0;
                if (\Yii::$app->session['_eyadatAuth'] == 2) {
                    $pharmacyOrders = $model->getPharmacyOrders()->where(['pharmacy_id' => Yii::$app->user->identity->pharmacy_id])->all();
                    foreach ($pharmacyOrders as $pharmacyOrder) {
                        foreach ($pharmacyOrder->orderItems as $bo) {
                            $qty += $bo->quantity;
                        }
                    }
                } else {
                    foreach ($model->pharmacyOrders as $pharmacyOrder) {
                        foreach ($pharmacyOrder->orderItems as $bo) {
                            $qty += $bo->quantity;
                        }
                    }
                }
                $amt = $model->currency_code . " " . number_format($model->total_amount, 3);
                $pharmaAmt = $model->currency_code . " " . number_format(($model->order_item_amount - $model->admin_commission), 3);
                $orderStatus = \app\models\OrderStatus::find()
                    ->where(['order_id' => $model->order_id])
                    ->orderBy(['order_status_id' => SORT_DESC])
                    ->one();
                $status = (!empty($orderStatus)) ? $orderStatus->status->name_en : "(not set)";
                $paymode = '';
                if ($model->payment_mode == 'K') {
                    $paymode = 'Knet';
                } elseif ($model->payment_mode == 'CC') {
                    $paymode = 'Visa/MasterCard';
                } elseif ($model->payment_mode == 'C') {
                    $paymode = 'Cash on Delivery';
                }
                $pharmastr = [];
                foreach ($model->pharmacyOrders as $pharmacyOrder) {
                    $pharmastr[] = $pharmacyOrder->pharmacy->name_en;
                }

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->order_number);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $n, $date);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $n, $username);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $n, $addressStr);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $n, $qty);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $n, $amt);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $n, ($model->currency_code . ' ' . $model->admin_commission));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $n, $pharmaAmt);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $n, $paymode);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $n, implode(',', $pharmastr));
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $n, $status);
                $n++;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders-' . date('YmdHis') . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xls");
        $objWriter->save('php://output');
        exit;
    }

    public function actionAddStatus()
    {
        $request = Yii::$app->request->bodyParams;
        $model = Orders::find()
            ->where(['order_id' => $request['order_id']])
            ->one();
        if (!empty($model)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $check = \app\models\OrderStatus::find()
                    ->where(['order_id' => $model->order_id])
                    ->orderBy(['order_status_id' => SORT_DESC])
                    ->one();
                if (!empty($check) && $check->status_id == $request['status']) {
                    return json_encode(['status' => 201, 'msg' => 'The order is already in "' . strtoupper($check->status->name_en) . '".']);
                }

                $status = new \app\models\OrderStatus();
                $status->order_id = $request['order_id'];
                $status->status_id = $request['status'];
                $status->user_type = 'A';
                $status->user_id = Yii::$app->user->identity->admin_id;
                $status->comment = $request['comment'];
                $status->notify_customer = (isset($request['notify']) && !empty($request['notify'])) ? $request['notify'] : 0;
                $status->status_date = date('Y-m-d H:i:s');
                $store = \app\models\Stores::findOne($model->store_id);
                if ($status->save()) {
                    if ($status->status_id == 6) { // If status is cancelled
                        foreach ($model->pharmacyOrders as $pharmacyOrder) {
                            foreach ($pharmacyOrder->orderItems as $item) {
                                $product = \app\models\Product::findOne($item->product_id);
                                $product->updateCounters(['remaining_quantity' => $item->quantity]);
                                AppHelper::adjustStock($product->product_id, $item->quantity, "Adding back the stock after cancelling the order #{$model->order_number} from control panel. Remaining quantity is {$product->remaining_quantity}.");
                            }
                        }
                    }
                    if (isset($request['notify']) && $request['notify'] != 0) {
                        $userModel = $model->user;
                        if ($model->user_id == null) {
                            $email = $model->shipping_email;
                            $name = $model->recipient_name;
                        } else {
                            $email = $userModel->email;
                            $name = $userModel->first_name . ' ' . $userModel->last_name;
                        }
                        //Sending Push Notification
                        $msg = empty($request['comment']) ? 'Status for your order #' . $model->order_number . ' has been changed to ' . $status->status->name_en : $request['comment'];
                        if (!empty($userModel->device_token) && $userModel->push_enabled == '1') {
                            $target_id = (string) $model->order_id;
                            $user_device_token = $userModel->device_token;
                            $full_name =  $model->user->first_name;
                            $title  = "Order Status";
                            if (!empty($user_device_token)) {
                                $this->sendpush($target_id, $msg, $title, $user_device_token, "O");
                            }

                            // _    \app\helpers\AppHelper::sendPushwoosh($msg, [$userModel->device_token], "O", $targetid);
                        }
                        $subject = 'Status of order #' . $model->order_number . ' has been changed to ' . $status->status->name_en;
                        Yii::$app->mailer->compose('@app/mail/order-status-change', [
                            'supportEmail' => Yii::$app->params['supportEmail'],
                            'model' => $model,
                            'status' => $status->status->name_en,
                            'name' => $name,
                            'order_number' => $model->order_number,
                            'store' => $store,
                            'comment' => $status->comment,
                            'notify_customer' => $status->notify_customer,
                            'statusModel' => $status,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($email)
                            ->setSubject($subject)
                            ->send();
                        //get all admin emails 
                        $adminEmails = [];
                        $admins = \app\models\Admin::find()
                            ->where(['is_active' => 1, 'is_deleted' => 0])
                            ->all();
                        foreach ($admins as $adm) {
                            $adminEmails[] = $adm->email;
                        }
                        //send email to all admins
                        Yii::$app->mailer->compose('@app/mail/admin-order-status-change', [
                            'supportEmail' => Yii::$app->params['supportEmail'],
                            'model' => $model,
                            'status' => $status->status->name_en,
                            'name' => $model->user->first_name . ' ' . $model->user->last_name,
                            'order_number' => $model->order_number,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($adminEmails)
                            ->setSubject("Eyadat order status change")
                            ->send();
                    }





                    return json_encode(['status' => 200, 'msg' => 'Order status successfully updated.']);
                } else {
                    return json_encode($status->errors);
                }
            } else if (\Yii::$app->session['_eyadatAuth'] == 5) {
                $pharmaOrder = \app\models\PharmacyOrders::find()
                    ->where(['order_id' => $model->order_id, 'pharmacy_id' => Yii::$app->user->identity->pharmacy_id])
                    ->one();
                $check = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => $request['status'], 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if (!empty($check)) {
                    return json_encode(['status' => 201, 'msg' => 'Order status already been taken.Admin will review it']);
                }
                //check already delivered or not accepted
                $checkDeliveredAlready = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => [3, 4], 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if (!empty($checkDeliveredAlready)) {
                    return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
                }
                //check already accepted once
                $checkAcceptedAlready = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => 1, 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if ($request['status'] == 3 && !empty($checkAcceptedAlready)) {
                    return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
                }
                //insert new shop status
                $pharmaStatus = new \app\models\PharmacyOrderStatus();
                $pharmaStatus->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                $pharmaStatus->pharmacy_status_id = $request['status'];
                $pharmaStatus->user_type = 'S';
                $pharmaStatus->user_id = Yii::$app->user->identity->pharmacy_id;
                $pharmaStatus->status_date = date('Y-m-d H:i:s');
                $pharmaStatus->comment = $request['comment'];
                if ($pharmaStatus->save()) {
                    //get all admin emails 
                    $adminEmails = [];
                    $admins = \app\models\Admin::find()
                        ->where(['is_active' => 1, 'is_deleted' => 0])
                        ->all();
                    foreach ($admins as $adm) {
                        $adminEmails[] = $adm->email;
                    }
                    if ($adminEmails != "") {
                        //send email to all admins
                        Yii::$app->mailer->compose('@app/mail/shop-order-status-change', [
                            'supportEmail' => Yii::$app->params['supportEmail'],
                            'model' => $model,
                            'status' => $pharmaStatus->pharmacyStatus->name_en,
                            'name' => $model->user->first_name . ' ' . $model->user->last_name,
                            'order_number' => $pharmaOrder->order_number,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($adminEmails)
                            ->setSubject("Eyadat order status changed")
                            ->send();
                    }
                    return json_encode(['status' => 200, 'msg' => 'Order status has been saved successfully.Admin will review it.']);
                }
            }
        }
    }



    public function sendpush($id, $msg, $title = "", $user_device_token = "", $target = "O")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Orders does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);

                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $model->user->user_id;
                $notification->target   = $target;
                $notification->target_id = $model->order_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, $target, $model->order_id, $title, '');

                /*return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);*/
            }
        }
    }


    public function actionAddPharmacyStatus()
    {
        $request = Yii::$app->request->bodyParams;
        $model = PharmacyOrders::find()
            ->where(['pharmacy_order_id' => $request['order_id']])
            ->one();
        if (!empty($model)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $pharmaOrder = \app\models\PharmacyOrders::find()
                    ->where(['order_id' => $model->order_id, 'pharmacy_id' => Yii::$app->user->identity->pharmacy_id])
                    ->one();
                $check = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => $request['status'], 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if (!empty($check)) {
                    return json_encode(['status' => 201, 'msg' => 'Order status already been taken.Admin will review it']);
                }
                //check already delivered or not accepted
                $checkDeliveredAlready = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => [3, 4], 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if (!empty($checkDeliveredAlready)) {
                    return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
                }
                //check already accepted once
                $checkAcceptedAlready = \app\models\PharmacyOrderStatus::find()
                    ->where(['pharmacy_status_id' => 1, 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
                    ->one();
                if ($request['status'] == 3 && !empty($checkAcceptedAlready)) {
                    return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
                }
                //insert new shop status
                $pharmaStatus = new \app\models\PharmacyOrderStatus();
                $pharmaStatus->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                $pharmaStatus->pharmacy_status_id = $request['status'];
                $pharmaStatus->user_type = 'S';
                $pharmaStatus->user_id = Yii::$app->user->identity->pharmacy_id;
                $pharmaStatus->status_date = date('Y-m-d H:i:s');
                $pharmaStatus->comment = $request['comment'];
                if ($pharmaStatus->save()) {
                    //get all admin emails 
                    $adminEmails = [];
                    $admins = \app\models\Admin::find()
                        ->where(['is_active' => 1, 'is_deleted' => 0])
                        ->all();
                    foreach ($admins as $adm) {
                        $adminEmails[] = $adm->email;
                    }
                    if ($adminEmails != "") {
                        //send email to all admins
                        Yii::$app->mailer->compose('@app/mail/shop-order-status-change', [
                            'supportEmail' => Yii::$app->params['supportEmail'],
                            'model' => $model,
                            'status' => $pharmaStatus->pharmacyStatus->name_en,
                            'name' => $model->user->first_name . ' ' . $model->user->last_name,
                            'order_number' => $pharmaOrder->order_number,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($adminEmails)
                            ->setSubject("Eyadat order status changed")
                            ->send();
                    }
                    return json_encode(['status' => 200, 'msg' => 'Order status has been saved successfully.Admin will review it.']);
                }
            }
        }
    }

    public function actionAddShopOrderStatus($status_id, $pharmacy_order_id)
    {
        $pharmaOrder = \app\models\PharmacyOrders::find()
            ->where(['pharmacy_order_id' => $pharmacy_order_id])
            ->one();
        //order model
        $model = Orders::find()
            ->where(['order_id' => $pharmaOrder->order_id])
            ->one();
        $check = \app\models\PharmacyOrderStatus::find()
            ->where(['pharmacy_status_id' => $status_id, 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
            ->one();
        if (!empty($check)) {
            return json_encode(['status' => 201, 'msg' => 'Order status already been taken']);
        }
        //check already delivered or not accepted
        $checkDeliveredAlready = \app\models\PharmacyOrderStatus::find()
            ->where(['pharmacy_status_id' => [3, 4], 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
            ->one();
        if (!empty($checkDeliveredAlready)) {
            return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
        }
        //check already accepted once
        $checkAcceptedAlready = \app\models\PharmacyOrderStatus::find()
            ->where(['pharmacy_status_id' => 1, 'pharmacy_order_id' => $pharmaOrder->pharmacy_order_id])
            ->one();
        if ($status_id == 3 && !empty($checkAcceptedAlready)) {
            return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
        }

        if ($status_id == 4 && \Yii::$app->session['_eyadatAuth'] == 5) {
            return json_encode(['status' => 201, 'msg' => "You're not allowed to do this"]);
        }
        //insert new shop status
        $pharmaStatus = new \app\models\PharmacyOrderStatus();
        $pharmaStatus->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
        $pharmaStatus->pharmacy_status_id = $status_id;
        $pharmaStatus->user_type = (\Yii::$app->session['_eyadatAuth'] == 5) ? 'S' : 'A';
        $pharmaStatus->user_id = (\Yii::$app->session['_eyadatAuth'] == 5) ? Yii::$app->user->identity->pharmacy_id : Yii::$app->user->identity->admin_id;
        $pharmaStatus->status_date = date('Y-m-d H:i:s');
        $pharmaStatus->comment = "Status added by admin";
        /*$total_shop_orders = \app\models\PharmacyOrders::find()
                    ->where(['order_id' => $pharmaOrder->order_id])
                    ->all();
        echo count($total_shop_orders).'<Br>';
        $check_all_shop_accepted = \app\models\PharmacyOrderStatus::find()
                    ->where(['order_id' => $pharmaOrder->order_id])
                    ->all();
        echo count($check_all_shop_accepted);die;*/

        if ($pharmaStatus->save(false)) {
            //get all admin emails 
            $adminEmails = [];
            $admins = \app\models\Admin::find()
                ->where(['is_active' => 1, 'is_deleted' => 0])
                ->all();
            foreach ($admins as $adm) {
                $adminEmails[] = $adm->email;
            }
            //send email to all admins
            if ($adminEmails != "") {
                Yii::$app->mailer->compose('@app/mail/shop-order-status-change', [
                    'supportEmail' => Yii::$app->params['supportEmail'],
                    'model' => $model,
                    'status' => $pharmaStatus->pharmacyStatus->name_en,
                    'name' => $model->user->first_name . ' ' . $model->user->last_name,
                    'order_number' => $pharmaOrder->order_number,
                ])
                    ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                    ->setTo($adminEmails)
                    ->setSubject("Eyadat shop order status changed")
                    ->send();
            }
            //send email to shop
            foreach ($model->pharmacyOrders as $so) {
                if ($so->pharmacy->email != "") {
                    Yii::$app->mailer->compose('@app/mail/shop-order-status-change', [
                        'supportEmail' => Yii::$app->params['supportEmail'],
                        'model' => $model,
                        'status' => $pharmaStatus->pharmacyStatus->name_en,
                        'name' => $so->pharmacy->name_en,
                        'order_number' => $so->order_number,
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($so->pharmacy->email)
                        ->setSubject("Eyadat shop order status changed")
                        ->send();
                }
            }
            $order_id = $pharmaOrder->order_id;
            if ($status_id == 1) {
                $status = new \app\models\OrderStatus();
                $status->order_id = $order_id;
                $status->status_id = 3;
                $status->user_type = 'A';
                $status->user_id = Yii::$app->user->identity->admin_id;
                $status->comment = 'Automatically changed status';
                $status->notify_customer =  0;
                $status->status_date = date('Y-m-d H:i:s');
                //debugPrint($status);die;
                $status->save();
            }

            if ($status_id == 2) {
                $status = new \app\models\OrderStatus();
                $status->order_id = $order_id;
                $status->status_id = 8;
                $status->user_type = 'A';
                $status->user_id = Yii::$app->user->identity->admin_id;
                $status->comment = 'Automatically changed status';
                $status->notify_customer =  0;
                $status->status_date = date('Y-m-d H:i:s');
                //debugPrint($status);die;
                $status->save();
            }

            return json_encode(['status' => 200, 'msg' => 'Order status has been saved successfully.']);
        } else {
            return json_encode(['status' => 201, 'msg' => $pharmaStatus->errors, 'model' => $pharmaOrder]);
        }
    }

    public function actionAssignOrder()
    {
        $request = Yii::$app->request->bodyParams;
        //echo "<pre>";print_r($request);die;

        $model = Orders::find()
            ->where(['order_id' => $request['order_id']])
            ->one();

        if (!empty($model)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $check = \app\models\OrderStatus::find()
                    ->where(['order_id' => $model->order_id])
                    ->orderBy(['order_status_id' => SORT_DESC])
                    ->one();
                if (!empty($check) && $check->status_id == 7) {
                    $driverOrderModel = new DriverOrders();
                    $driverOrderModel->type = 'O';
                    $driverOrderModel->type_id = $request['order_id'];
                    $driverOrderModel->driver_id = $request['driver_id'];
                    $driverOrderModel->assigned_date = date("Y-m-d H:i:s");
                    $driverOrderModel->save();
                    $driver_id = $request['driver_id'];
                    $driverModel = \app\models\Drivers::find()
                        ->where(['driver_id' => $driver_id])
                        ->one();
                    $device_token = (!empty($driverModel)) ? $driverModel->device_token : '';
                    $title = "New Order assigned!";
                    $msg = "You have a new delivery order assigned to you. ";
                    if ($device_token != '') {
                        \app\helpers\AppHelper::sendPushwoosh($msg, $device_token, "D", '', $title, '', '', '');
                        //sendDriverPushwoosh($msg, [$device_token], "D", '',$title, '', '', ''); 

                    }
                    return json_encode(['status' => 200, 'msg' => 'Order successfully assigned to the driver.']);
                } else {
                    return json_encode(['status' => 500, 'msg' => 'Order is not in "Ready for pickup status".']);
                }
            }
        }
    }

    public function actionUnassignOrder($id, $driver_order_id = "")
    {
        //return $shop_order_id;die;
        $request = Yii::$app->request->bodyParams;

        $model = Orders::find()
            ->where(['order_id' => $id])
            ->one();

        if (!empty($model)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                if ($driver_order_id == "undefined") {
                    Yii::$app->db->createCommand("DELETE FROM driver_orders WHERE type_id = $id AND driver_order_id = $driver_order_id  AND type = 'O'")
                        ->execute();
                } else {
                    Yii::$app->db->createCommand("DELETE FROM driver_orders WHERE driver_order_id = $driver_order_id AND type_id = $id  AND type = 'O'")
                        ->execute();
                }
                return json_encode(['status' => 200, 'msg' => 'Order successfully unassigned from the driver.']);
            }
        }
    }

    public function actionAssignPickupOrder()
    {

        $request    = Yii::$app->request->bodyParams;
        $order_id   = $request['order_id'];
        $pharmacy_order_id = $request['pharmacy_order_id'];
        $driver_id  = $request['driver_id'];
        $pharmacy_id    = $request['pharmacy_id'];

        $model = Orders::find()
            ->where(['order_id' => $request['order_id']])
            ->one();

        if (!empty($model)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1 && $driver_id != 'undefined') {
                $check = \app\models\DriverSuborders::find()
                    ->where(['order_id' => $model->order_id, 'pharmacy_order_id' => $pharmacy_order_id])
                    ->one();

                $driver_id = $request['driver_id'];
                $driverModel = \app\models\Drivers::find()
                    ->where(['driver_id' => $driver_id])
                    ->one();

                $device_token = (!empty($driverModel)) ? $driverModel->device_token : '';

                $title = "New Pickup Order assigned!";
                $msg = "You have a new Pickup order assigned to you. ";
                if ($device_token != '') {
                    \app\helpers\AppHelper::sendPushwoosh($msg, $device_token, "D", '', $title, '', '', '');
                }

                if (empty($check) && $driver_id != null) {
                    $driverOrderModel = new \app\models\DriverSuborders();
                    $driverOrderModel->order_id = $request['order_id'];
                    $driverOrderModel->driver_id = $request['driver_id'];
                    $driverOrderModel->pharmacy_order_id = $request['pharmacy_order_id'];
                    $driverOrderModel->pharmacy_id = $request['pharmacy_id'];
                    $driverOrderModel->assigned_date = date("Y-m-d H:i:s");
                    $driverOrderModel->save(false);
                    return json_encode(['status' => 200, 'msg' => 'Pharmacy Order successfully assigned to the driver.']);
                } else {
                    $check->driver_id = $request['driver_id'];
                    $check->assigned_date = date("Y-m-d H:i:s");
                    $check->save(false);
                    return json_encode(['status' => 200, 'msg' => 'Pharmacy Order successfully assigned to the driver.']);
                }
            }
        }
    }

    public function actionBulkPharmacyDriverAssign()
    {
        if (\Yii::$app->session['_eyadatAuth'] == 1) {
            $request = Yii::$app->request->bodyParams;
            //echo "<pre>";print_r($request);die;
            if (empty($request)) {
                return json_encode(['status' => 200, 'msg' => 'Error processing the request!!!']);
            }
            $orders = explode(',', $request['order_id']);
            $driver_id = $request['driver_id'];
            $driver_model = Drivers::find()->select(['driver_id'])->where(['driver_id' => $driver_id])->one();
            $i = 0;

            if (!empty($driver_model)) {
                foreach ($orders as $row) {
                    $pharmacy_order = \app\models\PharmacyOrders::findOne($row);
                    $order_id = $pharmacy_order->order_id;
                    $pharmacy_id = $pharmacy_order->pharmacy_id;
                    $check = \app\models\DriverSuborders::find()
                        ->where(['order_id' => $order_id, 'pharmacy_order_id' => $row])
                        ->one();
                    if (empty($check) && $driver_id != null) {
                        $driverOrderModel = new \app\models\DriverSuborders();
                        $driverOrderModel->order_id = $order_id;
                        $driverOrderModel->driver_id = $driver_id;
                        $driverOrderModel->pharmacy_order_id = $row;
                        $driverOrderModel->pharmacy_id = $pharmacy_id;
                        $driverOrderModel->assigned_date = date("Y-m-d H:i:s");
                        $driverOrderModel->save(false);
                    } else {
                        $check->driver_id = $driver_id;
                        $check->assigned_date = date("Y-m-d H:i:s");
                        $check->save(false);
                        //return json_encode(['status' => 200, 'msg' => 'Shop Order successfully assigned to the driver.']);
                    }
                }
                return json_encode(['status' => 200, 'msg' => 'Order successfully assigned to the driver.']);
            } else {
                return json_encode(['status' => 500, 'msg' => 'Order not Found']);
            }
        }
    }
}
