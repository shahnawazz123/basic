<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

use Yii;

/**
 * Description of AppointmentHelper
 *
 * @author akram
 */
class AppointmentHelper
{

    static function isBooked($time, $doctor_id, $date, $requestDuration) {
        $bookingQuery = \app\models\DoctorAppointments::find()
                ->where(['>=', 'DATE(appointment_datetime)', $date])
                ->andWhere(['doctor_id' => $doctor_id])
                ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0]);
        $booking = $bookingQuery->asArray()
                ->all();
        if (!empty($booking)) {
            $found = 0;
            $booking_id = "";
            foreach ($booking as $book) {
                $duration = $book['duration'];
                $bookStartDateTime = $book['appointment_datetime'];
                $dateTime = new \DateTime($bookStartDateTime);
                $dateTime->modify('+' . $duration . ' minutes');
                $bookiEndDateTime = $dateTime->format('Y-m-d H:i:s');
                $slotDatetime = $date . ' ' . $time;
                $time1 = new \DateTime($bookStartDateTime);
                $time2 = new \DateTime($bookiEndDateTime);
                $time3 = new \DateTime($slotDatetime);
                //
                $t3t1Diff = $time3->diff(new \DateTime($time1->format('Y-m-d H:i:s')));
                $t3t1Minutes = $t3t1Diff->days * 24 * 60;
                $t3t1Minutes += $t3t1Diff->h * 60;
                $t3t1Minutes += $t3t1Diff->i;
                //
                if ($book['is_paid'] == 0) {
                    $createDate = new \DateTime($book['created_at'], new \DateTimeZone(date_default_timezone_get()));
                    $createDate->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                    $createTime = new \DateTime($createDate->format('Y-m-d H:i:s'));
                    //
                    $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
                    $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                    $timeFromCreate = $createTime->diff(new \DateTime($currentTime->format('Y-m-d H:i:s')));
                    $minutes = $timeFromCreate->days * 24 * 60;
                    $minutes += $timeFromCreate->h * 60;
                    $minutes += $timeFromCreate->i;
                    if (($minutes < 10 && $time3 >= $time1 && $time3 < $time2) || ($time3 < $time1 && $time3 < $time2 && $t3t1Minutes < $requestDuration)) {
                        $found = 1;
                        $booking_id = $book['doctor_appointment_id'];
                        break;
                    }
                } elseif ($book['is_paid'] == 1) {
                    if (($time3 >= $time1 && $time3 < $time2) || ($time3 < $time1 && $time3 < $time2 && $t3t1Minutes < $requestDuration)) {
                        $found = 1;
                        $booking_id = $book['doctor_appointment_id'];
                        break;
                    }
                }
            }
            return [
                'found' => (string) $found,
                'booking_id' => (string) $booking_id,
            ];
        } else {
            return [
                'found' => '0',
                'booking_id' => ''
            ];
        }
    }

    static function isLabBooked($time, $lab_id, $date, $requestDuration, $max_booking_per_lot) {
        $bookingQuery = \app\models\LabAppointments::find()
                ->where(['>=', 'DATE(appointment_datetime)', $date])
                ->andWhere(['lab_id' => $lab_id])
                ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0]);
        $booking = $bookingQuery->asArray()
                ->all();
        if (!empty($booking)) {
            $found = 0;
            $booking_id = "";
            foreach ($booking as $book) {
                $duration = $book['duration'];
                $bookStartDateTime = $book['appointment_datetime'];
                $dateTime = new \DateTime($bookStartDateTime);
                $dateTime->modify('+' . $duration . ' minutes');
                $bookiEndDateTime = $dateTime->format('Y-m-d H:i:s');
                $slotDatetime = $date . ' ' . $time;
                $time1 = new \DateTime($bookStartDateTime);
                $time2 = new \DateTime($bookiEndDateTime);
                $time3 = new \DateTime($slotDatetime);
                //
                $t3t1Diff = $time3->diff(new \DateTime($time1->format('Y-m-d H:i:s')));
                $t3t1Minutes = $t3t1Diff->days * 24 * 60;
                $t3t1Minutes += $t3t1Diff->h * 60;
                $t3t1Minutes += $t3t1Diff->i;
                //
                $bookingCount = \app\models\LabAppointments::find()
                         ->select([
                            'lab_appointments.*',
                            'ROUND(time_to_sec((TIMEDIFF("' . date('Y-m-d H:i:s') . '", lab_appointments.created_at))) / 60) as minutes'
                        ])
                        ->where(['=', 'appointment_datetime', $bookStartDateTime])
                        ->andWhere(['lab_id' => $lab_id])
                        ->andWhere(['is_cancelled' => 0, 'is_deleted' => 0])
                        ->andHaving('(is_paid = 1) OR (is_paid = 0 AND minutes < 10)')
                        ->count();
                //
                if ($book['is_paid'] == 0) {
                    $createDate = new \DateTime($book['created_at'], new \DateTimeZone(date_default_timezone_get()));
                    $createDate->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                    $createTime = new \DateTime($createDate->format('Y-m-d H:i:s'));
                    //
                    $currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
                    $currentTime->setTimezone(new \DateTimeZone('Asia/Kuwait'));
                    $timeFromCreate = $createTime->diff(new \DateTime($currentTime->format('Y-m-d H:i:s')));
                    $minutes = $timeFromCreate->days * 24 * 60;
                    $minutes += $timeFromCreate->h * 60;
                    $minutes += $timeFromCreate->i;
                    if (($minutes < 10 && $time3 >= $time1 && $time3 < $time2) || ($time3 < $time1 && $time3 < $time2 && $t3t1Minutes < $requestDuration)) {
                        if ($bookingCount >= $max_booking_per_lot) {
                            $found = 1;
                            $booking_id = $book['lab_appointment_id'];
                            break;
                        }
                    }
                } elseif ($book['is_paid'] == 1) {
                    if (($time3 >= $time1 && $time3 < $time2 && $bookingCount >= $max_booking_per_lot) || ($time3 < $time1 && $time3 < $time2 && $t3t1Minutes < $requestDuration)) {
                        if ($bookingCount >= $max_booking_per_lot) {
                            $found = 1;
                            $booking_id = $book['lab_appointment_id'];
                            break;
                        }
                    }
                }
            }
            return [
                'found' => (string) $found,
                'booking_id' => (string) $booking_id,
            ];
        } else {
            return [
                'found' => '0',
                'booking_id' => ''
            ];
        }
    }

}
