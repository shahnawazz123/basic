<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_report_request".
 *
 * @property int $doctor_report_request_id
 * @property int $doctor_appointment_id
 * @property string|null $doctor_request_for
 * @property int $user_id
 * @property string $request_date
 * @property string $status P - Pending, A - Accepted , R - Rejected
 *
 * @property Users $user
 * @property DoctorAppointments $doctorAppointment
 * @property UserReport $userReport
 */
class DoctorReportRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_report_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_id', 'user_id'], 'required'],
            [['doctor_appointment_id', 'user_id'], 'integer'],
            [['doctor_request_for', 'status'], 'string'],
            [['request_date'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['doctor_appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => DoctorAppointments::className(), 'targetAttribute' => ['doctor_appointment_id' => 'doctor_appointment_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_report_request_id' => 'Doctor Report Request ID',
            'doctor_appointment_id' => 'Doctor Appointment ID',
            'doctor_request_for' => 'Doctor Request For',
            'user_id' => 'User ID',
            'request_date' => 'Request Date',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[DoctorAppointment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointment()
    {
        return $this->hasOne(DoctorAppointments::className(), ['doctor_appointment_id' => 'doctor_appointment_id']);
    }

    public function getUserReport()
    {
        return $this->hasOne(UserReport::className(), ['report_id' => 'report_id']);
    }
}
