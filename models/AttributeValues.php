<?php

namespace app\models;

use Yii;
use app\models\Attributes;
use himiklab\sortablegrid\SortableGridBehavior;
/**
 * This is the model class for table "attribute_values".
 *
 * @property int $attribute_value_id
 * @property int $attribute_id
 * @property string|null $value_en
 * @property string|null $value_ar
 * @property int $sort_order
 *
 * @property Attributes $attribute0
 * @property ProductAttributeValues[] $productAttributeValues
 */
class AttributeValues extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attribute_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'required'],
            [['attribute_id', 'sort_order'], 'integer'],
            [['value_en', 'value_ar'], 'string', 'max' => 200],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attributes::className(), 'targetAttribute' => ['attribute_id' => 'attribute_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attribute_value_id' => 'Attribute Value ID',
            'attribute_id' => 'Attribute ID',
            'value_en' => 'Value En',
            'value_ar' => 'Value Ar',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * Gets query for [[Attribute0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attributes::className(), ['attribute_id' => 'attribute_id']);
    }

    /**
     * Gets query for [[ProductAttributeValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductAttributeValues()
    {
        return $this->hasMany(ProductAttributeValues::className(), ['attribute_value_id' => 'attribute_value_id']);
    }
}
