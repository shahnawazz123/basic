<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manufacturers".
 *
 * @property int $manufacturer_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image_en
 * @property string|null $image_ar
 * @property int|null $sort_order
 * @property int $is_active
 * @property int $is_deleted
 *
 * @property Product[] $products
 */
class Manufacturers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manufacturers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar'], 'required'],
            [['sort_order', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar'], 'string', 'max' => 200],
            [['image_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'manufacturer_id' => 'Manufacturer ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image_name' => 'Image Name',
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
        return $this->hasMany(Product::className(), ['manufacturer_id' => 'manufacturer_id']);
    }
}
