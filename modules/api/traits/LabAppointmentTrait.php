<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\traits;

use stdClass;
use app\helpers\AppHelper;
use app\helpers\AppointmentHelper;
use Yii;

trait LabAppointmentTrait
{

    public function actionLabTimeslot($lab_id, $date, $test_id)
    {
        $day = date('l', strtotime($date));
        $model = \app\models\Labs::find()
            ->where(['lab_id' => $lab_id, 'is_active' => 1, 'is_deleted' => 0])
            ->one();
        if (!empty($model)) {
            $price = 0;
            if (!empty($test_id)) {
                $testIds = explode(',', $test_id);
                $tests = \app\models\Tests::find()
                    ->where(['test_id' => $testIds])
                    ->all();
                if (!empty($tests)) {
                    foreach ($tests as $test) {
                        $price += $test->price;
                    }
                }
            }
            $duration = $model->consultation_time_interval;

            $labWorkingDay = \app\models\LabsWorkingDays::find()
                ->where(['lab_id' => $model->lab_id, 'day' => $day])
                ->one();
            $timeslot = [];
            if (!empty($labWorkingDay)) {
                $startTime = strtotime($date . ' ' . $labWorkingDay->lab_start_time);
                $endTime = strtotime($date . ' ' . $labWorkingDay->lab_end_time);

                $interval = $duration * 60;
                $requestDate = date('Y-m-d', strtotime($date));
                for ($i = $startTime; $i < $endTime; $i += $interval) {
                    $time = date('H:i:s', $i);
                    $slotDate = date('Y-m-d', $i);
                    $isBooked = AppointmentHelper::isLabBooked($time, $lab_id, $slotDate, $duration, $model->max_booking_per_lot);
                    $bookingCountQuery = \app\models\LabAppointments::find()
                        ->select([
                            'lab_appointments.*',
                            'ROUND(time_to_sec((TIMEDIFF("' . date('Y-m-d H:i:s') . '", lab_appointments.created_at))) / 60) as minutes'
                        ])
                        ->where(['=', 'appointment_datetime', $slotDate . ' ' . $time])
                        ->andWhere(['lab_id' => $lab_id])
                        ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0])
                        ->andHaving('(is_paid = 1) OR (is_paid = 0 AND minutes < 10)');

                    $bookingCount = $bookingCountQuery->count();
                    $t['time'] = $time;
                    $t['is_booked'] = $isBooked['found'];
                    $t['regular_price'] = $price;
                    $t['final_price'] = $price;
                    $t['max_booking_per_lot'] = $model->max_booking_per_lot;
                    $t['booking_count'] = $bookingCount;
                    $t['remaining_count'] = ($model->max_booking_per_lot - $bookingCount);
                    if (strtotime($slotDate) <= strtotime($requestDate)) {
                        array_push($timeslot, $t);
                    }
                }
            }

