<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_insurances".
 *
 * @property int $lab_insurance_id
 * @property int $lab_id
 * @property int $insurance_id
 *
 * @property Labs $lab
 * @property Insurances $insurance
 */
class LabInsurances extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_insurances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_id', 'insurance_id'], 'required'],
            [['lab_id', 'insurance_id'], 'integer'],
            [['lab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Labs::className(), 'targetAttribute' => ['lab_id' => 'lab_id']],
            [['insurance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Insurances::className(), 'targetAttribute' => ['insurance_id' => 'insurance_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_insurance_id' => 'Lab Insurance ID',
            'lab_id' => 'Lab ID',
            'insurance_id' => 'Insurance ID',
        ];
    }

    /**
     * Gets query for [[Lab]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLab()
    {
        return $this->hasOne(Labs::className(), ['lab_id' => 'lab_id']);
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
