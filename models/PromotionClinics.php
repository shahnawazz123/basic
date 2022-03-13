<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_clinics".
 *
 * @property int $promotion_clinic_id
 * @property int $promotion_id
 * @property int $clinic_id
 *
 * @property Clinics $clinic
 * @property Promotions $promotion
 */
class PromotionClinics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_clinics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'clinic_id'], 'required'],
            [['promotion_id', 'clinic_id'], 'integer'],
            [['clinic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clinics::className(), 'targetAttribute' => ['clinic_id' => 'clinic_id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_clinic_id' => 'Promotion Clinic ID',
            'promotion_id' => 'Promotion ID',
            'clinic_id' => 'Clinic ID',
        ];
    }

    /**
     * Gets query for [[Clinic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinic()
    {
        return $this->hasOne(Clinics::className(), ['clinic_id' => 'clinic_id']);
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
