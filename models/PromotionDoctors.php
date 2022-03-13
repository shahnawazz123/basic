<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_doctors".
 *
 * @property int $promotion_doctor_id
 * @property int $promotion_id
 * @property int $doctor_id
 *
 * @property Doctors $doctor
 * @property Promotions $promotion
 */
class PromotionDoctors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_doctors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'doctor_id'], 'required'],
            [['promotion_id', 'doctor_id'], 'integer'],
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Doctors::className(), 'targetAttribute' => ['doctor_id' => 'doctor_id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_doctor_id' => 'Promotion Doctor ID',
            'promotion_id' => 'Promotion ID',
            'doctor_id' => 'Doctor ID',
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

    /**
     * Gets query for [[Promotion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotions::className(), ['promotion_id' => 'promotion_id']);
    }
}
