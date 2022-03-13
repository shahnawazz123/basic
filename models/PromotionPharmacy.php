<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_pharmacy".
 *
 * @property int $promotion_pharmacy_id
 * @property int $promotion_id
 * @property int $pharmacy_id
 *
 * @property Promotions $promotion
 * @property Pharmacies $pharmacy
 */
class PromotionPharmacy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_pharmacy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'pharmacy_id'], 'required'],
            [['promotion_id', 'pharmacy_id'], 'integer'],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_pharmacy_id' => 'Promotion Pharmacy ID',
            'promotion_id' => 'Promotion ID',
            'pharmacy_id' => 'Pharmacy ID',
        ];
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

    /**
     * Gets query for [[Pharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }
}
