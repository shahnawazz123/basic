<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_attribute_values".
 *
 * @property int $product_attribute_value_id
 * @property int $product_id
 * @property int $attribute_value_id
 *
 * @property Product $product
 * @property AttributeValues $attributeValue
 */
class ProductAttributeValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_attribute_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'attribute_value_id'], 'required'],
            [['product_id', 'attribute_value_id'], 'integer'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
            [['attribute_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeValues::className(), 'targetAttribute' => ['attribute_value_id' => 'attribute_value_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_attribute_value_id' => 'Product Attribute Value ID',
            'product_id' => 'Product ID',
            'attribute_value_id' => 'Attribute Value ID',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[AttributeValue]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValue()
    {
        return $this->hasOne(AttributeValues::className(), ['attribute_value_id' => 'attribute_value_id']);
    }
}
