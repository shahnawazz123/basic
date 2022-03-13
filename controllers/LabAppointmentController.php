<?php

namespace app\controllers;

use Yii;
use app\models\LabAppointments;
use app\models\LabAppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

/**
 * LabAppointmentController implements the CRUD actions for LabAppointments model.
 */
class LabAppointmentController extends Controller
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
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'delete'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_LAB
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all LabAppointments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LabAppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new LabAppointments();
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            print_r($request);
            die;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LabAppointments model.
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

    /**
     * Creates a new LabAppointments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed

      public function actionCreate() {
      $model = new LabAppointments();

      if ($model->load(Yii::$app->request->post())) {
      $request = Yii::$app->request->bodyParams;
      if ($model->save()) {
      Yii::$app->session->setFlash('success', 'LabAppointments successfully added');
      return $this->redirect(['index']);
      } else {
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
     * Updates an existing LabAppointments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found

      public function actionUpdate($id) {
      $model = $this->findModel($id);
      if ($model->load(Yii::$app->request->post())) {
      $request = Yii::$app->request->bodyParams;
      if ($model->save()) {
      Yii::$app->session->setFlash('success', 'LabAppointments successfully updated');
      return $this->redirect(['index']);
      } else {
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
     * Deletes an existing LabAppointments model.
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
        Yii::$app->session->setFlash('success', 'LabAppointments successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionCalender($lab_id = "")
    {
        return $this->render('calender');
    }

    public function actionCalenderFetchEvent($lab_id = "", $start, $end)
    {
        $begin = new \DateTime($start);
        $end_date = date('Y-m-d', strtotime($end . '+1 day'));
        $endDt = new \DateTime($end_date);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $endDt);
        $eventObjects = [];
        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            $result = $this->backendTimeslots($lab_id, $date,);
            //debugPrint($result);
            if (!empty($result) && is_array($result['data'])) {
                //debugPrint($result['data']); exit;
                foreach ($result['data']['timeslots'] as $slot) {
                    $endTime = date('H:i:s', strtotime($slot['date'] . ' ' . $slot['time'] . '+' . $slot['duration'] . ' minutes'));
                    $endDate = date('Y-m-d', strtotime($slot['date'] . ' ' . $slot['time'] . '+' . $slot['duration'] . ' minutes'));
                    $bookingNumber = "";
                    $bookingUrl = '';
                    if ($slot['booking_count'] > 0) {
                        $bookingNumber = 'Total ' . $slot['booking_count'] . ' appointment';
                        $bookingUrl = \yii\helpers\Url::to(['lab-appointment/index', 'LabAppointmentSearch[appointment_datetime]=' => $slot['date'] . ' ' . $slot['time']]);
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
                            $bookingUrl = ($bookingUrl) ? $bookingUrl : "";
                        }
                        $d = [
                            'title' => ($slot['booking_count'] > 0) ? $bookingNumber : date('h:i A', strtotime($slot['time'])),
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

    public function backendTimeslots($lab_id, $date)
    {
        $day = date('l', strtotime($date));
        $query = \app\models\Labs::find()
            ->where(['lab_id' => $lab_id]);
        $model = $query->one();
        if (!empty($model)) {
            $duration = $model->consultation_time_interval;

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
                    $isBooked = \app\helpers\AppointmentHelper::isLabBooked($time, $lab_id, $slotDate, $duration, $model->max_booking_per_lot);
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
                    $t['booking_id'] = $isBooked['booking_id'];
                    $t['date'] = $slotDate;
                    $t['duration'] = $duration;
                    $t['max_booking_per_lot'] = $model->max_booking_per_lot;
                    $t['booking_count'] = $bookingCount;
                    $t['remaining_count'] = ($model->max_booking_per_lot - $bookingCount);
                    if (strtotime($slotDate) <= strtotime($requestDate)) {
                        array_push($timeslot, $t);
                    }
                }
                $data['data'] = [
                    'id' => 1,
                    'slot_day' => $day,
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

                    $user_device_token = $model->user->device_token;
                    $title  = $model->lab->name_en;
                    $full_name = $model->user->first_name;
                    $msg    =  "Hi " . $full_name . " ! your lab test is cancelled. please contact " . $model->lab->name_en;
                    if (!empty($user_device_token)) {
                        $this->sendpush($id, $msg, $title, $user_device_token . "LA");
                    }

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
            $title  = "Lab test completed";
            $full_name = $model->user->first_name;
            $msg    =  "Hi " . $full_name . " ! test is completed.";
            if (!empty($user_device_token)) {
                $this->sendpush($id, $msg, $title, $user_device_token, "LA");
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
            $title  = "Lab Appointment Missed";
            $full_name = $model->user->first_name;
            $msg    =  "Hi " . $full_name . " ! You have missed you lab appointment.";
            if (!empty($user_device_token)) {
                $this->sendpush($id, $msg, $title, $user_device_token, "LA");
            }

            Yii::$app->session->setFlash('success', 'Appointment set to no show  successfully');
            return $this->redirect(['view?id=' . $id]);
        }
    }

    /**
     * Finds the LabAppointments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LabAppointments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LabAppointments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddLabReport()
    {
        if (\Yii::$app->session['_eyadatAuth'] == 1 || \Yii::$app->session['_eyadatAuth'] == 4) {
            $request = Yii::$app->request->bodyParams;
            if (empty($request)) {
                return json_encode(['status' => 200, 'msg' => 'Error processing the request!!!']);
            }
            $lab_appointment_id = $request['lab_appointment_id'];
            $model = LabAppointments::find()->where(['lab_appointment_id' => $lab_appointment_id])->one();

            if (!empty($model)) {
                $model->report_title_en = $request['report_title_en'];
                $model->report_title_ar = $request['report_title_ar'];
                $model->report_upload_date = date('Y-m-d H:i:s');
                $model->uploaded_report = $request['pdf_file'];
                $model->save();

                $user_device_token = $model->user->device_token;
                $full_name =  $model->user->first_name;
                $title  = "Uploaded Lab Report";
                $msg = "Hi " .   $full_name . "! Your lab reports are uploaded.";
                if (!empty($user_device_token)) {
                    $this->sendpush($lab_appointment_id, $msg, $title, $user_device_token, "LA");
                }

                return json_encode(['status' => 200, 'msg' => 'Lab report uploaded successfully']);
            } else {
                return json_encode(['status' => 500, 'msg' => 'Lab appointment not found']);
            }
        }
    }


    public function sendpush($id, $msg, $title = "", $user_device_token = "", $target = "LA")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            $model = $this->findModel($id);
            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Lab appointment does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);

                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = $model->user->user_id;
                $notification->target   = $target;
                $notification->target_id = $model->lab_appointment_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, $target, $model->lab_appointment_id, $title, '', $model->lab->name_en, $model->lab->name_ar);

                /*return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);*/
            }
        }
    }
}
