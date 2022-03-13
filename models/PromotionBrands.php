<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_brands".
 *
 * @property int $promotion_brand_id
 * @property int $promotion_id
 * @property int $brand_id
 *
 * @property Brands $brand
 * @property Promotions $promotion
 */
class PromotionBrands extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_brands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'brand_id'], 'required'],
            [['promotion_id', 'brand_id'], 'integer'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_brand_id' => 'Promotion Brand ID',
            'promotion_id' => 'Promotion ID',
            'brand_id' => 'Brand ID',
        ];
    }

    /**
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brands::className(), ['brand_id' => 'brand_id']);
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
