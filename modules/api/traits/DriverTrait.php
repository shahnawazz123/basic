<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\traits;

use stdClass;
use Yii;
use app\helpers\AppHelper;
/**
 *
 * @author vasim
 */
trait DriverTrait
{
    public function actionDriverLogin() {
        $request = Yii::$app->request->bodyParams;
        $this->data = new \stdClass();
        if (!empty($request)) 
        {
            $email = '';
            $phone = '';

            if (filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $email = $request['email'];
            } else {
                $pattern = '/^[+]+[0-9]*$/';

                if (preg_match($pattern, $request['email'])) {
                    $phone = $request['email'];
                }

                $phoneCode = (isset($request['phone_code'])) ? $request['phone_code'] : "";
                $phone = (substr($request['email'], 0, 1) == "+" || substr($request['email'], 0, 2) == "00" || strlen($request['email']) > 10) ? $request['email'] : $phoneCode . $request['email'];
            }

            $query = \app\models\Drivers::find()
                    ->where(['is_deleted' => 0, 'is_active' => 1]);

            if (!empty($email)) {
                $query->andFilterWhere(['=', 'email', $email]);
            } else if (!empty($phone)) {
                $query->andFilterWhere(
                        [
                            'AND',
                            [
                                'OR',
                                ['=', 'phone', $phone],
                                ['=', 'SUBSTR(`phone`, 2)', $phone],
                                ['=', 'CONCAT("00", SUBSTR(`phone`, 2))', $phone],
                            ]
                        ]
                );
            } else {
                $this->response_code = 500;
                $this->message = 'Invalid email/phone.';

                return $this->response();
            }

            $model = $query->one();

            if (!empty($model)) {
                $validate = Yii::$app->security->validatePassword($request['password'], $model->password);

                if ($validate) {
                    if (isset($request['device_type']) && $request['device_type'] != "") {
                        $model->device_type = $request['device_type'];
                    }
                    if (isset($request['device_model']) && $request['device_model'] != "") {
                        $model->device_model = $request['device_model'];
                    }
                    if (isset($request['app_version']) && $request['app_version'] != "") {
                        $model->app_version = $request['app_version'];
                    }
                    if (isset($request['os_version']) && $request['os_version'] != "") {
                        $model->os_version = $request['os_version'];
                    }
                    if (isset($request['device_token']) && $request['device_token'] != "") {
                        $model->device_token = $request['device_token'];
                    }
                    $model->save(false);
                    $this->data = [
                        'id' => (string) $model->driver_id,
                        'name_en' => $model->name_en,
                        'name_ar' => $model->name_ar,
                        'email' => $model->email,
                        'phone' => $model->phone,
                        'location' => $model->location,
                        'civil_id_number' => $model->civil_id_number,
                        'license_number' => $model->license_number,
                        'location' => $model->location,
                        'image'    => !empty($model->image) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image,'https') : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png','https')
                    ];
                } else {
                    $this->response_code = 201;
                    $this->message = 'Invalid password';
                }
            } else {
                $this->response_code = 404;
                $this->message = 'Driver does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDriverForgotPassword() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Drivers::find()
                    ->where(['email' => $request['email'], 'is_deleted' => 0])
                    ->one();
            if (!empty($model)) {
                $newPassword = Yii::$app->security->generateRandomString(6);
                $model->password = Yii::$app->security->generatePasswordHash($newPassword);

                if ($model->save()) {
                    Yii::$app->mailer->compose('@app/mail/user-forgot-password', [
                                "name" => $model->name_en,
                                "email" => $model->email,
                                "password" => $newPassword,
                                'supportEmail' => Yii::$app->params['supportEmail'],
                            ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($model->email)
                            ->setSubject("Reset password")
                            ->send();
                    $this->message = 'Password successfully reset.please check your inbox';
                } else {
                    $this->response_code = 500;
                    $this->message = $model->errors;
                }
            } else {
                $this->response_code = 404;
                $this->message = 'Email address does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDriverEditProfile() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Drivers::find()
                    ->where(['driver_id' => $request['driver_id'], 'is_deleted' => 0])
                    ->one();

            if (!empty($model)) {
                if (isset($request['name_en']) && !empty($request['name_en']))
                    $model->name_en = $request['name_en'];

                if (isset($request['name_ar']) && !empty($request['name_ar']))
                    $model->name_ar = $request['name_ar'];

                if (isset($request['phone']) && !empty($request['phone']))
                    $model->phone = $request['phone'];

                if (isset($request['device_token']) && !empty($request['device_token']))
                    $model->device_token = $request['device_token'];

                if (isset($request['device_type']) && !empty($request['device_type']))
                    $model->device_type = $request['device_type'];

                if (isset($request['device_model']) && !empty($request['device_model']))
                    $model->device_model = $request['device_model'];

                if (isset($request['app_version']) && !empty($request['app_version']))
                    $model->app_version = $request['app_version'];

                if (isset($request['os_version']) && !empty($request['os_version']))
                    $model->os_version = $request['os_version'];

                if (isset($request['push_enabled']) && !empty($request['push_enabled']))
                    $model->push_enabled = $request['push_enabled'];

               
                if (isset($request['license_number']) && !empty($request['license_number']))
                    $model->license_number = $request['license_number'];

                if (isset($request['civil_id_number']) && !empty($request['civil_id_number']))
                    $model->civil_id_number = $request['civil_id_number'];



                if (isset($request['image']) && !empty($request['image'])) {
                    $image = base64_decode($request['image']);
                    if ($image) {
                        $img = imagecreatefromstring($image);
                        if ($img !== false) {
                            $imageName = time() . '.png';
                            imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                            imagedestroy($img);
                            $model->image = $imageName;
                        }
                    }
                }
                if (isset($request['location']) && $request['location'] != "") {
                    $model->location = $request['location'];
                }

                if (isset($request['old_password']) && !empty($request['old_password'])){
                    $data_password = $model->password;
                    $old_Password = $request['old_password'];

                    if (Yii::$app->getSecurity()->validatePassword($old_Password, $data_password)) {
                        if (isset($request['new_password']) && !empty($request['new_password'])) {
                            $model->password = Yii::$app->security->generatePasswordHash($request['new_password']);
                        } else {
                            $this->response_code = 201;
                            $this->message = 'New password cannot be blank.';
                            $this->data = new \stdClass();
                            return $this->response();
                        }
                    } else {
                        $this->response_code = 201;
                        $this->message = 'Old password does not match.';
                        $this->data = new \stdClass();
                        return $this->response();
                    }
                }

                if (isset($request['password']) && !empty($request['password']))
                    $model->password = Yii::$app->security->generatePasswordHash($request['password']);


                if ($model->save()) {
                    $this->message = 'Driver details successfully updated.';
                    $this->data = [
                        'id' => (string) $model->driver_id,
                        'name_en' => $model->name_en,
                        'name_ar' => $model->name_ar,
                        'civil_id_number' => (string) $model->civil_id_number,
                        'license_number' => (string) $model->license_number,
                        'email' => $model->email,
                        'image' => ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                        'phone' => ($model->phone != "") ? $model->phone : "",
                        'location' => (string) $model->location,
                        'device_token' => (string) $model->device_token,
                        'device_type' => (string) $model->device_type,
                        'device_model' => (string) $model->device_model,
                        'app_version' => (string) $model->app_version,
                        'os_version' => (string) $model->os_version,
                        'push_enabled' => (string) $model->push_enabled
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 404;
                $this->message = 'Driver not found.';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDriverOrders($driver_id, $lang = "en",$type="delivery") {
        $store = $this->getStoreDetails();
        if($type=="delivery")
        {
            $orders = \app\models\Orders::find()
                    ->join('LEFT JOIN', '(SELECT t1.* FROM order_status AS t1 LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id AND t1.order_status_id < t2.order_status_id WHERE t2.order_id IS NULL) as temp', ' temp.order_id = orders.order_id')
                    ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id= pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                    ->join('LEFT JOIN', 'driver_orders', 'orders.order_id = driver_orders.type_id')
                    ->where(['driver_orders.driver_id' => $driver_id,
                        'is_processed' => [1], 'type' => 'O'])
                    ->andWhere(['IN', 'temp.status_id', [4, 7]])
                    ->orderBy(['status_date' => SORT_DESC])
                    ->all();
        }else if($type=="pickup")
        {
            $orders = \app\models\PharmacyOrderStatus::find()

                    ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_order_status.pharmacy_order_id')
                    ->join('LEFT JOIN', '(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp', ' temp.pharmacy_order_id = order_items.pharmacy_order_id')
                    ->join('LEFT JOIN', 'pharmacy_orders', 'pharmacy_orders.pharmacy_order_id = pharmacy_order_status.pharmacy_order_id')
                    ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'driver_suborders', 'pharmacy_orders.pharmacy_order_id = driver_suborders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'order_status', 'order_status.order_id = orders.order_id')
                    ->join('LEFT JOIN', 'pharmacy_status', 'pharmacy_status.pharmacy_status_id = pharmacy_order_status.pharmacy_status_id')
                    ->where(['driver_suborders.driver_id' => $driver_id])
                    //->andWhere(['pharmacy_order_status.pharmacy_status_id'=>2])
                    ->andWhere(['IN', 'temp.pharmacy_status_id', [2]])
                    ->andWhere(['!=', 'order_status.status_id', 5])
                    ->orderBy(['pharmacy_order_status.status_date' => SORT_DESC])
                    ->groupBy('order_items.pharmacy_order_id')
                    ->all();
        }
        $data = [];
        $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
        if($type=="delivery")
        {
            if(!empty($orders))
            {
                foreach ($orders as $order) 
                {

                    $pharmacy_order_model = \app\models\PharmacyOrders::findOne($order->order_id);
                    $pharmacy_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->pharmacy_id : '';
                    $pharmacy_Model = \app\models\Pharmacies::findOne($pharmacy_id);
                    $pharmacy_name = (!empty($pharmacy_Model)) ? $pharmacy_Model->{'name_'.$lang} : '';


                    $pharmacy_location = \app\models\PharmacyLocations::find()
                        ->where(['pharmacy_id' => $pharmacy_id,'is_deleted'=>'0']);
                    $pharmacy_location_model = $pharmacy_location->all();
                    $pharmacy_location_address = [];
                    if(!empty($pharmacy_location_model))
                    {
                        foreach($pharmacy_location_model as $row)
                        {
                            $country_name = (isset($row->area)) ? $row->area->state->country->name_en : '';
                            $pharmacy_location_address = [
                                'name'=> $row->{'name_'.$lang},
                                'country_name' => $country_name,
                                'governorate' => (isset($row->governorate)) ? $row->governorate->name_en : '',
                                'area' => (isset($row->area)) ? $row->area->name_en : '',
                                'block' => (isset($row->block)) ? $row->block : '',
                                'street' => (isset($row->street)) ?$row->street: '',
                                'building' => (isset($row->building)) ?$row->building: '',
                                'latlon' => (isset($row->latlon)) ?$row->latlon: '',
                            ];
                        }
                    }


                    $deliveryCharges = $this->convertPrice($order->delivery_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $codCost = $this->convertPrice($order->cod_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $discountPrice = $this->convertPrice($order->discount_price, Yii::$app->params['default_currency'], $store['currency_id']);
                    $vatCharges = $this->convertPrice($order->vat_charges, Yii::$app->params['default_currency'], $store['currency_id']);
                    $items = $this->cartDetails($order->order_id, $lang, $store);
                    $shippingAddress = $order->shippingAddress;
                    $subTotal = $total = 0;
                    $subTotalKw = 0;
                    $baseCurrencyName = $baseCurrency->code_en;
                    if (isset($items['items'])) {
                        foreach ($items['items'] as $item) {
                            $subTotal += $item['final_price'] * $item['quantity'];
                            $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                        }
                    }
                    if ($order->payment_mode == 'C') {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
                    } else {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges;
                    }

                    $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
                    $vatCharges = 0;
                    $vatChargesKw = 0;
                    if ($vatPct != 0) {
                        $vatCharges = ($vatPct / 100) * $subTotal;
                        $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                    }
                    $total += $vatCharges;
                    $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                    $decimals = 2;
                    if ($currencyModel->code_en == 'KWD') {
                        $decimals = 3;
                    }

                    $orderStatus = $order->getOrderStatuses()->orderBy(['status_date' => SORT_DESC])->limit(1)->one();

                    $vatCharges = $this->convertPrice($vatChargesKw, Yii::$app->params['default_currency_code'], $store['currency_id']);

        
                    $data[] = [
                        'id' => (string) $order->order_id,
                        'order_number' => (string) $order->order_number,
                        'pharmacy_id' => (string) $pharmacy_id,
                        'pharmacy_name' => (string) $pharmacy_name,
                        'recipient_name' => (string) $order->recipient_name,
                        'recipient_phone' => (string) $order->recipient_phone,
                        'purchase_date' => $order->create_date,
                        'payment_mode' => $order->payment_mode,
                        'sub_total' => (string) $subTotal,
                        'total' => (string) $total,
                        'cod_cost' => (string) $codCost,
                        'delivery_charges' => (string) $deliveryCharges,
                        'vat_charges' => (string) $vatCharges,
                        'discount_price' => (string) $discountPrice,
                        'baseCurrencyName' => $baseCurrencyName,
                        'items' => (isset($items['items'])) ? $items['items'] : [],
                        'status_id' => !empty($orderStatus) ? $orderStatus->status_id : 0,
                        'status_name' => !empty($orderStatus) ? $orderStatus->status->{"name_" . $lang} : "",
                        'status_color' => !empty($orderStatus) ? $orderStatus->status->color : "",
                        'is_return_active' => !empty($orderStatus) ? (($orderStatus->status_id == 5) ? 1 : 0) : 0,
                        'is_cancel_active' => !empty($orderStatus) ? (($orderStatus->status_id < 2) ? 1 : 0) : 0,
                        'shipping_address' => [
                            'first_name' => (!empty($shippingAddress)) ? $shippingAddress->first_name : "",
                            'last_name' => (!empty($shippingAddress)) ? $shippingAddress->last_name : "",
                            'area_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->area) ? $shippingAddress->area->name_en : "") : "",
                            'governorate_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->state) ? $shippingAddress->state->name_en : "") : "",
                            'country_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->name_en : "") : "",
                            'phonecode' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->phonecode : "") : "",
                            'block_name' => (!empty($shippingAddress->block_id)) ? \app\helpers\AppHelper::getBlockNameById($shippingAddress->block_id, $lang) : '',
                            'street' => (!empty($shippingAddress)) ? $shippingAddress->street : "",
                            'flat' => (!empty($shippingAddress)) ? $shippingAddress->flat : "",
                            'floor' => (!empty($shippingAddress)) ? $shippingAddress->floor : "",
                            'building' => (!empty($shippingAddress)) ? $shippingAddress->building : "",
                            'addressline_1' => (!empty($shippingAddress)) ? $shippingAddress->addressline_1 : "",
                            'mobile_number' => (!empty($shippingAddress)) ? $shippingAddress->mobile_number : "",
                            'alt_phone_number' => (!empty($shippingAddress)) ? $shippingAddress->alt_phone_number : "",
                            'location_type' => (!empty($shippingAddress)) ? $shippingAddress->location_type : "",
                            'notes' => (!empty($shippingAddress)) ? $shippingAddress->notes : "",
                        ],
                        'pharmacy_location' => (!empty($pharmacy_location_address)) ? $pharmacy_location_address : new stdClass(),
                        'payment_details' => $this->getOrderPaymentDetails($order->order_id),
                    ];
                }
                $this->data = $data;
                if (!empty($data)) {
                    $this->data = $data;
                } else {
                    $this->response_code = 404;
                    $this->message = 'No orders for this driver.';
                }
            }
        }else if($type=="pickup")
        {
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $pharmacy_order_id = $order['pharmacy_order_id'];

                    $pharmacy_order_model = \app\models\PharmacyOrders::findOne($pharmacy_order_id);
                    $order_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->order_id : '';
                    $order_number = (!empty($pharmacy_order_model))? $pharmacy_order_model->order_number : '';
                    $pharmacy_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->pharmacy_id : '';
                    $pharmacy_Model = \app\models\Pharmacies::findOne($pharmacy_id);
                    $pharmacy_name = (!empty($pharmacy_Model)) ? $pharmacy_Model->{'name_'.$lang} : '';
                    $pharmacy_location = \app\models\PharmacyLocations::find()
                        ->where(['pharmacy_id' => $pharmacy_id,'is_deleted'=>'0']);
                    $pharmacy_location_model = $pharmacy_location->all();
                    $pharmacy_location_address = [];
                    if(!empty($pharmacy_location_model))
                    {
                        foreach($pharmacy_location_model as $row)
                        {
                            $country_name = (isset($row->area)) ? $row->area->state->country->name_en : '';
                            $pharmacy_location_address = [
                                'name'=> $row->{'name_'.$lang},
                                'country_name' => $country_name,
                                'governorate' => (isset($row->governorate)) ? $row->governorate->name_en : '',
                                'area' => (isset($row->area)) ? $row->area->name_en : '',
                                'block' => (isset($row->block)) ? $row->block : '',
                                'street' => (isset($row->street)) ?$row->street: '',
                                'building' => (isset($row->building)) ?$row->building: '',
                                'latlon' => (isset($row->latlon)) ?$row->latlon: '',
                            ];
                        }
                    }
                    

                    $order_model = \app\models\Orders::findOne($order_id);  
                    $purchase_date = (!empty($order_model)) ? $order_model->create_date : '';
                    $deliveryCharges = $this->convertPrice($order_model->delivery_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $codCost = $this->convertPrice($order_model->cod_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $discountPrice = $this->convertPrice($order_model->discount_price, Yii::$app->params['default_currency'], $store['currency_id']);
                    $vatCharges = $this->convertPrice($order_model->vat_charges, Yii::$app->params['default_currency'], $store['currency_id']);


                    $sql1 = \app\models\OrderItems::find()
                        ->join('LEFT JOIN', 'product', 'order_items.product_id = product.product_id')
                        ->where(['pharmacy_order_id'=>$pharmacy_order_id])
                        ->orderBy(['order_item_id' => SORT_ASC]);
                    $order_items = $sql1->all();
                    $total_quantity = 0;
                    $total_bill = 0;
                    $product_arr = [];
                    if (!empty($order_items)) {
                        foreach ($order_items as $r) {
                            $total_quantity += $r['quantity'];
                            $total_bill += ($r['price'] * $r['quantity']);
                            array_push($product_arr, $r['product_id']);
                        }
                    }
                    $items = $this->cartDetails($order_id, $lang, $store);
                    $item_list = [];
                    $p = 0;
                    if (!empty($items)) {
                        foreach ($items['items'] as $rr) {
                            if (isset($rr['id']) && isset($product_arr[$p])) {
                                if (in_array($rr['id'], $product_arr)) {
                                    array_push($item_list, $rr);
                                }
                            }
                        }
                        $p++;
                    }

                    $subTotal = $total = 0;
                    $subTotalKw = 0;
                    $baseCurrencyName = $baseCurrency->code_en;
                    if (isset($items['items'])) {
                        foreach ($items['items'] as $item) {
                            $subTotal += $item['final_price'] * $item['quantity'];
                            $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                        }
                    }
                    if ($order_model->payment_mode == 'C') {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
                    } else {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges;
                    }

                    $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
                    $vatCharges = 0;
                    $vatChargesKw = 0;
                    if ($vatPct != 0) {
                        $vatCharges = ($vatPct / 100) * $subTotal;
                        $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                    }
                    $total += $vatCharges;

                        
                        $sql_check_recent = \app\models\PharmacyOrderStatus::find()
                            ->where(['pharmacy_order_id'=>$pharmacy_order_id])
                            ->orderBy(['pharmacy_order_status_id' => SORT_DESC]);
                        $pharmacy_recent_status = $sql_check_recent->all();
                        
                        $pharmacy_recent_status_id = (!empty($pharmacy_recent_status)) ? $pharmacy_recent_status[0]['pharmacy_status_id'] : '';

                        if ($total_quantity != 0) {
                            $data[] = [
                                'order_id'          => (string) $order_id,
                                'order_number'      => $order_number,
                                'pharmacy_order_id' => (string) $pharmacy_order_id,
                                'pharmacy_id'       => (string) $pharmacy_id,
                                'pharmacy_name'       => $pharmacy_name,
                                'purchase_date'     => $purchase_date,
                                'quantity'          => (string)$total_quantity,
                                'sub_total' => (string) $subTotal,
                                'total' => (string) $total,
                                'cod_cost' => (string) $codCost,
                                'delivery_charges' => (string) $deliveryCharges,
                                'vat_charges' => (string) $vatCharges,
                                'discount_price' => (string) $discountPrice,
                                'pharmacy_status_id'=> (string)$pharmacy_recent_status_id, 
                                'driver_id'         => (string)$driver_id,
                                'status_id' => !empty($pharmacy_recent_status_id) ? $pharmacy_recent_status_id : 0,
                                'status_name'       => ($pharmacy_recent_status_id != '' && $pharmacy_recent_status_id != 2) ? 'Picked by Driver' : 'Ready for Pickup', 
                                'items'             => $item_list, 
                                'pharmacy_location' => (!empty($pharmacy_location_address)) ? $pharmacy_location_address : new stdClass(),
                            ];
                        }
                    
                }
                $this->data = $data; 
            } else {
                $this->response_code = 404;
                $this->message = 'No orders for this driver.';
            }
        }
        return $this->response();
    }

    public function actionDriverOrderDetails($driver_id,$id, $lang = "en",$type="delivery") {
        $store = $this->getStoreDetails();
        if($type=="delivery")
        {
            $orders = \app\models\Orders::find()
                    ->join('LEFT JOIN', '(SELECT t1.* FROM order_status AS t1 LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id AND t1.order_status_id < t2.order_status_id WHERE t2.order_id IS NULL) as temp', ' temp.order_id = orders.order_id')
                    ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id= pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                    ->join('LEFT JOIN', 'driver_orders', 'orders.order_id = driver_orders.type_id')
                    ->where(['driver_orders.driver_id' => $driver_id,
                        'is_processed' => [1], 'type' => 'O'])
                    ->andWhere(['IN', 'temp.status_id', [4, 7]])
                    ->orderBy(['status_date' => SORT_DESC])
                    ->all();
        }else if($type=="pickup")
        {
            $orders = \app\models\PharmacyOrderStatus::find()
                    ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_order_status.pharmacy_order_id')
                    ->join('LEFT JOIN', 'pharmacy_orders', 'pharmacy_orders.pharmacy_order_id = pharmacy_order_status.pharmacy_order_id')
                    ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'driver_suborders', 'pharmacy_orders.pharmacy_order_id = driver_suborders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'order_status', 'order_status.order_id = orders.order_id')
                    ->join('LEFT JOIN', 'pharmacy_status', 'pharmacy_status.pharmacy_status_id = pharmacy_order_status.pharmacy_status_id')
                    ->where(['driver_suborders.driver_id' => $driver_id,'pharmacy_orders.pharmacy_order_id'=>$id])
                    ->andWhere(['IN', 'pharmacy_order_status.pharmacy_status_id', [2]])
                    ->andWhere(['!=', 'pharmacy_order_status.pharmacy_status_id', 4])
                    ->andWhere(['!=', 'order_status.status_id', 5])
                    ->orderBy(['pharmacy_order_status.status_date' => SORT_DESC])
                    ->groupBy('order_items.pharmacy_order_id')
                    ->all();
        }
        $data = [];
        $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
        if($type=="delivery")
        {
            if(!empty($orders))
            {
                foreach ($orders as $order) 
                {

                    $pharmacy_order_model = \app\models\PharmacyOrders::findOne($order->order_id);
                    $pharmacy_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->pharmacy_id : '';
                    $pharmacy_Model = \app\models\Pharmacies::findOne($pharmacy_id);
                    $pharmacy_name = (!empty($pharmacy_Model)) ? $pharmacy_Model->{'name_'.$lang} : '';


                    $pharmacy_location = \app\models\PharmacyLocations::find()
                        ->where(['pharmacy_id' => $pharmacy_id,'is_deleted'=>'0']);
                    $pharmacy_location_model = $pharmacy_location->all();
                    $pharmacy_location_address = [];
                    if(!empty($pharmacy_location_model))
                    {
                        foreach($pharmacy_location_model as $row)
                        {
                            $country_name = (isset($row->area)) ? $row->area->state->country->name_en : '';
                            $pharmacy_location_address = [
                                'name'=> $row->{'name_'.$lang},
                                'country_name' => $country_name,
                                'governorate' => (isset($row->governorate)) ? $row->governorate->name_en : '',
                                'area' => (isset($row->area)) ? $row->area->name_en : '',
                                'block' => (isset($row->block)) ? $row->block : '',
                                'street' => (isset($row->street)) ?$row->street: '',
                                'building' => (isset($row->building)) ?$row->building: '',
                                'latlon' => (isset($row->latlon)) ?$row->latlon: '',
                            ];
                        }
                    }

                    $deliveryCharges = $this->convertPrice($order->delivery_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $codCost = $this->convertPrice($order->cod_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $discountPrice = $this->convertPrice($order->discount_price, Yii::$app->params['default_currency'], $store['currency_id']);
                    $vatCharges = $this->convertPrice($order->vat_charges, Yii::$app->params['default_currency'], $store['currency_id']);
                    $items = $this->cartDetails($order->order_id, $lang, $store);
                    $shippingAddress = $order->shippingAddress;
                    $subTotal = $total = 0;
                    $subTotalKw = 0;
                    $baseCurrencyName = $baseCurrency->code_en;
                    if (isset($items['items'])) {
                        foreach ($items['items'] as $item) {
                            $subTotal += $item['final_price'] * $item['quantity'];
                            $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                        }
                    }
                    if ($order->payment_mode == 'C') {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
                    } else {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges;
                    }

                    $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
                    $vatCharges = 0;
                    $vatChargesKw = 0;
                    if ($vatPct != 0) {
                        $vatCharges = ($vatPct / 100) * $subTotal;
                        $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                    }
                    $total += $vatCharges;
                    $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                    $decimals = 2;
                    if ($currencyModel->code_en == 'KWD') {
                        $decimals = 3;
                    }

                    $orderStatus = $order->getOrderStatuses()->orderBy(['status_date' => SORT_DESC])->limit(1)->one();

                    $vatCharges = $this->convertPrice($vatChargesKw, Yii::$app->params['default_currency_code'], $store['currency_id']);

        
                    
                    $temp = [
                        'order_id' => (string)$order->order_id,
                        'order_number' => (string)$order->order_number,
                        'pharmacy_id' => (string)$pharmacy_id,
                        'pharmacy_name'       => $pharmacy_name,
                        'recipient_name' => (string) $order->recipient_name,
                        'recipient_phone' => (string) $order->recipient_phone,
                        'purchase_date' => $order->create_date,
                        'payment_mode' => $order->payment_mode,
                        'sub_total' => (string) $subTotal,
                        'total' => (string) $total,
                        'cod_cost' => (string) $codCost,
                        'delivery_charges' => (string) $deliveryCharges,
                        'vat_charges' => (string) $vatCharges,
                        'discount_price' => (string) $discountPrice,
                        'baseCurrencyName' => $baseCurrencyName,
                        'items' => (isset($items['items'])) ? $items['items'] : [],
                        'status_id' => !empty($orderStatus) ? $orderStatus->status_id : 0,
                        'status_name' => !empty($orderStatus) ? $orderStatus->status->{"name_" . $lang} : "",
                        'status_color' => !empty($orderStatus) ? $orderStatus->status->color : "",
                        'is_return_active' => !empty($orderStatus) ? (($orderStatus->status_id == 5) ? 1 : 0) : 0,
                        'is_cancel_active' => !empty($orderStatus) ? (($orderStatus->status_id < 2) ? 1 : 0) : 0,
                        'shipping_address' => [
                            'first_name' => (!empty($shippingAddress)) ? $shippingAddress->first_name : "",
                            'last_name' => (!empty($shippingAddress)) ? $shippingAddress->last_name : "",
                            'area_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->area) ? $shippingAddress->area->name_en : "") : "",
                            'governorate_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->state) ? $shippingAddress->state->name_en : "") : "",
                            'country_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->name_en : "") : "",
                            'phonecode' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->phonecode : "") : "",
                            'block_name' => (!empty($shippingAddress->block_id)) ? \app\helpers\AppHelper::getBlockNameById($shippingAddress->block_id, $lang) : '',
                            'street' => (!empty($shippingAddress)) ? $shippingAddress->street : "",
                            'flat' => (!empty($shippingAddress)) ? $shippingAddress->flat : "",
                            'floor' => (!empty($shippingAddress)) ? $shippingAddress->floor : "",
                            'building' => (!empty($shippingAddress)) ? $shippingAddress->building : "",
                            'addressline_1' => (!empty($shippingAddress)) ? $shippingAddress->addressline_1 : "",
                            'mobile_number' => (!empty($shippingAddress)) ? $shippingAddress->mobile_number : "",
                            'alt_phone_number' => (!empty($shippingAddress)) ? $shippingAddress->alt_phone_number : "",
                            'location_type' => (!empty($shippingAddress)) ? $shippingAddress->location_type : "",
                            'notes' => (!empty($shippingAddress)) ? $shippingAddress->notes : "",
                        ],
                        'pharmacy_location' => (!empty($pharmacy_location_address)) ? $pharmacy_location_address : new stdClass(),
                        'payment_details' => $this->getOrderPaymentDetails($order->order_id),
                    ];
                    $this->data = $temp;
                }
                return $this->response();
                /*$this->data = $data;
                if (!empty($data)) {
                    $this->data = $data;
                } else {
                    $this->response_code = 404;
                    $this->message = 'No orders for this driver.';
                }*/
            }else {
                $this->response_code = 404;
                $this->message = 'No orders for this driver.';
                $this->data = new stdClass();
                return $this->response();
            }
        }else if($type=="pickup")
        {
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $pharmacy_order_id = $order['pharmacy_order_id'];

                    $pharmacy_order_model = \app\models\PharmacyOrders::findOne($pharmacy_order_id);
                    $order_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->order_id : '';
                    $order_number = (!empty($pharmacy_order_model))? $pharmacy_order_model->order_number : '';
                    $pharmacy_id = (!empty($pharmacy_order_model))? $pharmacy_order_model->pharmacy_id : '';
                    $pharmacy_Model = \app\models\Pharmacies::findOne($pharmacy_id);
                    $pharmacy_name = (!empty($pharmacy_Model)) ? $pharmacy_Model->{'name_'.$lang} : '';
                    $pharmacy_location = \app\models\PharmacyLocations::find()
                        ->where(['pharmacy_id' => $pharmacy_id,'is_deleted'=>'0']);
                    $pharmacy_location_model = $pharmacy_location->all();
                    $pharmacy_location_address = [];
                    if(!empty($pharmacy_location_model))
                    {
                        foreach($pharmacy_location_model as $row)
                        {
                            $country_name = (isset($row->area)) ? $row->area->state->country->name_en : '';
                            $pharmacy_location_address = [
                                'name'=> $row->{'name_'.$lang},
                                'country_name' => $country_name,
                                'governorate' => (isset($row->governorate)) ? $row->governorate->name_en : '',
                                'area' => (isset($row->area)) ? $row->area->name_en : '',
                                'block' => (isset($row->block)) ? $row->block : '',
                                'street' => (isset($row->street)) ?$row->street: '',
                                'building' => (isset($row->building)) ?$row->building: '',
                                'latlon' => (isset($row->latlon)) ?$row->latlon: '',
                            ];
                        }
                    }
                    

                    $order_model = \app\models\Orders::findOne($order_id);
                    $shippingAddress = $order_model->shippingAddress;  
                    $purchase_date = (!empty($order_model)) ? $order_model->create_date : '';

                    $deliveryCharges = $this->convertPrice($order_model->delivery_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $codCost = $this->convertPrice($order_model->cod_charge, Yii::$app->params['default_currency'], $store['currency_id']);
                    $discountPrice = $this->convertPrice($order_model->discount_price, Yii::$app->params['default_currency'], $store['currency_id']);
                    $vatCharges = $this->convertPrice($order_model->vat_charges, Yii::$app->params['default_currency'], $store['currency_id']);


                        $sql1 = \app\models\OrderItems::find()
                            ->join('LEFT JOIN', 'product', 'order_items.product_id = product.product_id')
                            ->where(['pharmacy_order_id'=>$pharmacy_order_id])
                            ->orderBy(['order_item_id' => SORT_ASC]);
                        $order_items = $sql1->all();
                        $total_quantity = 0;
                        $total_bill = 0;
                        $product_arr = [];
                        if (!empty($order_items)) {
                            foreach ($order_items as $r) {
                                $total_quantity += $r['quantity'];
                                $total_bill += ($r['price'] * $r['quantity']);
                                array_push($product_arr, $r['product_id']);
                            }
                        }
                        $items = $this->cartDetails($order_id, $lang, $store);
                        $item_list = [];
                        $p = 0;
                        if (!empty($items)) {
                            foreach ($items['items'] as $rr) {
                                if (isset($rr['id']) && isset($product_arr[$p])) {
                                    if (in_array($rr['id'], $product_arr)) {
                                        array_push($item_list, $rr);
                                    }
                                }
                            }
                            $p++;
                        }


                    $subTotal = $total = 0;
                    $subTotalKw = 0;
                    $baseCurrencyName = $baseCurrency->code_en;
                    if (isset($items['items'])) {
                        foreach ($items['items'] as $item) {
                            $subTotal += $item['final_price'] * $item['quantity'];
                            $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                        }
                    }
                    if ($order_model->payment_mode == 'C') {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
                    } else {
                        $total = ($subTotal - $discountPrice) + $deliveryCharges;
                    }

                    $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
                    $vatCharges = 0;
                    $vatChargesKw = 0;
                    if ($vatPct != 0) {
                        $vatCharges = ($vatPct / 100) * $subTotal;
                        $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                    }
                    $total += $vatCharges;

                        
                        $sql_check_recent = \app\models\PharmacyOrderStatus::find()
                            ->where(['pharmacy_order_id'=>$pharmacy_order_id])
                            ->orderBy(['pharmacy_order_status_id' => SORT_DESC]);
                        $pharmacy_recent_status = $sql_check_recent->all();
                        
                        $pharmacy_recent_status_id = (!empty($pharmacy_recent_status)) ? $pharmacy_recent_status[0]['pharmacy_status_id'] : '';
                        $pharmacy_recent_status_color = '';
                        if($pharmacy_recent_status_id!="")
                        {
                            $pharmacy_status = \app\models\PharmacyStatus::findOne($pharmacy_recent_status_id);
                            $pharmacy_recent_status_color = (!empty($pharmacy_status)) ? $pharmacy_status->color : '';
                        }
                        

                        if ($total_quantity != 0) {
                            
                            $temp = [
                                'order_id'          => (string)$order_id,
                                'pharmacy_order_id' => (string)$pharmacy_order_id,
                                'pharmacy_id'       => (string)$pharmacy_id,
                                'pharmacy_name'       => (string)$pharmacy_name,
                                'order_number'      => (string)$order_number,
                                'purchase_date'     => $purchase_date,
                                'quantity'          => $total_quantity,
                                'payment_mode' => $order_model->payment_mode,
                                'sub_total' => (string) $subTotal,
                                'total' => (string) $total,
                                'cod_cost' => (string) $codCost,
                                'delivery_charges' => (string) $deliveryCharges,
                                'vat_charges' => (string) $vatCharges,
                                'discount_price' => (string) $discountPrice, 
                                'driver_id'         => $driver_id,
                                'status_id'=> $pharmacy_recent_status_id,
                                'status_name'       => ($pharmacy_recent_status_id != '' && $pharmacy_recent_status_id != 2) ? 'Picked by Driver' : 'Ready for Pickup',
                                'status_color' => (string)($pharmacy_recent_status_color!="") ? $pharmacy_recent_status_color : "",
                         
                                'items'             => $item_list, 
                                'shipping_address' => [
                                    'first_name' => (!empty($shippingAddress)) ? $shippingAddress->first_name : "",
                                    'last_name' => (!empty($shippingAddress)) ? $shippingAddress->last_name : "",
                                    'area_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->area) ? $shippingAddress->area->name_en : "") : "",
                                    'governorate_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->state) ? $shippingAddress->state->name_en : "") : "",
                                    'country_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->name_en : "") : "",
                                    'phonecode' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->phonecode : "") : "",
                                    'block_name' => (!empty($shippingAddress->block_id)) ? \app\helpers\AppHelper::getBlockNameById($shippingAddress->block_id, $lang) : '',
                                    'street' => (!empty($shippingAddress)) ? $shippingAddress->street : "",
                                    'flat' => (!empty($shippingAddress)) ? $shippingAddress->flat : "",
                                    'floor' => (!empty($shippingAddress)) ? $shippingAddress->floor : "",
                                    'building' => (!empty($shippingAddress)) ? $shippingAddress->building : "",
                                    'addressline_1' => (!empty($shippingAddress)) ? $shippingAddress->addressline_1 : "",
                                    'mobile_number' => (!empty($shippingAddress)) ? $shippingAddress->mobile_number : "",
                                    'alt_phone_number' => (!empty($shippingAddress)) ? $shippingAddress->alt_phone_number : "",
                                    'location_type' => (!empty($shippingAddress)) ? $shippingAddress->location_type : "",
                                    'notes' => (!empty($shippingAddress)) ? $shippingAddress->notes : "",
                                ],
                                'pharmacy_location' => (!empty($pharmacy_location_address)) ? $pharmacy_location_address : new stdClass(),
                            ];
                        }
                        $this->data = $temp;
                        return $this->response();
                }
                
            } else {
                $this->response_code = 404;
                $this->message = 'No orders for this driver.';
                $this->data = new stdClass();
                return $this->response();
            }
        }
         
    }

    public function actionChangeOrderStatus() {
        $request = Yii::$app->request->bodyParams;

        if (!empty($request)) 
        {
            if($request['type'] == 'pickup') 
            {
                $driver = \app\models\Drivers::findOne($request['driver_id']);

                if (empty($driver)) {
                    $this->response_code = 400;
                    $this->message = 'Driver does not exist.';
                    return $this->response();
                }

                $sql_check_recent = \app\models\PharmacyOrderStatus::find()
                            ->where(['pharmacy_order_id'=>$request['order_id']])
                            ->orderBy(['pharmacy_order_status_id' => SORT_DESC]);
                $pharmacy_recent_status = $sql_check_recent->all();
                
                $currentStatus = (!empty($pharmacy_recent_status)) ? $pharmacy_recent_status[0]['pharmacy_status_id'] : '';

                if ($currentStatus == 2) 
                {
                    $order_status = new \app\models\PharmacyOrderStatus();
                    $order_status->pharmacy_order_id = $request['order_id'];
                    $order_status->pharmacy_status_id = $request['status'];
                    $order_status->status_date = date('Y-m-d H:i:s');
                    $order_status->user_type = 'D';
                    $order_status->user_id = $request['driver_id'];
                    $order_status->comment = ($request['comment'] != '') ? $request['comment'] : 'Automated order status update via driver.';
                    if ($order_status->save(false)) 
                    {

                        /** START TO CHANGE STATUS AS OUT FOR DELIVERY AND ASSIGN SAME DRIVER TO MAIN ORDER **/
                        $model_pharmacy_order = \app\models\PharmacyOrders::findOne($request['order_id']);

                        $order_id = $model_pharmacy_order->order_id;

                        $check = \app\models\OrderStatus::find()
                                ->where(['order_id' => $order_id])
                                ->orderBy(['order_status_id' => SORT_DESC])
                                ->one();
                        if (!empty($check) && $check->status_id == 4) {
                            return json_encode(['status' => 201, 'msg' => 'The order is already in "' . strtoupper($check->status->name_en) . '".']);
                        }

                        $status = new \app\models\OrderStatus();
                        $status->order_id = $order_id;
                        $status->status_id = 4;
                        $status->user_type = 'D';
                        $status->user_id = $request['driver_id'];
                        $status->comment = 'Automatically changed status once picked by driver';
                        $status->notify_customer =  0;
                        $status->status_date = date('Y-m-d H:i:s');
                        //debugPrint($status);die;
                        $status->save();

                        $driverOrderModel = new \app\models\DriverOrders();
                        $driverOrderModel->type = 'O';
                        $driverOrderModel->type_id = $order_id;
                        $driverOrderModel->driver_id = $request['driver_id'];
                        $driverOrderModel->assigned_date = date("Y-m-d H:i:s");
                        $driverOrderModel->save();
                        
                        /** END TO CHANGE STATUS AS OUT FOR DELIVERY AND ASSIGN SAME DRIVER TO MAIN ORDER **/

                        $this->response_code = 200;
                        $this->message = 'success';
                    } else {
                        $this->response_code = 500;
                        $this->message = 'error';
                    }
                } else {
                    $this->response_code = 400;
                    $this->message = 'Invalid Status';
                }
            }else if ($request['type'] == 'delivery') 
            {
                $driver = \app\models\Drivers::findOne($request['driver_id']);

                if (empty($driver)) {
                    $this->response_code = 400;
                    $this->message = 'Driver does not exist.';
                    return $this->response();
                }

                $currentStatus = AppHelper::getCurrentOrderStatus($request['order_id']);
                

                if ($currentStatus == 7 || $currentStatus == 4) 
                {
                    $order_status = new \app\models\OrderStatus();
                    $order_status->order_id = $request['order_id'];
                    $order_status->status_id = $request['status'];
                    $order_status->status_date = date('Y-m-d H:i:s');
                    $order_status->user_type = 'D';
                    $order_status->user_id = $request['driver_id'];
                    $order_status->comment = ($request['comment'] != '') ? $request['comment'] : 'Automated order status update via driver.';
                    $imageName = '';
                    if ($request['image'] != '') {
                        define('PATH', Yii::$app->basePath . '/web/uploads/');
                        $img = $request['image'];
                        $img = str_replace('data:image/jpeg;base64,', '', $img);
                        $img = str_replace(' ', '+', $img);
                        $data = base64_decode($img);
                        $file = PATH . uniqid() . '.jpeg';
                        $success = file_put_contents($file, $data);
                        $profile_pict = $success ? str_replace(PATH, '', $file) : '';
                        $imageName = ($profile_pict != '') ? $profile_pict : '';
                        $order_status->delivery_proof = $imageName;
                    }
                    
                    $model = \app\models\Orders::find()
                            ->where(['order_id' => $request['order_id']])
                            ->one();

                       

                    if ($order_status->save(false)) 
                    {

                        if (!empty($model)) 
                        {
                            $store = \app\models\Stores::findOne($model->store_id);
                            $userModel = $model->user;
                            if ($model->user_id == null) {
                                $email = $model->shipping_email;
                                $name = $model->recipient_name;
                            } else {
                                $email = $userModel->email;
                                $name = $userModel->first_name . ' ' . $userModel->last_name;
                            }
                            //Sending Push Notification
                            $msg = 'Status for your order #' . $model->order_number . ' has been changed to delivered';
                            if (!empty($userModel->device_token) && $userModel->push_enabled == '1') {
                                $target_id = (string) $model->order_id;
                                \app\helpers\AppHelper::sendPushwoosh($msg, [$userModel->device_token], "O", $target_id);
                            }
                            $subject = 'Status of order #' . $model->order_number . ' has been changed to delivered';
                            Yii::$app->mailer->compose('@app/mail/order-status-change', [
                                        'supportEmail' => Yii::$app->params['supportEmail'],
                                        'model' => $model,
                                        'status' => 'delivered',
                                        'name' => $name,
                                        'order_number' => $model->order_number,
                                        'store' => $store,
                                        'comment' => 'Driver changed status by delivered',
                                        'notify_customer' => 1,
                                        'statusModel' => $order_status,
                                    ])
                                    ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                    ->setTo($email)
                                    ->setSubject($subject)
                                    ->send();
                            //get all admin emails 

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
                                            'status' => 'delivered',
                                            'name' => $model->user->first_name . ' ' . $model->user->last_name,
                                            'order_number' => $model->order_number,
                                        ])
                                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                        ->setTo($adminEmails)
                                        ->setSubject("Eyadat order status change")
                                        ->send();
                        }


                        $this->response_code = 200;
                        $this->message = 'success';
                    } else {
                        $this->response_code = 500;
                        $this->message = 'error';
                    }
                } else {
                    $this->response_code = 400;
                    $this->message = 'Invalid Status';
                }
            }

        }

        return $this->response();
    }

}
