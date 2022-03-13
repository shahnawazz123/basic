<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_status_history".
 *
 * @property int $product_status_history_id
 * @property int $product_id
 * @property int $product_status_id
 * @property string $status_date
 * @property string|null $comment
 * @property int $notify_customer
 *
 * @property Product $product
 * @property ProductStatus $productStatus
 */
class ProductStatusHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_status_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'product_status_id', 'status_date'], 'required'],
            [['product_id', 'product_status_id', 'notify_customer'], 'integer'],
            [['status_date'], 'safe'],
            [['comment'], 'string'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
            [['product_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductStatus::className(), 'targetAttribute' => ['product_status_id' => 'product_status_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_status_history_id' => 'Product Status History ID',
            'product_id' => 'Product ID',
            'product_status_id' => 'Product Status ID',
            'status_date' => 'Status Date',
            'comment' => 'Comment',
            'notify_customer' => 'Notify Customer',
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
     * Gets query for [[ProductStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatus()
    {
        return $this->hasOne(ProductStatus::className(), ['product_status_id' => 'product_status_id']);
    }
}
