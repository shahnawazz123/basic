<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_stocks".
 *
 * @property int $product_stock_id
 * @property int $product_id
 * @property int $quantity
 * @property string|null $message
 * @property string $created_date
 * @property string $is_deleted
 *
 * @property Product $product
 */
class ProductStocks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_stocks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'created_date'], 'required'],
            [['product_id', 'quantity'], 'integer'],
            [['message'], 'string'],
            [['created_date'], 'safe'],
            [['is_deleted'], 'string', 'max' => 45],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_stock_id' => 'Product Stock ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'message' => 'Message',
            'created_date' => 'Created Date',
            'is_deleted' => 'Is Deleted',
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
