<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faq".
 *
 * @property int $faq_id
 * @property string $question_en
 * @property string $question_ar
 * @property string $answer_en
 * @property string $answer_ar
 */
class Faq extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_en', 'question_ar', 'answer_en', 'answer_ar'], 'required'],
            [['question_en', 'question_ar', 'answer_en', 'answer_ar'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'faq_id' => 'Faq ID',
            'question_en' => 'Question in English',
            'question_ar' => 'Question in Arabic',
            'answer_en' => 'Answer in English',
            'answer_ar' => 'Answer in Arabic',
        ];
    }
}
