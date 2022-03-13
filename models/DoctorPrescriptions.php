<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_prescriptions".
 *
 * @property int $doctor_appointment_prescription_id
 * @property int $doctor_appointment_id
 * @property int $total_usage
 * @property int $referred_pharmacy_id
 * @property int $is_deleted
 * @property int $is_active
 * @property string $created_at
 * 
 * @property Pharmacy $pharmacy
 * @property Appointment $appointment
 */
class DoctorPrescriptions extends \yii\db\ActiveRecord
{
    public $qty;
    public $selection;
    public $instruction;
    public $name_en;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_prescriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referred_pharmacy_id'], 'required'],
            [['doctor_appointment_id', 'total_usage', 'referred_pharmacy_id', 'is_deleted', 'is_active'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_appointment_prescription_id' => 'Prescription ID',
            'doctor_appointment_id' => 'Doctor Appointment ID',
            'total_usage' => 'Total Usage',
            'referred_pharmacy_id' => 'Pharmacy',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
        ];
    }

    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'referred_pharmacy_id']);
    }

    public function getAppointment()
    {
        return $this->hasOne(DoctorAppointments::className(), ['doctor_appointment_id' => 'doctor_appointment_id']);
    }
}