            $this->data = [
                'id' => 1,
                'slot_day' => $day,
                'slot_date' => $date,
                'duration' => $duration,
                'timeslots' => $timeslot,
            ];
        } else {
            $this->response_code = 404;
            $this->message = 'Lab does not exist';
        }
        return $this->response();
    }

    public function actionAddLabAppointment($lang = "en")
    {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $lab = \app\models\Labs::find()
                ->where(['lab_id' => $request['lab_id'], 'is_active' => 1, 'is_deleted' => 0])
                ->one();
            if (empty($lab)) {
                $this->response_code = 404;
                $this->message = 'Lab does not exist';
                return $this->response();
            }
            $duration = $lab->consultation_time_interval;
            $max_booking_per_lot = $lab->max_booking_per_lot;
            $isBooked = AppointmentHelper::isLabBooked($request['appointment_time'], $request['lab_id'], $request['appointment_date'], $duration, $max_booking_per_lot);
            if ($isBooked['found'] == '1') {
                $this->response_code = 408;
                $this->message = 'Requested slot is already booked';
                $this->data = new stdClass();
                return $this->response();
            }

            $requestBookingStartDateTime = $request['appointment_date'] . ' ' . $request['appointment_time'];
            $day = date('l', strtotime($request['appointment_date']));
            $requestDate = new \DateTime($requestBookingStartDateTime);
            $today = new \DateTime(date("Y-m-d H:i:s"));
            $today->setTimezone(new \DateTimeZone('Asia/Kuwait'));
            $currentDateTime = new \DateTime($today->format('Y-m-d H:i:s'));
            if ($requestDate < $currentDateTime) {
                $this->response_code = 405;
                $this->message = 'Requested datetime is invalid';
                $this->data = new stdClass();
                return $this->response();
            }
            $slotAvailable = 0;
            $startTime = strtotime($request['appointment_date'] . ' ' . $lab->start_time);
            $endTime = strtotime($request['appointment_date'] . ' ' . $lab->end_time);
            $timeslot = [];
            $lab_tests = [];
            $lab_address = [];
            $interval = $duration * 60;
            //making timeslot using min interval
            for ($i = $startTime; $i <= $endTime; $i += $interval) {
                $time = date('H:i:s', $i);
                $dt = date('Y-m-d', $i);
                if (strtotime($dt) <= strtotime($request['appointment_date'])) {
                    array_push($timeslot, $dt . ' ' . $time);
                }
            }
            $labSlot = \app\models\LabsWorkingDays::find()
                ->where(['lab_id' => $request['lab_id'], 'day' => $day])
                ->one();
            if (!empty($labSlot)) {
                $slotAvailable = 0;
                $startTime = strtotime($request['appointment_date'] . ' ' . $labSlot->lab_start_time);
                $endTime = strtotime($request['appointment_date'] . ' ' . $labSlot->lab_end_time);
                $timeslot = [];
                $interval = $duration * 60;
                //making timeslot using min interval
                for ($i = $startTime; $i <= $endTime; $i += $interval) {
                    $time = date('H:i:s', $i);
                    $dt = date('Y-m-d', $i);
                    if (strtotime($dt) <= strtotime($request['appointment_date'])) {
                        array_push($timeslot, $dt . ' ' . $time);
                    }
                }
                if (!empty($timeslot)) {
                    //added min to start time to find out end time
                    $dateTime = new \DateTime($requestBookingStartDateTime);
                    $dateTime->modify('+' . $duration . ' minutes');
                    $requestBookingEndDateTime = $dateTime->format('Y-m-d H:i:s');
                    //change datetime format
                    $time1 = new \DateTime($requestBookingStartDateTime);
                    $time2 = new \DateTime($requestBookingEndDateTime);
                    //calculate min time n max time to compare request time is valid or not
                    $first = new \DateTime($timeslot[0]);
                    $lastTime = new \DateTime(end($timeslot));
                    //added min to last time to find out maxlast time
                    $lastTime->modify('+' . $duration . ' minutes');
                    $lastDateTime = $lastTime->format('Y-m-d H:i:s');
                    //change datetime format
                    $last = new \DateTime($lastDateTime);
                    //if request time >= first time slot and less then last time slot 
                    //also booking end time is less then or equal to max last time
                    //then request slot is valid
                    if (($time1 >= $first && $time1 <= $last) && $time2 <= $last) {
                        $slotAvailable = 1;
                    }
                    if ($slotAvailable == 0) {
                        $this->response_code = 404;
                        $this->message = 'Requested timeslot does not exist';
                        $this->data = new stdClass();
                        return $this->response();
                    }
                } else {
                    $this->response_code = 407;
                    $this->message = 'Timeslot creation failed due to unexpected error';
                    $this->data = new stdClass();
                    return $this->response();
                }
            } else {
                $this->response_code = 404;
                $this->message = 'No available timeslot for appointment';
                $this->data = new stdClass();
                return $this->response();
            }
            //checking duplicate
            $exist = 1;
            $requestDatetime = $request['appointment_date'] . ' ' . $request['appointment_time'];
            //
            $booking = \app\models\LabAppointments::find()
                ->where(['appointment_datetime' => $requestDatetime])
                ->andWhere(['lab_id' => $request['lab_id']])
                ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($booking)) {
                $bookingCount = \app\models\LabAppointments::find()
                    ->select([
                        'lab_appointments.*',
                        'ROUND(time_to_sec((TIMEDIFF("' . date('Y-m-d H:i:s') . '", lab_appointments.created_at))) / 60) as minutes'
                    ])
                    ->where(['=', 'appointment_datetime', $requestDatetime])
                    ->andWhere(['lab_id' => $request['lab_id']])
                    ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0])
                    ->andHaving('(is_paid = 1) OR (is_paid = 0 AND minutes < 10)')
                    ->count();

                $createDate = new \DateTime($booking->created_at, new \DateTimeZone(date_default_timezone_get()));
                $createDate->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                $createTime = new \DateTime($createDate->format('Y-m-d H:i:s'));
                $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
                $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                $timeFromCreate = $createTime->diff(new \DateTime($currentTime->format('Y-m-d H:i:s')));
                $minutes = $timeFromCreate->days * 24 * 60;
                $minutes += $timeFromCreate->h * 60;
                $minutes += $timeFromCreate->i;
                if ($minutes > 10) {
                    $exist = 0;
                } elseif ($minutes < 10) {
                    if ($bookingCount >= $max_booking_per_lot) {
                        $exist = 1;
                    } else {
                        $exist = 0;
                    }
                }
            } else {
                $exist = 0;
            }
            $accepted_payment_method = $lab->accepted_payment_method;
            $paymentTypes = AppHelper::paymentTypes($lang, $accepted_payment_method);

            if ($exist == 0) {
                $appointmentPrice = 0;
                $test_id = $request['test_id'];
                if (!empty($test_id)) {
                    $testIds = explode(',', $test_id);
                    $tests = \app\models\Tests::find()
                        ->where(['test_id' => $testIds])
                        ->all();
                    if (!empty($tests)) {
                        foreach ($tests as $test) {
                            $appointmentPrice += $test->price;
                        }
                    }
                }
                $subtotal = $appointmentPrice;
                // if ($request['type'] == 'H') {
                //     if (!empty($lab->home_test_charge)) {
                //         $appointmentPrice += $lab->home_test_charge;
                //     }
                // }
                $model = new \app\models\LabAppointments();
                $model->user_id = $request['user_id'];
                $model->name = $request['name'];
                $model->email = $request['email'];
                $model->phone_number = $request['phone_number'];
                $model->type = $request['type'];
                $model->appointment_datetime = $requestDatetime;
                $model->lab_id = $lab->lab_id;
                $model->kid_id = !empty($request['kid_id']) ? $request['kid_id'] : '';
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->is_cancelled = 0;
                $model->is_paid = 0;
                $model->duration = $duration;
                $discount = 0;
                $discount_price = 0;
                $model->lab_amount = $appointmentPrice;
                $model->discount = $discount;
                $model->discount_price = $discount_price;
                $model->home_service_price =  $request['type'] == 'H' ?  $lab->home_test_charge : 0;
                $model->sub_total = $subtotal; //$appointmentPrice;
                $model->amount = $appointmentPrice +  $model->home_service_price - $discount_price;
                $model->sample_collection_address = !empty($request['sample_collection_address']) ? $request['sample_collection_address'] : "";
                $model->admin_commission = (!empty($model->lab)) ? $model->lab->admin_commission : 0;
                if ($model->isNewRecord) {
                    $model->appointment_number = \app\helpers\AppHelper::getNextBookingNumber('lab');
                }
                if ($model->save()) {
                    if (!empty($tests)) {
                        foreach ($tests as $test) {
                            $labTests = new \app\models\LabAppointmentTests();
                            $labTests->lab_appointment_id = $model->lab_appointment_id;
                            $labTests->test_id = $test->test_id;
                            $labTests->save();
                        }
                    }
                    $kids = \app\models\Kids::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0])
                        ->all();
                    $kidsList = [];
                    if (!empty($kids)) {

                        foreach ($kids as $kid) {
                            $age = '';
                            if ($kid->dob != null) {
                                $dateOfBirth = $kid->dob;
                                $today = date("Y-m-d");
                                $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                $age = $diff->format('%y');
                            }
                            $k = [
                                'id' => $kid->kid_id,
                                'name' => $kid->{"name_" . $lang},
                                'civil_id' => $kid->civil_id,
                                'dob' => $kid->dob,
                                'age' => $age,
                                'gender' => $kid->gender,
                                'blood_group' => $kid->blood_group,
                                'relation' => $kid->relation,
                            ];
                            array_push($kidsList, $k);
                        }
                    }

                    if ($lang == 'ar') {
                        $lab_image = (!empty($model->lab->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->lab->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    } else {
                        $lab_image = (!empty($model->lab->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->lab->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                    }

                    if (!empty($model->labAppointmentTests)) {
                        foreach ($model->labAppointmentTests as $lab_row) {
                            array_push($lab_tests, $lab_row->test->{'name_' . $lang});
                        }
                    }
                    $country_name = (isset($model->lab)) ? ((!empty($model->lab->area)) ? $model->lab->area->state->country->name_en : '') : '';

                    $lab_address = [
                        'country_name' => $country_name,
                        'governorate' => (isset($model->lab->governorate)) ? $model->lab->governorate->name_en : '',
                        'area' => (isset($model->lab->area)) ? $model->lab->area->name_en : '',
                        'block' => (isset($model->lab->block)) ? $model->lab->block : '',
                        'street' => (isset($model->lab->street)) ? $model->lab->street : '',
                        'building' => (isset($model->lab->building)) ? $model->lab->building : '',
                        'latlon' => (isset($model->lab->latlona)) ? $model->lab->latlon : '',
                    ];

                    $userAddresses = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0, 'is_default' => 1])
                        ->one();
                    $user_address = [];
                    if (!empty($userAddresses)) {
                        $user_address = [

                            'first_name' => $userAddresses->first_name,
                            'shipping_address_id' => $userAddresses->shipping_address_id,
                            'area_id' => $userAddresses->area_id,
                            'area_name' => !empty($userAddresses->area) ? (($lang == 'en') ? $userAddresses->area->name_en : $userAddresses->area->name_ar) : "",
                            'governorate_id' => !empty($userAddresses->state) ? $userAddresses->state_id : "",
                            'governorate_name' => !empty($userAddresses->state) ? (($lang == 'en') ? $userAddresses->state->name_en : $userAddresses->state->name_ar) : "",
                            'country_id' => !empty($userAddresses->country_id) ? $userAddresses->country_id : "",
                            'country_name' => !empty($userAddresses->country_id) ? (($lang == 'en') ? $userAddresses->country->name_en : $userAddresses->country->name_ar) : "",
                            'block_id' => (!empty($userAddresses->block_id)) ? $userAddresses->block_id : '',
                            'block_name' => (!empty($userAddresses->block_id)) ? \app\helpers\AppHelper::getBlockNameById($userAddresses->block_id, $lang) : '',
                            'avenue' => $userAddresses->avenue,
                            'street' => $userAddresses->street,
                            'building_number' => (string) $userAddresses->building,
                            'flat_number' => (string) $userAddresses->flat,
                            'floor_number' => (string) $userAddresses->floor,
                            'addressline_1' => $userAddresses->addressline_1,
                            'mobile_number' => $userAddresses->mobile_number,
                            'alt_phone_number' => $userAddresses->alt_phone_number,
                            'location_type' => $userAddresses->location_type,
                            'notes' => $userAddresses->notes,
                            'is_default' => ($userAddresses->is_default == '0') ? "No" : "Yes",
                            'phonecode' => !empty($userAddresses->country) ? $userAddresses->country->phonecode : "",
                        ];
                    } else {
                        $user_address = new \stdClass();
                    }

                    $this->message = 'Lab appointment successfully saved.';
                    $this->data = [
                        'appointment_details' => [
                            'id' => $model->lab_appointment_id,
                            'appointment_number' => $model->appointment_number,
                            'name' => $model->name,
                            'email' => $model->email,
                            'appointment_datetime' => $model->appointment_datetime,
                            'duration' => $model->duration,
                            'user_id' => $model->user_id,
                            'user_address' => ($request['type'] == 'H') ? $user_address : new \stdClass(),
                            'type' => $model->type,
                            'lab_id' => $model->lab_id,
                            'lab_name' => $model->lab->{"name_" . $lang},
                            'home_test_charge' => (float)$model->home_service_price,
                            'lab_image' => $lab_image,
                            'lab_address' => ($country_name != '') ? $lab_address : new \stdClass(),
                            'lab_tests' => $lab_tests,
                            'created_at' => $model->created_at,
                            'updated_at' => $model->updated_at,
                            'kid_id' => $model->kid_id,
                            'is_cancelled' => $model->is_cancelled,
                            'is_paid' => $model->is_paid,
                            'discount' => $model->discount_price,
                            'sub_total' => $model->sub_total,
                            'total_amount' => $model->amount,
                            'sample_collection_address' => $model->sample_collection_address,
                        ],
                        'kids_list' => $kidsList,
                        'payment_types' => $paymentTypes,
                    ];
                } else {
                    $this->response_code = 500;
                    $this->data = $model->errors;
                }
            } else {
                $this->response_code = 406;
                $this->message = 'Appointment is not available for this timeslot';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionLabAppointmentPayment($lang = 'en')
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\LabAppointments::find()
                ->where(['user_id' => $request['user_id'], 'lab_appointment_id' => $request['lab_appointment_id'], 'is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($model)) {
                $src = 'src_card';
                $paymode = 'CC';
                if ($request['paymode'] == 'K') {
                    $src = 'src_kw.knet';
                    $paymode = 'K';
                } else if ($request['paymode'] == 'W') {
                    $src = '';
                    $paymode = 'W';
                    $model->paymode = $paymode;
                    $model->is_paid = '1';
                }
                $model->payment_initiate_time = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                $model->has_gone_payment = 1;
                $model->kid_id = !empty($request['kid_id']) ? $request['kid_id'] : "";
                $model->user_address_id = !empty($request['address_id']) ? $request['address_id'] : "";
                $model->save(false);

                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $promoFor = $model->promo_for;
                $promoId = $model->promotion_id;
                $discount = $model->discount;
                $subTotal = 0;
                $discountPrice = 0;
                if (isset($model->sub_total)) {
                    //$subTotal = $model->sub_total;
                    $subTotal = $model->lab_amount;
                    if (isset($promoFor) && !empty($promoFor)) {
                        if ($promoFor == 'L') {
                            if (empty($promotion->promotionLabs)) {
                                $discountPrice += ((($subTotal * $discount) / 100) * 1);
                            } else {
                                $hasPromoLab = 1;
                                $promoLabs = \app\models\PromotionLabs::find()
                                    ->where(['promotion_id' => $promoId, 'lab_id' => $model->lab_id])
                                    ->one();
                                if (!empty($promoLabs)) {
                                    $discountPrice += ((($subTotal * $discount) / 100) * 1);
                                    $hasPromotionFor[] = $model->lab_id;
                                } else {
                                    //$discountPrice = 0;
                                }
                            }
                        }
                    } else {
                        $discountPrice += ((($subTotal * $discount) / 100) * 1);
                    }
                }
                $total = ($model->sub_total + ($model->type == "H" ? (float) $model->home_service_price : 0)  - $discountPrice);
                // $total = ($model->lab_amount  - $discountPrice);
                $model->amount = $total;
                $model->discount_price = $discountPrice;
                $model->save(false);
                $lab_tests = [];
                $transactionNumber = uniqid('LA-');

                //$paymentResponse = \app\helpers\PaymentHelper::tapPayment('LA', $model->lab_appointment_id, $model->user_id, $model->amount, $transactionNumber, '', $lang, $src, $paymode);
                if ($request['paymode'] == 'C' || $request['paymode'] == 'W') {
                    $src = '';
                    $paymode = $request['paymode'];
                    $model->is_paid = '1';
                    $model->save(false);

                    $paymentModel = \app\models\Payment::find()
                        ->where(['type_id' => $model->lab_appointment_id, 'type' => 'LA'])
                        ->one();
                    if (empty($paymentModel)) {
                        $paymentModel = new \app\models\Payment();
                    }
                    $trackID = date("YmdHis") . time() . rand();
                    $paymentModel->transaction_id = $transactionNumber;
                    $paymentModel->type_id = $model->lab_appointment_id;
                    $paymentModel->type = 'LA';
                    $paymentModel->paymode = $request['paymode'];
                    $paymentModel->gross_amount = $model->amount;
                    $paymentModel->net_amount = $model->amount;
                    $paymentModel->TrackID = $trackID;
                    $paymentModel->currency_code = "KWD";
                    $paymentModel->result = 'CAPTURED';
                    $paymentModel->payment_date = date("Y-m-d H:i:s");
                    $paymentModel->save(false);

                    $paymentResponse = [
                        'status' => 200,
                        'url' => "",
                        'success' => "",
                        'error' => "",
                        'gateway_response' => ""
                    ];

                    Yii::$app->mailer->compose('@app/mail/lab-appointment', [
                        'model' => $model,
                    ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->user->email)
                        ->setSubject('Lab Appointment Booked ')
                        ->send();
                } else if ($request['paymode'] == 'K' || $request['paymode'] == 'CC') {
                    $myfatorah = \app\helpers\PaymentHelper::payThroughMyfatoorahExecutePayment('LA', $model->lab_appointment_id, $model->user_id, $model->amount, '', $lang, $paymode, 'KWD', $transactionNumber, '');
                    $paymentResponse = [
                        'status' => 200,
                        'url' => $myfatorah['payment_url'],
                        'success' => $myfatorah['success_url'],
                        'error' => $myfatorah['error_url'],
                        'gateway_response' => $myfatorah['gateway_response']
                    ];
                }


                if ($lang == 'ar') {
                    $lab_image = (!empty($model->lab->image_ar)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->lab->image_ar) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                } else {
                    $lab_image = (!empty($model->lab->image_en)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->lab->image_en) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                }

                if (!empty($model->labAppointmentTests)) {
                    foreach ($model->labAppointmentTests as $lab_row) {
                        array_push($lab_tests, $lab_row->test->{'name_' . $lang});
                    }
                }

                $country_name = (isset($model->lab)) ? ((!empty($model->lab->area)) ? $model->lab->area->state->country->name_en : '') : '';
                $lab_address = [
                    'country_name' => $country_name,
                    'governorate' => (isset($model->lab->governorate)) ? $model->lab->governorate->name_en : '',
                    'area' => (isset($model->lab->area)) ? $model->lab->area->name_en : '',
                    'block' => (isset($model->lab->block)) ? $model->lab->block : '',
                    'street' => (isset($model->lab->street)) ? $model->lab->street : '',
                    'building' => (isset($model->lab->building)) ? $model->lab->building : '',
                    'latlon' => (isset($model->lab->latlona)) ? $model->lab->latlon : '',
                ];


                $user_address = new \stdClass();
                if ($model->type == 'H') {
                    $userAddresses = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $model->user_id, 'shipping_address_id' => $model->user_address_id, 'is_deleted' => 0])
                        ->one();

                    if (!empty($userAddresses)) {
                        $user_address = [
                            'first_name' => $userAddresses->first_name,
                            'shipping_address_id' => $userAddresses->shipping_address_id,
                            'area_id' => $userAddresses->area_id,
                            'area_name' => !empty($userAddresses->area) ? (($lang == 'en') ? $userAddresses->area->name_en : $userAddresses->area->name_ar) : "",
                            'governorate_id' => !empty($userAddresses->state) ? $userAddresses->state_id : "",
                            'governorate_name' => !empty($userAddresses->state) ? (($lang == 'en') ? $userAddresses->state->name_en : $userAddresses->state->name_ar) : "",
                            'country_id' => !empty($userAddresses->country_id) ? $userAddresses->country_id : "",
                            'country_name' => !empty($userAddresses->country_id) ? (($lang == 'en') ? $userAddresses->country->name_en : $userAddresses->country->name_ar) : "",
                            'block_id' => (!empty($userAddresses->block_id)) ? $userAddresses->block_id : '',
                            'block_name' => (!empty($userAddresses->block_id)) ? \app\helpers\AppHelper::getBlockNameById($userAddresses->block_id, $lang) : '',
                            'avenue' => $userAddresses->avenue,
                            'street' => $userAddresses->street,
                            'building_number' => (string) $userAddresses->building,
                            'flat_number' => (string) $userAddresses->flat,
                            'floor_number' => (string) $userAddresses->floor,
                            'addressline_1' => $userAddresses->addressline_1,
                            'mobile_number' => $userAddresses->mobile_number,
                            'alt_phone_number' => $userAddresses->alt_phone_number,
                            'location_type' => $userAddresses->location_type,
                            'notes' => $userAddresses->notes,
                            'is_default' => ($userAddresses->is_default == '0') ? "No" : "Yes",
                            'phonecode' => !empty($userAddresses->country) ? $userAddresses->country->phonecode : "",
                        ];
                    }
                }
                $kids = \app\models\Kids::find()
                    ->where(['kid_id' => $model->kid_id, 'is_deleted' => 0])
                    ->one();
                $kid_data = new \stdClass();
                if (!empty($kids)) {
                    $age = '';
                    if ($kids->dob != null) {
                        $dateOfBirth = $kids->dob;
                        $today = date("Y-m-d");
                        $diff = date_diff(date_create($dateOfBirth), date_create($today));
                        $age = $diff->format('%y');
                    }
                    $kid_data = [
                        'id' => $kids->kid_id,
                        'name' => $kids->{"name_" . $lang},
                        'civil_id' => $kids->civil_id,
                        'dob' => $kids->dob,
                        'age' => $age,
                        'gender' => $kids->gender,
                        'blood_group' => $kids->blood_group,
                        'relation' => $kids->relation,
                    ];
                }

                $this->message = 'Payment information updated.';
                $this->data = [
                    'appointment_details' => [
                        'id' => $model->lab_appointment_id,
                        'appointment_number' => $model->appointment_number,
                        'name' => $model->name,
                        'email' => $model->email,
                        'appointment_datetime' => $model->appointment_datetime,
                        'type' => $model->type,
                        'duration' => $model->duration,
                        'user_id' => $model->user_id,
                        'user_address' => ($model->type == 'H') ? $user_address : new \stdClass(),
                        'lab_id' => $model->lab_id,
                        'lab_name' => $model->lab->{"name_" . $lang},
                        'home_test_charge' => (float)$model->lab->home_test_charge,
                        'lab_image' => $lab_image,
                        'lab_address' => ($country_name != '') ? $lab_address : new \stdClass(),
                        'lab_tests' => $lab_tests,
                        'created_at' => $model->created_at,
                        'updated_at' => $model->updated_at,
                        'kid_id' => $model->kid_id,
                        'kid_details' => $kid_data,
                        'is_cancelled' => $model->is_cancelled,
                        'is_paid' => $model->is_paid,
                        'discount' => $model->discount,
                        'discount_price' => $model->discount_price,
                        'sub_total' => $model->sub_total,
                        'total_amount' => $model->amount,
                        'sample_collection_address' => $model->sample_collection_address,
                    ],

                    'payment_url' => (!empty($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['url'] : '',
                    'success_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['success'] : "",
                    'error_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['error'] : "",
                    'paymode' => $paymode,

                ];
            } else {
                $this->response_code = 404;
                $this->message = "Appointment doesn't exist.";
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDeleteLabAppointmentSlot()
    {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $user_id = $request['user_id'];
            $lab_appointment_id = $request['lab_appointment_id'];
            $booking = \app\models\LabAppointments::find()
                ->where(['lab_appointment_id' => $lab_appointment_id, 'user_id' => $user_id])
                ->andWhere(['is_paid' => 0, 'is_cancelled' => 0, 'is_deleted' => 0])
                ->one();
            if (!empty($booking)) {
                if ($booking->delete()) {
                    $this->message = 'Slot successfully cleared';
                }
            } else {
                $this->response_code = 404;
                $this->message = 'Appointment does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }
}
