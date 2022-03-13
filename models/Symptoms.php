<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "symptoms".
 *
 * @property int $symptom_id
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $image
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DoctorSymptoms[] $doctorSymptoms
 */
class Symptoms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'symptoms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar'], 'required'],
            [['is_active', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_en', 'name_ar', 'image'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'symptom_id' => 'Symptom ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image' => 'Image',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[DoctorSymptoms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorSymptoms()
    {
        return $this->hasMany(DoctorSymptoms::className(), ['symptom_id' => 'symptom_id']);
    }
}
