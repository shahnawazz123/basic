<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_appointment_medicines".
 *
 * @property int $doctor_appointment_medicine_id
 * @property int $doctor_appointment_prescription_id
 * @property int $product_id
 * @property int $qty
 * @property string|null $instruction
 * 
 * 
 * @property Product $product
 */
class DoctorAppointmentMedicines extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_appointment_medicines';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_prescription_id', 'product_id'], 'required'],
            [['doctor_appointment_prescription_id', 'product_id', 'qty'], 'integer'],
            [['instruction'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_appointment_medicine_id' => 'Doctor Appointment Medicine ID',
            'doctor_appointment_prescription_id' => 'Doctor Appointment Prescription ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'instruction' => 'Instruction',
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
}
