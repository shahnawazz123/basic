<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinic_working_days".
 *
 * @property int $clinic_working_day_id
 * @property int $clinic_id
 * @property string $day
 * @property string $end_time
 * @property string $start_time
 */
class ClinicWorkingDays extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinic_working_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clinic`_`id', 'day', 'end_time', 'start_time'], 'required'],
            [['clinic_id'], 'integer'],
            [['end_time', 'start_time'], 'safe'],
            [['day'], 'string', 'max' => 100],
            ['end_time', 'compare',  'compareAttribute' => 'start_date', 'operator' => '>', 'enableClientValidation' =>false,'message' => 'Start Date must be less than End Date'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clinic_working_day_id' => 'Clinic Working Day ID',
            'clinic_id' => 'Clinic ID',
            'day' => 'Day',
            'end_time' => 'End Time',
            'start_time' => 'Start Time',
        ];
    }
}
