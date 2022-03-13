<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_symptoms".
 *
 * @property int $doctor_symptom_id
 * @property int $doctor_id
 * @property int $symptom_id
 *
 * @property Symptoms $symptom
 */
class DoctorSymptoms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_symptoms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'symptom_id'], 'required'],
            [['doctor_id', 'symptom_id'], 'integer'],
            [['symptom_id'], 'exist', 'skipOnError' => true, 'targetClass' => Symptoms::className(), 'targetAttribute' => ['symptom_id' => 'symptom_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_symptom_id' => 'Doctor Symptom ID',
            'doctor_id' => 'Doctor ID',
            'symptom_id' => 'Symptom ID',
        ];
    }

    /**
     * Gets query for [[Symptom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSymptoms()
    {
        return $this->hasOne(Symptoms::className(), ['symptom_id' => 'symptom_id']);
    }
}
