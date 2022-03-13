<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_appointment_test_results".
 *
 * @property int $doctor_appointment_test_result_id
 * @property int $doctor_appointment_request_id
 * @property int $lab_appointment_test_file_id
 * @property string $created_at
 *
 * @property DoctorAppointmentRequests $doctorAppointmentRequest
 * @property LabAppointmentTestFiles $labAppointmentTestFile
 */
class DoctorAppointmentTestResults extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_appointment_test_results';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_request_id', 'lab_appointment_test_file_id', 'created_at'], 'required'],
            [['doctor_appointment_request_id', 'lab_appointment_test_file_id'], 'integer'],
            [['created_at'], 'safe'],
            [['doctor_appointment_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => DoctorAppointmentRequests::className(), 'targetAttribute' => ['doctor_appointment_request_id' => 'doctor_appointment_request_id']],
            [['lab_appointment_test_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => LabAppointmentTestFiles::className(), 'targetAttribute' => ['lab_appointment_test_file_id' => 'lab_appointment_test_file_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_appointment_test_result_id' => 'Doctor Appointment Test Result ID',
            'doctor_appointment_request_id' => 'Doctor Appointment Request ID',
            'lab_appointment_test_file_id' => 'Lab Appointment Test File ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[DoctorAppointmentRequest]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointmentRequest()
    {
        return $this->hasOne(DoctorAppointmentRequests::className(), ['doctor_appointment_request_id' => 'doctor_appointment_request_id']);
    }

    /**
     * Gets query for [[LabAppointmentTestFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentTestFile()
    {
        return $this->hasOne(LabAppointmentTestFiles::className(), ['lab_appointment_test_file_id' => 'lab_appointment_test_file_id']);
    }
}
