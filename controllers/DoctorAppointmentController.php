<?php

namespace app\controllers;

require(__DIR__ . '/../vendor/autoload.php');

use Yii;
use app\models\DoctorAppointments;
use app\models\DoctorAppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

/**
 * DoctorAppointmentController implements the CRUD actions for DoctorAppointments model.
 */
class DoctorAppointmentController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'delete'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN,
                            UserIdentity::ROLE_DOCTOR,
                            UserIdentity::ROLE_CLINIC,
                            UserIdentity::ROLE_TRANSLATOR,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_TRANSLATOR,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DoctorAppointments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DoctorAppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single DoctorAppointments model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionVideoCall($id)
    {
        $model = $this->findModel($id);
        $room_name = $model->appointment_number;

        if (Yii::$app->session['_eyadatAuth'] == 8) {
            $identity  = "translator";
        } else {
            $identity  = $model->doctor->name_en . $model->doctor_id;
        }


        $today_date = strtotime(date('Y-m-d H:i:s'));
        $appointment_datetime = strtotime($model->appointment_datetime);

        if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date && $model->consultation_type == 'V') {
            // Required for all Twilio access tokens
            $twilioAccountSid = "AC9e4942d195c3a3a90a3ee809db045772";
            $twilioApiKey     = "SKb20b4cf1ee0135a3a71afd94e00e66a9";
            $twilioApiSecret  = "nNDafbCSUovDqncWDIwuoxKOUcPbePfx";

            // A unique identifier for this user
            $identity = $identity;
            // The specific Room we'll allow the user to access
            $roomName = $room_name;

            // Create access token, which we will serialize and send to the client
            $token = new AccessToken($twilioAccountSid, $twilioApiKey, $twilioApiSecret, 3600, $identity);

            // Create Video grant
            $videoGrant = new VideoGrant();
            $videoGrant->setRoom($roomName);
            $token->addGrant($videoGrant);

            // $user_device_token = $model->user->device_token;
            // $title  = "Video Consultation";
            // $msg    = "Hi ". $model->user->first_name + $model->user->last_name . "! Doctor has joined the video consultation. Please Join";
            // if (!empty($user_device_token)) {
            //     $this->sendpush($id, $msg, $title, $user_device_token);
            // }

            return $this->render('video-call', [
                'model' => $this->findModel($id),
                'identity' => $identity,
                'roomName' => $room_name,
                'token' => $token,
            ]);
        } else {
            return $this->redirect(['view?id=' . $model->doctor_appointment_id]);
        }
    }
    public function actionVideoCallTranslator($id)
    {
        $model = $this->findModel($id);
        $room_name = $model->appointment_number;
        $identity  = rand(1000, 2000);

        $today_date = strtotime(date('Y-m-d H:i:s'));
        $appointment_datetime = strtotime($model->appointment_datetime);

        if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date && $model->consultation_type == 'V') {
            // Required for all Twilio access tokens
            $twilioAccountSid = "AC9e4942d195c3a3a90a3ee809db045772";
            $twilioApiKey     = "SKb20b4cf1ee0135a3a71afd94e00e66a9";
            $twilioApiSecret  = "nNDafbCSUovDqncWDIwuoxKOUcPbePfx";

            // A unique identifier for this user
            $identity = $identity;
            // The specific Room we'll allow the user to access
            $roomName = $room_name;

            // Create access token, which we will serialize and send to the client
            $token = new AccessToken($twilioAccountSid, $twilioApiKey, $twilioApiSecret, 3600, $identity);


            // Create Video grant
            $videoGrant = new VideoGrant();
            $videoGrant->setRoom($roomName);
            $token->addGrant($videoGrant);

            // $user_device_token = $model->user->device_token;
            // $title  = "Video Consultation";
            // $msg    = "Your video consultation appointment will begin in 5 minutes.";
            // if (!empty($user_device_token)) {
            //     $this->sendpush($id, $msg, $title, $user_device_token);
            // }

            return $this->render('video-call', [
                'model' => $this->findModel($id),
                'identity' => $identity,
                'roomName' => $room_name,
                'token' => $token,
            ]);
        } else {
            return $this->redirect(['view?id=' . $model->doctor_appointment_id]);
        }
    }
    public function actionAddTranslatorToAppointment($appointement_id, $translator_id)
    {
        $model = DoctorAppointments::find()
            ->where(['doctor_appointment_id' => $appointement_id])
            ->one();
        $model->translator_id = $translator_id;
        if ($model->save(false)) {
            return json_encode(['status' => 200, 'msg' => 'Translator Assigned to Appointment, Successfully.']);
        } else {
            return json_encode(['status' => 201, 'msg' => 'Translator Not Assigned to Appointment.']);
        }
    }
    public function actionDoctorCallInitaited($id, $act)
    {
        $model = $this->findModel($id);
        $full_name =  $model->user->first_name ;
        if ($act == 1) {
            $model->is_call_initiated = 1;
            $model->save(false);

            $user_device_token = $model->user->device_token;
            $title  = "Join Video Consultation";
            $msg = "Hi " . $full_name . "! Your scheduled appointment #" . $model->appointment_number . " has started. Please join the call now.";
            if (!empty($user_device_token)) {
                $this->sendpush($id, $msg, $title, $user_device_token);
            }
            return '1';
        } elseif ($act == 2) {
            $model->is_call_initiated = 2;
            $model->save(false);
            return '2';
        }
    }

    /**
     * Creates a new DoctorAppointments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed

      public function actionCreate()
      {
      $model = new DoctorAppointments();

      if ($model->load(Yii::$app->request->post())) {
      $request = Yii::$app->request->bodyParams;
      if($model->save()){
      Yii::$app->session->setFlash('success', 'DoctorAppointments successfully added');
      return $this->redirect(['index']);
      }else{
      return $this->render('create', [
      'model' => $model,
      ]);
      }

      }
      return $this->render('create', [
      'model' => $model,
      ]);
      } */
    /**
     * Updates an existing DoctorAppointments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found

      public function actionUpdate($id)
      {
      $model = $this->findModel($id);
      if ($model->load(Yii::$app->request->post())) {
      $request = Yii::$app->request->bodyParams;
      if($model->save()){
      Yii::$app->session->setFlash('success', 'DoctorAppointments successfully updated');
      return $this->redirect(['index']);
      }else{
      return $this->render('update', [
      'model' => $model,
      ]);
      }
      }

      return $this->render('update', [
      'model' => $model,
      ]);
      } */

    /**
     * Deletes an existing DoctorAppointments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        Yii::$app->session->setFlash('success', 'DoctorAppointments successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionCalender($doctor_id = "")
    {
        return $this->render('calender');
    }

    public function actionCalenderFetchEvent($doctor_id = "", $type = "I", $start, $end)
    {
        $begin = new \DateTime($start);
        $end_date = date('Y-m-d', strtotime($end . '+1 day'));
        $endDt = new \DateTime($end_date);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $endDt);
        $eventObjects = [];
        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            $result = $this->backendTimeslots($doctor_id, $date, $type);
            if (!empty($result) && is_array($result['data'])) {
                //debugPrint($result['data']); exit;
                foreach ($result['data']['timeslots'] as $slot) {
                    $endTime = date('H:i:s', strtotime($slot['date'] . ' ' . $slot['time'] . '+' . $slot['duration'] . ' minutes'));
                    $endDate = date('Y-m-d', strtotime($slot['date'] . ' ' . $slot['time'] . '+' . $slot['duration'] . ' minutes'));
                    $bookingNumber = "";
                    $bookingUrl = '';
                    if ($slot['booking_id'] != "") {
                        $booking = DoctorAppointments::findOne($slot['booking_id']);
                        $bookingNumber = 'Doctor Appointment#' . $booking->doctor_appointment_id;
                        $bookingUrl = \yii\helpers\Url::to(['doctor-appointment/view', 'id' => $booking->doctor_appointment_id]);
                    }
                    if (strtotime($date) == strtotime($slot['date'])) {
                        $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
                        $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                        $currentDatetime = new \DateTime($currentTime->format('Y-m-d H:i:s'));
                        $startDatetime = new \DateTime($slot['date'] . ' ' . $slot['time']);

                        $txtColor = '#FFFFFF';
                        if ($startDatetime >= $currentDatetime) {
                            $bgColor = ($slot['is_booked'] == 1) ? "#fcb200" : "#09b62d";
                        } else {
                            $bgColor = ($slot['is_booked'] == 1) ? "#fcb200" : "#ededed";
                            $txtColor = ($slot['is_booked'] == 1) ? "#FFFFFF" : "#444";
                            $bookingUrl = ($slot['is_booked'] == 1) ? $bookingUrl : "";
                        }
                        $d = [
                            'title' => ($slot['booking_id'] != "") ? $bookingNumber : date('h:i A', strtotime($slot['time'])),
                            'start' => $slot['date'] . 'T' . $slot['time'],
                            'end' => $endDate . 'T' . $endTime,
                            'backgroundColor' => $bgColor,
                            'url' => $bookingUrl,
                            'textColor' => $txtColor,
                        ];
                        array_push($eventObjects, $d);
                    }
                }
            }
        }
        //debugPrint($eventObjects);exit;
        echo json_encode($eventObjects);
        exit;
    }

    public function backendTimeslots($doctor_id, $date, $consultation_type)
    {
        $day = date('l', strtotime($date));
        $query = \app\models\DoctorWorkingDays::find()
            ->join('LEFT JOIN', 'doctors', 'doctor_working_days.doctor_id = doctors.doctor_id')
            ->where(['doctors.doctor_id' => $doctor_id, 'day' => $day]);
        $model = $query->one();
        if (!empty($model)) {
            $duration = ($consultation_type == 'I') ? $model->doctor->consultation_time_offline : $model->doctor->consultation_time_online;

            if (!empty($model)) {
                $startTime = strtotime($date . ' ' . $model->start_time);
                $endTime = strtotime($date . ' ' . $model->end_time);
                $timeslot = [];
                $interval = $duration * 60;

                $requestDate = date('Y-m-d', strtotime($date));
                //currendays slot
                for ($i = $startTime; $i <= $endTime; $i += $interval) {
                    $time = date('H:i:s', $i);
                    $slotDate = date('Y-m-d', $i);
                    //echo $slotDate;
                    $isBooked = \app\helpers\AppointmentHelper::isBooked($time, $doctor_id, $slotDate, $duration);
                    $t['time'] = $time;
                    $t['is_booked'] = $isBooked['found'];
                    $t['booking_id'] = $isBooked['booking_id'];
                    $t['date'] = $slotDate;
                    $t['duration'] = $duration;
                    if (strtotime($slotDate) <= strtotime($requestDate)) {
                        array_push($timeslot, $t);
                    }
                }
                $data['data'] = [
                    'id' => $model->doctor_working_day_id,
                    'slot_day' => $model->day,
                    'slot_date' => $date,
                    'timeslots' => $timeslot,
                ];

                return $data;
            }
        } else {
            return null;
        }
    }

    public function actionCancelBooking($id)
    {
        $model = $this->findModel($id);
        if ($model->is_cancelled == 1) {
            Yii::$app->session->setFlash('error', 'Booking already canceled');
            return $this->redirect(['index']);
        } else {
            $datetime = $model->appointment_datetime;
            $bookingDatetime = new \DateTime($datetime);
            $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
            $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
            $interval = $currentTime->diff($bookingDatetime);
            $minutes = $interval->days * 24 * 60;
            $minutes += $interval->h * 60;
            $minutes += $interval->i;
            if ($model->is_cancelled == 0 && $minutes > \Yii::$app->params['allowed_cancel_minutes'] && $model->is_paid == 1) {
                $isCancelable = 1;
            } else {
                $isCancelable = 0;
            }
            if ($isCancelable == 1) {
                $model->is_cancelled = 1;
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Booking canceled successfully');
                    return $this->redirect(['index']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Can\'t cancel booking now');
                return $this->redirect(['index']);
            }
        }
    }
    public function actionComplete($id)
    {
        $model = $this->findModel($id);
        if ($model->is_completed == 0) {
            $model->is_completed = '1';
        } else {
            $model->is_completed = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {
            return json_encode($model->errors);
        }
    }
    public function actionCompleteUrl($id)
    {
        $model = $this->findModel($id);
        if ($model->is_completed == 0) {
            $model->is_completed = '1';
        }
        if ($model->save(false)) {
            $user_device_token = $model->user->device_token;
            $title  = "Your Appointment #" . $model->appointment_number . " is completed";
            $full_name = $model->user->first_name;
            $msg    =  "Please tap here to view more details.";
            if (!empty($user_device_token)) {
                $this->sendpush($id, $msg, $title, $user_device_token);
            }

            Yii::$app->session->setFlash('success', 'Appointment completed successfully');
            return $this->redirect(['view?id=' . $id]);
        }
    }
    public function actionNotShowUrl($id)
    {
        $model = $this->findModel($id);
        if ($model->not_show == 0) {
            $model->not_show = '1';
        }
        if ($model->save(false)) {
            $user_device_token = $model->user->device_token;
            $title  = "Oops! You missed an appointment";
            $full_name = $model->user->first_name;
            $msg    = "Your appointment #" . $model->appointment_number . " status has been changed to no show.";
            if (!empty($user_device_token)) {
                $this->sendpush($id, $msg, $title, $user_device_token);
            }

            Yii::$app->session->setFlash('success', 'Appointment set to no show  successfully');
            return $this->redirect(['view?id=' . $id]);
        }
    }
    /**
     * Finds the DoctorAppointments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DoctorAppointments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DoctorAppointments::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionReportRequest($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $report_requests = explode(',', $request['DoctorAppointments']['reports']);
            //print_r($report_requests);die;
            // echo "<pre>";print_r($request);die;
            if (!empty($report_requests)) {
                foreach ($report_requests as $report) {
                    $modelRequest = new \app\models\DoctorReportRequest();
                    $modelRequest->doctor_appointment_id = $id;
                    $modelRequest->doctor_request_for = $report;
                    $modelRequest->user_id = $model->user_id;
                    $modelRequest->save(false);
                }
                $user_device_token = $model->user->device_token;
                $full_name =  $model->user->first_name;
                $title  = "Medical report request for appointment #" . $model->appointment_number;
                $msg = "Your doctor has requested medical report. Please tap to submit now.";
                if (!empty($user_device_token)) {
                    $this->sendpush($id, $msg, $title, $user_device_token, "RR");
                }
            }

            Yii::$app->session->setFlash('success', 'Report request successfully submitted');
            return $this->redirect(['view?id=' . $id]);
        }

        return $this->render('report_request', [
            'model' => $model,
        ]);
    }


    public function actionAddDoctorReport()
    {
        if (\Yii::$app->session['_eyadatAuth'] == 1 || \Yii::$app->session['_eyadatAuth'] == 3) {
            $request = Yii::$app->request->bodyParams;
            if (empty($request)) {
                return json_encode(['status' => 200, 'msg' => 'Error processing the request!!!']);
            }
            $doctor_appointment_id = $request['doctor_appointment_id'];
            $model = DoctorAppointments::find()->where(['doctor_appointment_id' => $doctor_appointment_id])->one();

            if (!empty($model)) {
                $model->report_title_en = $request['report_title_en'];
                $model->report_title_ar = $request['report_title_ar'];
                $model->report_upload_date = date('Y-m-d H:i:s');
                $model->uploaded_report = $request['pdf_file'];
                $model->save();

                $user_device_token = $model->user->device_token;
                $full_name =  $model->user->first_name;
                $title  = "New report uploaded.";
                $msg = "A new report has been uploaded for appointment #" .  $model->appointment_number . ". Tap here to see more.";
                if (!empty($user_device_token)) {
                    $this->sendpush($doctor_appointment_id, $msg, $title, $user_device_token, "DR");
                }

                return json_encode(['status' => 200, 'msg' => 'Doctor report uploaded successfully']);
            } else {
                return json_encode(['status' => 500, 'msg' => 'Doctor appointment not found']);
            }
        }
    }

    public function actionTest()
    {
        $model = \app\models\DoctorAppointments::findOne(15);

        return $this->render('doctor-appointment', [
            'model' => $model,
        ]);
    }

    public function sendpush($id, $msg, $title = "", $user_device_token = "", $target = "DA")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Doctor appointment does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);

                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $model->user->user_id;
                $notification->target   = $target;
                $notification->target_id = $model->doctor_appointment_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, $target, $model->doctor_appointment_id, $title, '', $model->doctor->name_en, $model->doctor->name_ar);

                /*return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);*/
            }
        }
    }

    public function actionTestPush()
    {
        $device_token = "21056e116062e328269924da3a674f33c5a7573d9dc34bd9320da4af57c6d247";
        $title  = "Video Appointment Alert";
        $msg    = "You appointment is scheduled to begin in 10 minutes. ";
        if (!empty($device_token)) {
            echo \app\helpers\AppHelper::sendTestPushwoosh($msg, $device_token, "DA", 10, $title, '', 'vasim', 'vasim');
        }
    }
}
