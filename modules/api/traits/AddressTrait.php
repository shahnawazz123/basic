<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\traits;
use Yii;
use app\helpers\AppHelper;
/**
 *
 * @author akram
 */
trait AddressTrait
{

    public function actionAddAddress($lang = "en", $store = "") {
        $request = Yii::$app->request->bodyParams;
        $store = $this->getStoreDetails($store);
        if (!empty($request)) {
            $userAddresses = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $request['user_id'], 'is_deleted' => 0])
                    ->count();
            $subTotal = $totalItems = 0;
            $subTotalKw = 0;
            if (isset($request['order_id']) && $request['order_id'] != "") {
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                    }
                }
            }
            $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
            $decimals = 2;
            if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                $decimals = 3;
            }
            $model = new \app\models\ShippingAddresses();
            $model->first_name = (isset($request['first_name'])) ? $request['first_name'] : "";
            $model->last_name = (isset($request['last_name'])) ? $request['last_name'] : "";
            $model->user_id = (isset($request['user_id'])) ? $request['user_id'] : "";
            $model->country_id = (isset($request['country_id'])) ? $request['country_id'] : "";
            $model->state_id = (isset($request['state_id'])) ? $request['state_id'] : "";
            $model->area_id = (isset($request['area_id'])) ? $request['area_id'] : "";
            $model->block_id = (isset($request['block_id'])) ? $request['block_id'] : "";
            $model->block_name = (isset($request['block'])) ? $request['block'] : "";
            $model->street = (isset($request['street'])) ? $request['street'] : "";
            $model->avenue = (isset($request['avenue'])) ? $request['avenue'] : "";
            $model->landmark = (isset($request['landmark'])) ? $request['landmark'] : "";
            $model->flat = (isset($request['flat_number'])) ? $request['flat_number'] : "";
            $model->floor = (isset($request['floor_number'])) ? $request['floor_number'] : "";
            $model->building = (isset($request['building_number'])) ? $request['building_number'] : "";
            $model->addressline_1 = (isset($request['addressline_1'])) ? $request['addressline_1'] : "";
            $model->mobile_number = (isset($request['mobile_number'])) ? $request['mobile_number'] : "";
            $model->alt_phone_number = (isset($request['alt_phone_number'])) ? $request['alt_phone_number'] : "";
            $model->location_type = (isset($request['location_type'])) ? $request['location_type'] : "";
            $model->notes = (isset($request['notes'])) ? $request['notes'] : "";
            $model->is_default = ($userAddresses == 0) ? 1 : 0;
            if ($model->save()) {
                $this->message = 'Address successfully added.';
                $country = (isset($model->country_id) && !empty($model->country_id)) ? $model->country_id : 114;
                $orderTotal = '';
                if ($subTotal != 0) {
                    $orderTotal = $subTotal;
                }
                $deliveryOptions = AppHelper::getDeliveryOptions($lang, $country, $store, $orderTotal, $totalItems);
                $vatCharges = 0;
                $vatPct = $model->country->vat;
                $vatChargesKw = 0;
                if (!empty($subTotal)) {
                    if (!empty($vatPct)) {
                        $vatCharges = ($vatPct / 100) * $subTotal;
                        $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                    }
                }
                $vatCharges = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
                $this->data = [
                    'address_id' => $model->shipping_address_id,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'area_id' => $model->area_id,
                    'area_name' => !empty($model->area) ? (($lang == 'en') ? $model->area->name_en : $model->area->name_ar) : "",
                    'governorate_id' => !empty($model->state) ? $model->state_id : "",
                    'governorate_name' => !empty($model->state) ? (($lang == 'en') ? $model->state->name_en : $model->state->name_ar) : "",
                    'country_id' => !empty($model->country_id) ? $model->country_id : "",
                    'country_name' => !empty($model->country_id) ? (($lang == 'en') ? $model->country->name_en : $model->country->name_ar) : "",
                    'vat_pct' => (string) $vatPct,
                    'vat_charges' => (!empty($vatCharges)) ? (string) number_format($vatCharges, $decimals) : '0',
                    'block_id' => $model->block_id,
                    'block_name' => (!empty($model->block_id)) ? (!empty($model->block) ? (($lang == 'en') ? $model->block->name_en : $model->block->name_ar) : "") : $model->block_name,
                    'street' => $model->street,
                    'avenue' => (!empty($model->avenue)) ? $model->avenue : "",
                    'landmark' => (!empty($model->landmark)) ? $model->landmark : "",
                    'flat' => (!empty($model->flat)) ? $model->flat : "",
                    'floor' => (!empty($model->floor)) ? $model->floor : "",
                    'building' => (!empty($model->building)) ? $model->building : "",
                    'addressline_1' => $model->addressline_1,
                    'mobile_number' => $model->mobile_number,
                    'alt_phone_number' => $model->alt_phone_number,
                    'location_type' => $model->location_type,
                    'notes' => $model->notes,
                    'id_number' => (!empty($model->id_number)) ? $model->id_number : "",
                    'is_default' => ($model->is_default == '0') ? "No" : "Yes",
                    'shipping_cost' => !empty($model->country) ? (string) $this->convertPrice($model->country->shipping_cost, 82, $store['currency_id']) : '0',
                    'cod_cost' => !empty($model->country) ? (string) $this->convertPrice($model->country->cod_cost, 82, $store['currency_id']) : "0",
                    'is_cod_enable' => !empty($model->country) ? $model->country->is_cod_enable : 0,
                    'phonecode' => !empty($model->country) ? $model->country->phonecode : "",
                    'delivery_options' => $deliveryOptions
                ];
            } else {
                $this->response_code = 500;
                $this->data = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionUserAddress($user_id, $lang = "en", $store = "", $order_id = "") {
        if (isset($user_id) && $user_id != "") {
            $store = $this->getStoreDetails($store);
            $models = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $user_id, 'is_deleted' => 0])
                    ->orderBy(['is_default' => SORT_DESC])
                    ->all();
            $subTotal = $totalItems = 0;
            $subTotalKw = 0;
            $cartDetails = $this->cartDetails($order_id, $lang, $store);
            if (isset($cartDetails['items'])) {
                foreach ($cartDetails['items'] as $item) {
                    $subTotal += ($item['final_price'] * $item['quantity']);
                    $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                    $totalItems += $item['quantity'];
                }
            }
            $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
            $decimals = 2;
            if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                $decimals = 3;
            }
            if (!empty($models)) {
                $result = [];
                foreach ($models as $row) {
                    $orderTotal = '';
                    if ($subTotal != 0) {
                        $orderTotal = $subTotal;
                    }
                    $deliveryOptions = AppHelper::getDeliveryOptions($lang, $row->country_id, $store, $orderTotal, $totalItems);
                    $vatCharges = 0;
                    $vatPct = (!empty($row->country->vat)) ? $row->country->vat : 0;
                    $vatChargesKw = 0;
                    if (!empty($subTotal)) {
                        if (!empty($vatPct)) {
                            $vatCharges = ($vatPct / 100) * $subTotal;
                            $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                        }
                    }
                    $vatCharges = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
                    $d['address_id'] = $row->shipping_address_id;
                    $d['first_name'] = $row->first_name;
                    $d['last_name'] = $row->last_name;
                    $d['area_id'] = (string) $row->area_id;
                    $d['area_name'] = !empty($row->area) ? (($lang == 'en') ? $row->area->name_en : $row->area->name_ar) : "";
                    $d['governorate_id'] = !empty($row->state) ? $row->state_id : "";
                    $d['governorate_name'] = !empty($row->state) ? (($lang == 'en') ? $row->state->name_en : $row->state->name_ar) : "";
                    $d['country_id'] = !empty($row->country_id) ? $row->country_id : "";
                    $d['country_name'] = !empty($row->country_id) ? (($lang == 'en') ? $row->country->name_en : $row->country->name_ar) : "";
                    $d['vat_pct'] = (string) $vatPct;
                    $d['vat_charges'] = (!empty($vatCharges)) ? (string) number_format($vatCharges, $decimals) : '0';
                    $d['block_id'] = (string) $row->block_id;
                    $d['block_name'] = (!empty($row->block_id)) ? (!empty($row->block) ? (($lang == 'en') ? $row->block->name_en : $row->block->name_ar) : "") : $row->block_name;
                    $d['shipping_cost'] = !empty($deliveryOptions[0]) ? $deliveryOptions[0]['price'] : '0';
                    $d['express_shipping_cost'] = !empty($deliveryOptions[1]) ? $deliveryOptions[1]['price'] : '0';
                    $d['cod_cost'] = !empty($row->country) ? (string) $this->convertPrice($row->country->cod_cost, 82, $store['currency_id']) : "0";
                    $d['is_cod_enable'] = !empty($row->country) ? $row->country->is_cod_enable : 0;
                    $d['phonecode'] = !empty($row->country) ? $row->country->phonecode : "";
                    $d['street'] = $row->street;
                    $d['avenue'] = !empty($row->avenue) ? $row->avenue : "";
                    $d['landmark'] = !empty($row->landmark) ? $row->landmark : "";
                    $d['flat'] = !empty($row->flat) ? $row->flat : "";
                    $d['floor'] = !empty($row->floor) ? $row->floor : "";
                    $d['building'] = !empty($row->building) ? $row->building : "";
                    $d['addressline_1'] = (string) $row->addressline_1;
                    $d['mobile_number'] = $row->mobile_number;
                    $d['alt_phone_number'] = $row->alt_phone_number;
                    $d['location_type'] = (string) $row->location_type;
                    $d['notes'] = $row->notes;
                    $d['is_default'] = ($row->is_default == '0') ? "No" : "Yes";
                    $d['delivery_options'] = $deliveryOptions;
                    array_push($result, $d);
                }
                $this->data = $result;
            } else {
                $this->response_code = 200;
                $this->message = 'No address found for requested user.';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionUpdateAddress($lang = "en", $store = "") {
        $store = $this->getStoreDetails($store);
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $request['user_id'], 'shipping_address_id' => $request['shipping_address_id']])
                    ->one();
            $subTotal = $totalItems = 0;
            $subTotalKw = 0;
            if (isset($request['order_id']) && $request['order_id'] != "") {
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                    }
                }
            }
            $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
            $decimals = 2;
            if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                $decimals = 3;
            }
            if (!empty($model)) {
                $model->first_name = (isset($request['first_name'])) ? $request['first_name'] : "";
                $model->last_name = (isset($request['last_name'])) ? $request['last_name'] : "";
                $model->user_id = (isset($request['user_id'])) ? $request['user_id'] : "";
                $model->country_id = (isset($request['country_id'])) ? $request['country_id'] : "";
                $model->state_id = (isset($request['state_id'])) ? $request['state_id'] : "";
                $model->area_id = (isset($request['area_id'])) ? $request['area_id'] : "";
                $model->block_id = (isset($request['block_id'])) ? $request['block_id'] : "";
                $model->block_name = (isset($request['block'])) ? $request['block'] : "";
                $model->street = (isset($request['street'])) ? $request['street'] : "";
                $model->avenue = (isset($request['avenue'])) ? $request['avenue'] : "";
                $model->landmark = (isset($request['landmark'])) ? $request['landmark'] : "";
                $model->flat = (isset($request['flat_number'])) ? $request['flat_number'] : "";
                $model->floor = (isset($request['floor_number'])) ? $request['floor_number'] : "";
                $model->building = (isset($request['building_number'])) ? $request['building_number'] : "";
                $model->addressline_1 = (isset($request['addressline_1'])) ? $request['addressline_1'] : "";
                $model->mobile_number = (isset($request['mobile_number'])) ? $request['mobile_number'] : "";
                $model->alt_phone_number = (isset($request['alt_phone_number'])) ? $request['alt_phone_number'] : "";
                $model->location_type = (isset($request['location_type'])) ? $request['location_type'] : "";
                $model->notes = (isset($request['notes'])) ? $request['notes'] : "";
                if (isset($request['is_default']) && $request['is_default'] != "") {
                    if ($request['is_default'] == '1') {
                        \app\models\ShippingAddresses::updateAll(['is_default' => 0], 'user_id = ' . $request['user_id']);
                    }
                    $model->is_default = $request['is_default'];
                }
                if ($model->save()) {
                    $this->message = 'Address successfully updated.';
                    $country = (isset($model->country_id) && !empty($model->country_id)) ? $model->country_id : 114;
                    $orderTotal = '';
                    if ($subTotal != 0) {
                        $orderTotal = $subTotal;
                    }
                    $deliveryOptions = AppHelper::getDeliveryOptions($lang, $country, $store, $orderTotal, $totalItems);
                    $vatCharges = $vatPct = 0;
                    $vatChargesKw = 0;
                    if (!empty($subTotal)) {
                        $vatPct = $model->country->vat;
                        if (!empty($vatPct)) {
                            $vatCharges = ($vatPct / 100) * $subTotal;
                            $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                        }
                    }
                    $vatCharges = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
                    $this->data = [
                        'address_id' => $model->shipping_address_id,
                        'first_name' => $model->first_name,
                        'last_name' => $model->last_name,
                        'area_id' => $model->area_id,
                        'area_name' => !empty($model->area) ? (($lang == 'en') ? $model->area->name_en : $model->area->name_ar) : "",
                        'governorate_id' => !empty($model->state) ? $model->state_id : "",
                        'governorate_name' => !empty($model->state) ? (($lang == 'en') ? $model->state->name_en : $model->state->name_ar) : "",
                        'country_id' => !empty($model->country_id) ? $model->country_id : "",
                        'country_name' => !empty($model->country_id) ? (($lang == 'en') ? $model->country->name_en : $model->country->name_ar) : "",
                        'vat_pct' => (string) $vatPct,
                        'vat_charges' => (!empty($vatCharges)) ? (string) number_format($vatCharges, $decimals) : '0',
                        'block_id' => $model->block_id,
                        'block_name' => (!empty($model->block_id)) ? (!empty($model->block) ? (($lang == 'en') ? $model->block->name_en : $model->block->name_ar) : "") : $model->block_name,
                        'street' => $model->street,
                        'avenue' => (!empty($model->avenue)) ? $model->avenue : "",
                        'landmark' => (!empty($model->landmark)) ? $model->landmark : "",
                        'flat' => (!empty($model->flat)) ? $model->flat : "",
                        'floor' => (!empty($model->floor)) ? $model->floor : "",
                        'building' => (!empty($model->building)) ? $model->building : "",
                        'addressline_1' => $model->addressline_1,
                        'mobile_number' => $model->mobile_number,
                        'alt_phone_number' => $model->alt_phone_number,
                        'location_type' => $model->location_type,
                        'notes' => $model->notes,
                        'is_default' => ($model->is_default == '0') ? "No" : "Yes",
                        'shipping_cost' => !empty($model->country) ? (string) $this->convertPrice($model->country->shipping_cost, 82, $store['currency_id']) : "",
                        'cod_cost' => !empty($model->country) ? (string) $this->convertPrice($model->country->cod_cost, 82, $store['currency_id']) : "",
                        'is_cod_enable' => !empty($model->country) ? $model->country->is_cod_enable : 0,
                        'phonecode' => !empty($model->country) ? $model->country->phonecode : "",
                        'delivery_options' => $deliveryOptions
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 404;
                $this->message = 'The request address does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDeleteAddress($lang = "en") {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $id = $request['shipping_address_id'];
            $user_id = $request['user_id'];
            $data = explode(',', $id);
            foreach ($data as $d) {
                $model = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $user_id, 'shipping_address_id' => $d])
                        ->one();
                if (!empty($model)) {
                    $model->is_deleted = 1;
                    if (!$model->save()) {
                        debugPrint($model->errors);
                        exit;
                    }
                }
            }
            $models = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $user_id, 'is_deleted' => 0])
                    ->orderBy(['is_default' => SORT_DESC])
                    ->all();
            if (!empty($models)) {
                $result = [];
                foreach ($models as $row) {
                    $d = [];
                    $d['address_id'] = $row->shipping_address_id;
                    $d['first_name'] = $row->first_name;
                    $d['last_name'] = $row->last_name;
                    $d['area_id'] = (string) $row->area_id;
                    $d['area_name'] = !empty($row->area) ? (($lang == 'en') ? $row->area->name_en : $row->area->name_ar) : "";
                    $d['governorate_id'] = !empty($row->state) ? $row->state_id : "";
                    $d['governorate_name'] = !empty($row->state) ? (($lang == 'en') ? $row->state->name_en : $row->state->name_ar) : "";
                    $d['country_id'] = !empty($row->country_id) ? $row->country_id : "";
                    $d['country_name'] = !empty($row->country_id) ? (($lang == 'en') ? $row->country->name_en : $row->country->name_ar) : "";
                    $d['block_id'] = (string) $row->block_id;
                    $d['block_name'] = (!empty($row->block_id)) ? (!empty($row->block) ? (($lang == 'en') ? $row->block->name_en : $row->block->name_ar) : "") : $row->block_name;
                    $d['street'] = $row->street;
                    $d['addressline_1'] = $row->addressline_1;
                    $d['mobile_number'] = $row->mobile_number;
                    $d['alt_phone_number'] = $row->alt_phone_number;
                    $d['location_type'] = $row->location_type;
                    $d['notes'] = $row->notes;
                    $d['is_default'] = ($row->is_default == '0') ? "No" : "Yes";
                    $d['shipping_cost'] = !empty($row->country) ? (string) $row->country->shipping_cost : "";
                    $d['cod_cost'] = !empty($row->country) ? (string) $row->country->cod_cost : "";
                    $d['is_cod_enable'] = !empty($row->country) ? $row->country->is_cod_enable : 0;
                    $d['phonecode'] = !empty($row->country) ? $row->country->phonecode : "";
                    array_push($result, $d);
                }
                $this->data = $result;
            } else {
                $this->message = 'Address successfully deleted.';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionMakeDefaultAddress($lang = "en") {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $id = $request['shipping_address_id'];
            $user_id = $request['user_id'];
            \app\models\ShippingAddresses::updateAll(['is_default' => 0], ['user_id' => $user_id]);
            $model = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $user_id, 'shipping_address_id' => $id])
                    ->one();
            if (!empty($model)) {
                $model->is_default = 1;
                $model->save();
                $models = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $user_id, 'is_deleted' => 0])
                        ->orderBy(['is_default' => SORT_DESC])
                        ->all();
                if (!empty($models)) {
                    $result = [];
                    foreach ($models as $row) {
                        $d['address_id'] = $row->shipping_address_id;
                        $d['first_name'] = $row->first_name;
                        $d['last_name'] = $row->last_name;
                        $d['area_id'] = (string) $row->area_id;
                        $d['area_name'] = !empty($row->area) ? (($lang == 'en') ? $row->area->name_en : $row->area->name_ar) : "";
                        $d['governorate_id'] = !empty($row->state) ? $row->state_id : "";
                        $d['governorate_name'] = !empty($row->state) ? (($lang == 'en') ? $row->state->name_en : $row->state->name_ar) : "";
                        $d['country_id'] = !empty($row->country_id) ? $row->country_id : "";
                        $d['country_name'] = !empty($row->country_id) ? (($lang == 'en') ? $row->country->name_en : $row->country->name_ar) : "";
                        $d['block_id'] = (string) $row->block_id;
                        $d['block_name'] = (!empty($row->block_id)) ? (!empty($row->block) ? (($lang == 'en') ? $row->block->name_en : $row->block->name_ar) : "") : $row->block_name;
                        $d['street'] = $row->street;
                        $d['avenue'] = !empty($row->avenue) ? $row->avenue : "";
                        $d['landmark'] = !empty($row->landmark) ? $row->landmark : "";
                        $d['flat'] = !empty($row->flat) ? $row->flat : "";
                        $d['floor'] = !empty($row->floor) ? $row->floor : "";
                        $d['building'] = !empty($row->building) ? $row->building : "";
                        $d['addressline_1'] = $row->addressline_1;
                        $d['mobile_number'] = $row->mobile_number;
                        $d['alt_phone_number'] = $row->alt_phone_number;
                        $d['location_type'] = $row->location_type;
                        $d['notes'] = $row->notes;
                        $d['is_default'] = ($row->is_default == '0') ? "No" : "Yes";
                        $d['shipping_cost'] = !empty($row->country) ? (string) $row->country->shipping_cost : "";
                        $d['cod_cost'] = !empty($row->country) ? (string) $row->country->cod_cost : "";
                        $d['is_cod_enable'] = !empty($row->country) ? (string) $row->country->is_cod_enable : 0;
                        $d['phonecode'] = !empty($row->country) ? $row->country->phonecode : "";
                        array_push($result, $d);
                    }
                    $this->data = $result;
                } else {
                    $this->response_code = 404;
                    $this->message = 'No address found for requested user.';
                }
            } else {
                $this->response_code = 404;
                $this->message = "Address doesn't exist.";
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    private function getUserDefaultAddress($user, $lang = "en", $store = "", $id = "", $order_id = '') {
        $addressQuery = \app\models\ShippingAddresses::find()
                ->where(['user_id' => $user, 'is_deleted' => 0]);
        if (!empty($id)) {
            $addressQuery->andWhere(['shipping_address_id' => $id]);
        } else {
            $addressQuery->andWhere(['is_default' => 1]);
        }
        $address = $addressQuery->one();
        $totalItems = 0;
        $subTotalKw = 0;
        if (!empty($order_id)) {
            $cartDetails = $this->cartDetails($order_id, $lang, $store);
            if (isset($cartDetails['items'])) {
                foreach ($cartDetails['items'] as $item) {
                    $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                    $totalItems += $item['quantity'];
                }
            }
        }
        $result = [];
        if (!empty($address)) {
            $deliveryOptions = !empty($address->country) ? AppHelper::getDeliveryOptions($lang, $address->country_id, $store, $subTotalKw, $totalItems) : [];
            $result = [
                'address_id' => $address->shipping_address_id,
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'area_name' => !empty($address->area) ? (($lang == 'en') ? $address->area->name_en : $address->area->name_ar) : "",
                'governorate_name' => !empty($address->state) ? (($lang == 'en') ? $address->state->name_en : $address->state->name_ar) : "",
                'country_id' => !empty($address->country) ? $address->country_id : "",
                'country_name' => !empty($address->country) ? (($lang == 'en') ? $address->country->name_en : $address->country->name_ar) : "",
                'phonecode' => !empty($address->country) ? $address->country->phonecode : "",
                'block_id' => (string) $address->block_id,
                'block_name' => (!empty($address->block_id)) ? (!empty($address->block) ? (($lang == 'en') ? $address->block->name_en : $address->block->name_ar) : "") : $address->block_name,
                'street' => $address->street,
                'avenue' => (!empty($address->avenue)) ? $address->avenue : "",
                'landmark' => (!empty($address->landmark)) ? $address->landmark : "",
                'flat' => (!empty($address->flat)) ? $address->flat : "",
                'floor' => (!empty($address->floor)) ? $address->floor : "",
                'building' => (!empty($address->building)) ? $address->building : "",
                'addressline_1' => $address->addressline_1,
                'mobile_number' => $address->mobile_number,
                'alt_phone_number' => $address->alt_phone_number,
                'location_type' => $address->location_type,
                'notes' => $address->notes,
                'shipping_cost' => !empty($deliveryOptions[0]) ? $deliveryOptions[0]['price'] : '0',
                'express_shipping_cost' => !empty($deliveryOptions[1]) ? $deliveryOptions[1]['price'] : '0',
                'shipping_cost_kw' => !empty($deliveryOptions[0]) ? $deliveryOptions[0]['price_kw'] : '0',
                'express_shipping_cost_kw' => !empty($deliveryOptions[1]) ? $deliveryOptions[1]['price_kw'] : '0',
                'cod_cost' => !empty($address->country) ? (string) $this->convertPrice($address->country->cod_cost, 82, $store['currency_id']) : "",
                'cod_cost_kw' => !empty($address->country) ? (string) $address->country->cod_cost : "",
                'is_cod_enable' => $address->country->is_cod_enable,
                'vat' => !empty($address->country) ? (string) $address->country->vat : "0",
            ];
        }

        return $result;
    }
    
}
