<?php

namespace app\commands;

use app\models\DoctorAppointments;
use app\models\LabAppointments;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ReminderController extends Controller
{
    public function actionIndex()
    {
        date_default_timezone_set('Asia/Kuwait');
        $today = date('Y-m-d h:i:s');
        $doctorAppointmentModel = DoctorAppointments::find()
            ->where([
                'is_call_initiated' => 0,
                'is_completed' => 0,
                'is_cancelled' => 0,
                'not_show' => 0,
            ])
            ->andWhere(['>=', 'appointment_datetime', $today])
            ->andWhere(['<=', 'appointment_datetime', date('Y-m-d h:i:s', strtotime('+ 15 minute'))])
            ->with(['user', 'doctor']);
        $query = $doctorAppointmentModel->all();
        foreach ($query as $row) {

            $user_device_token = $row->user->device_token;
            $title  = "You have an appointment.";
            $msg = "Your scheduled appointment #" . $row['appointment_number'] . " is about to start in 15 mins.";
            $notification = new \app\models\Notifications();
            $notification->title    = $title;
            $notification->message  = $msg;
            $notification->user_id  = $row->user->user_id;
            $notification->target   = "DA";
            $notification->target_id = $row['doctor_appointment_id'];
            $notification->posted_date = date('Y-m-d H:i:s');
            $notification->save(false);
            \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, "DA", $row['doctor_appointment_id'], $title, '', $row['doctor']['name_en'], $row['doctor']['name_ar']);
        }

        $lapAppointments = LabAppointments::find()
            ->where([
                'is_completed' => 0,
                'is_cancelled' => 0,
                'not_show' => 0,
            ])
            ->andWhere(['>=', 'appointment_datetime', $today])
            ->andWhere(['<=', 'appointment_datetime', date('Y-m-d h:i:s', strtotime('+ 15 minute'))])
            ->with(['user', 'lab']);
        $query = $lapAppointments->all();
        foreach ($query as $row) {
            $user_device_token = $row->user->device_token;
            $title  = "You have an appointment.";
            $msg = "Your scheduled appointment #" . $row['appointment_number'] . " is about to start in 15 mins.";
            $notification = new \app\models\Notifications();
            $notification->title    = $title;
            $notification->message  = $msg;
            $notification->user_id  = $row->user->user_id;
            $notification->target   = "LA";
            $notification->target_id = $row['lab_appointment_id'];
            $notification->posted_date = date('Y-m-d H:i:s');
            $notification->save(false);
            \app\helpers\AppHelper::sendPushwoosh($msg, $user_device_token, "LA", $row['lab_appointment_id'], $title, '', $row['lab']['name_en'], $row['lab']['name_ar']);
        }
    }
}
