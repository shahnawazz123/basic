<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_working_days".
 *
 * @property int $doctor_working_day_id
 * @property int $doctor_id
 * @property string $day
 *
 * @property Doctors $doctor
 */
class DoctorWorkingDays extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doctor_working_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'day'], 'required'],
            [['doctor_id'], 'integer'],
            [['day'], 'string', 'max' => 100],
            // [['end_time'], 'compare', 'compareAttribute' => "start_time", 'operator' => '>'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctors::className(), 'targetAttribute' => ['doctor_id' => 'doctor_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'doctor_working_day_id' => 'Doctor Working Day ID',
            'doctor_id' => 'Doctor ID',
            'day' => 'Day',
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
}
