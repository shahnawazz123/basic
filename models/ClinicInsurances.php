<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinic_insurances".
 *
 * @property int $clinic_insurance_id
 * @property int $insurance_id
 * @property int $clinic_id
 *
 * @property Clinics $clinic
 * @property Insurances $insurance
 */
class ClinicInsurances extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinic_insurances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['insurance_id', 'clinic_id'], 'required'],
            [['insurance_id', 'clinic_id'], 'integer'],
            [['clinic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clinics::className(), 'targetAttribute' => ['clinic_id' => 'clinic_id']],
            [['insurance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Insurances::className(), 'targetAttribute' => ['insurance_id' => 'insurance_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clinic_insurance_id' => 'Clinic Insurance ID',
            'insurance_id' => 'Insurance ID',
            'clinic_id' => 'Clinic ID',
        ];
    }

    /**
     * Gets query for [[Clinic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinic()
    {
        return $this->hasOne(Clinics::className(), ['clinic_id' => 'clinic_id']);
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
