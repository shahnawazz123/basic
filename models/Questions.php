<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property int $question_id
 * @property string $question_en
 * @property string $question_ar
 *
 * @property LabAppointmentAnswers[] $labAppointmentAnswers
 */
class Questions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_en', 'question_ar'], 'required'],
            [['question_en', 'question_ar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'question_en' => 'Question En',
            'question_ar' => 'Question Ar',
        ];
    }

    /**
     * Gets query for [[LabAppointmentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointmentAnswers()
    {
        return $this->hasMany(LabAppointmentAnswers::className(), ['question_id' => 'question_id']);
    }
}
