<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;
use Yii;
/**
 * Description of PaymentHelper
 *
 * @author akram
 */
class PaymentHelper
{

    static function tapPayment($type, $type_id, $user_id, $total_amt, $transactionNumber, $store, $lang, $src, $paymode) 
    {
        $user = \app\models\Users::findOne($user_id);
        $total = number_format($total_amt, 3, '.', '');
        $TranTrackid = date('YmdHis');
        $successUrl = \Yii::$app->urlManager->createAbsoluteUrl('site/success');
        $errorUrl = \Yii::$app->urlManager->createAbsoluteUrl('site/failed');
        $returnUrl = Yii::$app->urlManager->createAbsoluteUrl(['site/tap-response', 'type' => $type, 'type_id' => $type_id, 'total' => $total]);
        //
        $paymentModel = new \app\models\Payment();
        $paymentModel->type_id = $type_id;
        $paymentModel->type = $type;
        $paymentModel->gross_amount = $total;
        $paymentModel->net_amount = $total;
        $paymentModel->paymode = $paymode;
        $paymentModel->currency_code = 'KWD';
        $paymentModel->TrackID = $TranTrackid;
        $paymentModel->udf1 = $transactionNumber;
        $paymentModel->udf2 = $user_id;
        $paymentModel->udf3 = $store;
        $paymentModel->udf4 = $lang;
        $paymentModel->udf5 = $total;
        if($paymode == 'W')
        {
           $paymentModel->result = 'CAPTURED'; 
        }
        
        if (!$paymentModel->save()) {
            die(json_encode($paymentModel->errors));
        }

        if($paymode == 'W')
        {
            return [
                'status' => 200,
                'message' => 'Success',
                'url' =>  "",
                'success' => "",
                'error' => "",
                'payment_id' => $paymentModel->payment_id,
            ];
        }
        //
        $data = [
            "amount" => $total,
            "currency" => "KWD",
            "statement_descriptor" => "3EYADAT",
            "livemode" => "false",
            "description" => "3EYADAT",
            "metadata" => [
                "type" => "$type",
                "type_id" => "$type_id",
                'udf1' => $paymentModel->payment_id,
                'udf2' => $user_id,
            ],
            'reference' => [
                'transaction' => $transactionNumber,
                'order' => $type_id,
            ],
            'customer' => [
                'first_name' => $user->first_name,
                'email' => $user->email,
                'phone' => [
                    'country_code' => '965',
                    'number' => $user->phone
                ]
            ],
            'source' => [
                'id' => $src,
            ],
            'post' => [
                'url' => $returnUrl,
            ],
            'redirect' => [
                'url' => $returnUrl,
            ],
        ];
        $_data = str_replace('\/', '/', json_encode($data));
        $headers = array(
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($_data),
            "authorization: Bearer sk_test_O1IDmadnBKh7A6MqG32CUu4F", 
            //"authorization: Bearer sk_test_lBfNL4SvWXEPqr5RU3oY6aA7",
        );
        $url = 'https://api.tap.company/v2/charges';
        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $res = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $result = json_decode($res, true);
            $paymentUrl = isset($result['transaction']['url']) ? $result['transaction']['url'] : '';
            return [
                'status' => 200,
                'message' => 'Success',
                'url' => ($paymentModel->gross_amount > 0) ? $paymentUrl : "",
                'success' => $successUrl,
                'error' => $errorUrl,
                'payment_id' => $paymentModel->payment_id,
            ];
        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => $ex->getMessage(),
                'paymentUrl' => ""
            ];
        }
    }

    public static function payThroughMyfatoorahExecutePayment($type,$type_id, $user_id, $totalKw, $storeId, $lang, $paymode, $currency,$transactionNumber,$store) {
        $user = \app\models\Users::findOne($user_id);
        $paymentMethodId = self::getPaymentMethodId($paymode);
        /*$order = \app\models\Orders::find()
                ->where(['order_id' => $orderId, 'user_id' => $userId])
                ->one();*/
        $referenceId = date("YmdHis") . time() . rand();
        $TranTrackid = date('YmdHis');
        $total = number_format($totalKw, 3, '.', '');
        $trackID = date("YmdHis") . time() . rand();

        $responseUrl = Yii::$app->urlManager->createAbsoluteUrl(['site/fathorah-tap-response', 'type' => $type, 'type_id' => $type_id, 'total' => $total]);

        $returnUrl = $responseUrl;
        $errorUrl = $responseUrl;

        $customerName = $user->first_name . ' ' . $user->last_name;
        $customerEmail = $user->email;
        $customerPhone = self::removeGccCode($user->phone);
       
        if (empty($customerPhone)) {
            $customerPhone = '12345678';
        }

        $udf1 = $transactionNumber;
        $udf2 = $user_id;
        $udf3 = '';
        $udf4 = $lang;
        $udf5 = $total;

        if (strlen($customerPhone) > 11) {
            $customerPhone = substr($customerPhone, 0, 11);
        }

        $_data = [
            'PaymentMethodId' => $paymentMethodId,
            'CustomerName' => $customerName,
            'DisplayCurrencyIso' => $currency,
            'MobileCountryCode' => '+965',
            'CustomerMobile' => $customerPhone,
            'CustomerEmail' => $customerEmail,
            'InvoiceValue' => $total,
            'CallBackUrl' => $returnUrl,
            'ErrorUrl' => $errorUrl,
            'Language' => $lang,
            'CustomerReference' => $trackID,
            'CustomerCivilId' => '',
            'UserDefinedField' => '',
            'ExpireDate' => '',
            'CustomerAddress' => [
                'Block' => "",
                'Street' => "",
                'HouseBuildingNo' => '',
                'Address' => "",
                'AddressInstructions' => "",
            ],
            'InvoiceItems' => [
                [
                    'ItemName' => 'Order Total',
                    'Quantity' => 1,
                    'UnitPrice' => $total,
                ]
            ]
        ];
        
        $token = Yii::$app->params['myfatoorahExecuteToken'];
        $host = Yii::$app->params['myfatoorahUrl'];
        $basURL = "https://$host.myfatoorah.com";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$basURL/v2/ExecutePayment",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($_data),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ),
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        //debugPrint($response);die;
        $err = curl_error($curl);
        curl_close($curl);
        //
        $result = json_decode($response);
       //echo "<pre>"; print_r($result);die;
        //echo $result->Data->CustomerReference;;die;
        if (!empty($result) && $result->IsSuccess == true) {
            $paymentUrl = $result->Data->PaymentURL;
            $invoiceId = $result->Data->InvoiceId;
            $CustomerReference = $result->Data->CustomerReference;
            $paymentModel = new \app\models\Payment();
            $paymentModel->type_id = $type_id;
            $paymentModel->type = $type;
            $paymentModel->gross_amount = $total;
            $paymentModel->net_amount = $total;
            $paymentModel->paymode = $paymode;
            $paymentModel->currency_code = 'KWD';
            $paymentModel->TrackID = $TranTrackid;
            $paymentModel->ref = $CustomerReference;
            $paymentModel->udf1 = $transactionNumber;
            $paymentModel->udf2 = $user_id;
            $paymentModel->udf3 = $store;
            $paymentModel->udf4 = $lang;
            $paymentModel->udf5 = $total;
            if($paymode == 'W' || $paymode == 'C')
            {
               $paymentModel->result = 'CAPTURED'; 
            }
            
            if (!$paymentModel->save()) {
                die(json_encode($paymentModel->errors));
            }

            if($paymode == 'W' || $paymode == 'C')
            {
                return [
                    'status' => 200,
                    'message' => 'Success',
                    'url' =>  "",
                    'success' => "",
                    'error' => "",
                    'payment_id' => $paymentModel->payment_id,
                ];
            }
            //
            $successUrl = Yii::$app->urlManager->createAbsoluteUrl('site/success');
            $errorUrl = Yii::$app->urlManager->createAbsoluteUrl('site/failed');
            return $result = [
                'payment_url' => $paymentUrl,
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
                'gateway_response' => new \stdClass(),
            ];
        } else {
            return $result = [
                'msg' => "Invalid response from gateway",
                'payment_url' => '',
                'success_url' => Yii::$app->urlManager->createAbsoluteUrl('site/success', 'https'),
                'error_url' => Yii::$app->urlManager->createAbsoluteUrl('site/failed', 'https'),
                'gateway_response' => $response
            ];
        }
    }

    public static function removeGccCode($phone) {
        $phone_codes = [
            "965", "966", "971", "972", "968", "973", "00965", "00966", "00971", "00972", "00968", "00973", "+965", "+966", "+971", "+972", "+968", "+973"
        ];
        $first_five_char = substr($phone, 0, 5);

        foreach ($phone_codes as $phone_code) {
            if (strpos($first_five_char, $phone_code) !== false) {
                $replaced_phone = str_replace($phone_code, "", $phone);
                return $replaced_phone;
            }
        }
        return $phone;
    }

    static function getPaymentMethodId($paymode) {
        $paytypes = [
            'K' => '1',
            'CC' => '2',
            'AE' => '3',
            'S' => '4',
            'B' => '5',
            'NP' => '7',
            'MD' => '6',
            'KF' => '10',
            'AP' => '11',
            'AF' => '13',
            'STC' => '14',
            'UAECC' => '8',
            'C' => '9',
        ];

        return $paytypes[$paymode];
    }

}
