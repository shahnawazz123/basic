<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attributes".
 *
 * @property int $attribute_id
 * @property string $code
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property int $sort_order
 *
 * @property AttributeSetGroups[] $attributeSetGroups
 * @property AttributeValues[] $attributeValues
 */
class Attributes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'sort_order'], 'required'],
            [['sort_order'], 'integer'],
            [['code'], 'string', 'max' => 45],
            [['name_en', 'name_ar'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attribute_id' => 'Attribute ID',
            'code' => 'Code',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * Gets query for [[AttributeSetGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeSetGroups()
    {
        return $this->hasMany(AttributeSetGroups::className(), ['attribute_id' => 'attribute_id']);
    }

    /**
     * Gets query for [[AttributeValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValues()
    {
        return $this->hasMany(AttributeValues::className(), ['attribute_id' => 'attribute_id']);
    }
}
