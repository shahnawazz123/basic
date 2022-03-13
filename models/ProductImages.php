<?php

namespace app\models;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
/**
 * This is the model class for table "product_images".
 *
 * @property int $product_image_id
 * @property string $image
 * @property int $product_id
 * @property int|null $is_thumbnail
 * @property int|null $sort_order
 *
 * @property Product $product
 */
class ProductImages extends \yii\db\ActiveRecord
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
        return 'product_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image', 'product_id'], 'required'],
            [['product_id', 'is_thumbnail', 'sort_order'], 'integer'],
            [['image'], 'string', 'max' => 100],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_image_id' => 'Product Image ID',
            'image' => 'Image',
            'product_id' => 'Product ID',
            'is_thumbnail' => 'Is Thumbnail',
            'sort_order' => 'Sort Order',
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
}
