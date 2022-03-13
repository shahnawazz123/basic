<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attribute_sets".
 *
 * @property int $attribute_set_id
 * @property string|null $attribute_set_code
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property int $has_size_guide
 * @property string|null $size_guide_image_en
 * @property string|null $size_guide_image_ar
 *
 * @property AttributeSetGroups[] $attributeSetGroups
 * @property Product[] $products
 */
class AttributeSets extends \yii\db\ActiveRecord
{
    public $attributes_id, $size_guide_image_file_en, $size_guide_image_file_ar;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attribute_sets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['has_size_guide'], 'integer'],
            [['attributes_id'], 'required', 'on' => 'create'],
            [['attributes_id'], 'required', 'on' => 'update'],
            [['attribute_set_code'], 'string', 'max' => 255],
            [['name_en', 'name_ar'], 'string', 'max' => 200],
            [['size_guide_image_en', 'size_guide_image_ar'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attribute_set_id' => 'Attribute Set ID',
            'attribute_set_code' => 'Attribute Set Code',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'has_size_guide' => 'Has Size Guide',
            'size_guide_image_en' => 'Size Guide Image in English',
            'size_guide_image_ar' => 'Size Guide Image in Arabic',
            'attributes_id' => 'Attributes',
        ];
    }

    /**
     * Gets query for [[AttributeSetGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeSetGroups()
    {
        return $this->hasMany(AttributeSetGroups::className(), ['attribute_set_id' => 'attribute_set_id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['attribute_set_id' => 'attribute_set_id']);
    }
}
