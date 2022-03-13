<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attribute_set_groups".
 *
 * @property int $attribute_set_group
 * @property int $attribute_set_id
 * @property int $attribute_id
 *
 * @property AttributeSets $attributeSet
 * @property Attributes $attribute0
 */
class AttributeSetGroups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attribute_set_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_set_id', 'attribute_id'], 'required'],
            [['attribute_set_id', 'attribute_id'], 'integer'],
            [['attribute_set_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeSets::className(), 'targetAttribute' => ['attribute_set_id' => 'attribute_set_id']],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attributes::className(), 'targetAttribute' => ['attribute_id' => 'attribute_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attribute_set_group' => 'Attribute Set Group',
            'attribute_set_id' => 'Attribute Set ID',
            'attribute_id' => 'Attribute ID',
        ];
    }

    /**
     * Gets query for [[AttributeSet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeSet()
    {
        return $this->hasOne(AttributeSets::className(), ['attribute_set_id' => 'attribute_set_id']);
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
}
