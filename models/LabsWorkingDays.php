<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "labs_working_days".
 *
 * @property int $lab_working_day_id
 * @property int $lab_id
 * @property string $day
 * @property string $lab_end_time
 * @property string $lab_start_time
 */
class LabsWorkingDays extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'labs_working_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_id', 'day', 'lab_end_time', 'lab_start_time'], 'required'],
            [['lab_id'], 'integer'],
            [['lab_end_time', 'lab_start_time'], 'safe'],
            [['day'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_working_day_id' => 'Lab Working Day ID',
            'lab_id' => 'Lab ID',
            'day' => 'Day',
            'lab_end_time' => 'End Time',
            'lab_start_time' => 'Start Time',
        ];
    }
}
