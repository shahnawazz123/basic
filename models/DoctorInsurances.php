<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_insurances".
 *
 * @property int $doctor_insurance_id
 * @property int $doctor_id
 * @property int $insurance_id
 *
 * @property Doctors $doctor
 * @property Insurances $insurance
 */
class DoctorInsurances extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_insurances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'insurance_id'], 'required'],
            [['doctor_id', 'insurance_id'], 'integer'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctors::className(), 'targetAttribute' => ['doctor_id' => 'doctor_id']],
            [['insurance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Insurances::className(), 'targetAttribute' => ['insurance_id' => 'insurance_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_insurance_id' => 'Doctor Insurance ID',
            'doctor_id' => 'Doctor ID',
            'insurance_id' => 'Insurance ID',
        ];
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

    /**
     * Gets query for [[Insurance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInsurance()
    {
        return $this->hasOne(Insurances::className(), ['insurance_id' => 'insurance_id']);
    }
}
