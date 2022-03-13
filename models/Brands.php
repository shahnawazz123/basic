<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "brands".
 *
 * @property int $brand_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image_en
 * @property string|null $image_ar
 * @property float $commission_percentage
 * @property int|null $sort_order
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property Product[] $products
 * @property PromotionBrands[] $promotionBrands
 */
class Brands extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar'], 'required'],
            [['commission_percentage'], 'number'],
            [['image_name'], 'safe'],
            [['sort_order', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 200],
            //[['image_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => 'Brand ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image_name' => 'Image',
            'commission_percentage' => 'Commission Percentage',
            'sort_order' => 'Sort Order',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * Gets query for [[PromotionBrands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionBrands()
    {
        return $this->hasMany(PromotionBrands::className(), ['brand_id' => 'brand_id']);
    }
}
