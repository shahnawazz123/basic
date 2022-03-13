<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lab_appointment_answers".
 *
 * @property int $lab_appointment_answer_id
 * @property int $lab_appointment_id
 * @property int $question_id
 * @property string $answer_en
 * @property string $answer_ar
 *
 * @property Questions $question
 * @property LabAppointments $labAppointment
 */
class LabAppointmentAnswers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lab_appointment_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_appointment_id', 'question_id', 'answer_en', 'answer_ar'], 'required'],
            [['lab_appointment_id', 'question_id'], 'integer'],
            [['answer_en', 'answer_ar'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::className(), 'targetAttribute' => ['question_id' => 'question_id']],
            [['lab_appointment_id'], 'exist', 'skipOnError' => true, 'targetClass' => LabAppointments::className(), 'targetAttribute' => ['lab_appointment_id' => 'lab_appointment_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_appointment_answer_id' => 'Lab Appointment Answer ID',
            'lab_appointment_id' => 'Lab Appointment ID',
            'question_id' => 'Question ID',
            'answer_en' => 'Answer En',
            'answer_ar' => 'Answer Ar',
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Questions::className(), ['question_id' => 'question_id']);
    }

    /**
     * Gets query for [[LabAppointment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointment()
    {
        return $this->hasOne(LabAppointments::className(), ['lab_appointment_id' => 'lab_appointment_id']);
    }
}
