<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_appointment_requests".
 *
 * @property int $doctor_appointment_request_id
 * @property int|null $doctor_appointment_id
 * @property int $test_id
 * @property string $created_at
 *
 * @property DoctorAppointments $doctorAppointment
 * @property Tests $test
 * @property DoctorAppointmentTestResults[] $doctorAppointmentTestResults
 */
class DoctorAppointmentRequests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_appointment_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_id', 'test_id'], 'integer'],
            [['test_id', 'created_at'], 'required'],
            [['created_at'], 'safe'],
            [['doctor_appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => DoctorAppointments::className(), 'targetAttribute' => ['doctor_appointment_id' => 'doctor_appointment_id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tests::className(), 'targetAttribute' => ['test_id' => 'test_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_appointment_request_id' => 'Doctor Appointment Request ID',
            'doctor_appointment_id' => 'Doctor Appointment ID',
            'test_id' => 'Test ID',
            'created_at' => 'Created At',
        ];
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

    /**
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Tests::className(), ['test_id' => 'test_id']);
    }

    /**
     * Gets query for [[DoctorAppointmentTestResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointmentTestResults()
    {
        return $this->hasMany(DoctorAppointmentTestResults::className(), ['doctor_appointment_request_id' => 'doctor_appointment_request_id']);
    }
}
