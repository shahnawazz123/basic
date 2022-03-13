<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_appointments".
 *
 * @property int $doctor_appointment_id
 * @property string $name
 * @property string|null $email
 * @property string $phone_number
 * @property string|null $consultation_type
 * @property float $consultation_fees
 * @property string $appointment_datetime
 * @property int $user_id
 * @property int $doctor_id
 * @property string|null $prescription_file
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property float $admin_commission
 * @property int|null $kid_id
 * @property int|null $need_translator
 *
 * @property DoctorAppointmentRequests[] $doctorAppointmentRequests
 * @property Doctors $doctor
 * @property Users $user
 * @property Kids $kid
 * @property Payment $payment
 */
class DoctorAppointments extends \yii\db\ActiveRecord
{
    public $reports;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_appointments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone_number', 'consultation_fees', 'appointment_datetime', 'user_id', 'doctor_id', 'created_at', 'updated_at'], 'required'],
            [['consultation_type'], 'string'],
            [['consultation_fees', 'admin_commission'], 'number'],
            [['appointment_datetime', 'created_at', 'updated_at'], 'safe'],
            [['user_id', 'doctor_id', 'is_deleted', 'kid_id', 'translator_id'], 'integer'],
            [['name', 'email', 'phone_number', 'prescription_file'], 'string', 'max' => 100],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctors::className(), 'targetAttribute' => ['doctor_id' => 'doctor_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['kid_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kids::className(), 'targetAttribute' => ['kid_id' => 'kid_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_appointment_id' => 'Doctor Appointment ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'consultation_type' => 'Consultation Type',
            'consultation_fees' => 'Consultation Fees',
            'appointment_datetime' => 'Appointment Datetime',
            'user_id' => 'User',
            'doctor_id' => 'Doctor',
            'prescription_file' => 'Prescription File',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'kid_id' => 'Appointment Booked for',
            'amount' => 'Total Amount',
            'need_translator' => 'Need translator?',
            'translator_id' => 'translator',
        ];
    }

    /**
     * Gets query for [[DoctorAppointmentRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointmentRequests()
    {
        return $this->hasMany(DoctorAppointmentRequests::className(), ['doctor_appointment_id' => 'doctor_appointment_id']);
    }

    /**
     * Gets query for [[Doctor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctors::className(), ['doctor_id' => 'doctor_id']);
    }
    public function getTranslator()
    {
        return $this->hasOne(Translator::className(), ['translator_id' => 'translator_id']);
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
     * Gets query for [[Kid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKid()
    {
        return $this->hasOne(Kids::className(), ['kid_id' => 'kid_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['type_id' => 'doctor_appointment_id']);
    }

    public function getRequestReports()
    {
        return $this->hasMany(DoctorReportRequest::className(), ['doctor_appointment_id' => 'doctor_appointment_id']);
    }

    public function getReports()
    {
        return $this->hasMany(DoctorAssignedReportRequest::className(), ['doctor_appointment_id' => 'doctor_appointment_id']);
    }

    public function getPrescriptionList($doctor_appointment_id)
    {
        return  \app\models\DoctorPrescriptions::find()
            ->where(['doctor_appointment_id' => $doctor_appointment_id, 'is_deleted' => 0])
            ->orderBy('doctor_prescriptions.doctor_appointment_prescription_id');
        //->asArray();
        //return $this->hasMany(DoctorPrescriptions::className(), ['doctor_appointment_id' => 'doctor_appointment_id','doctor_prescritions.is_deleted'=>"0"]);
    }
}
