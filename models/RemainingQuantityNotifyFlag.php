<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "remaining_quantity_notify_flag".
 *
 * @property int $remaining_quantity_notify_id
 * @property int $product_id
 * @property int $quantity
 * @property string $notify_date
 *
 * @property Product $product
 */
class RemainingQuantityNotifyFlag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'remaining_quantity_notify_flag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'notify_date'], 'required'],
            [['product_id', 'quantity'], 'integer'],
            [['notify_date'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'remaining_quantity_notify_id' => 'Remaining Quantity Notify ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'notify_date' => 'Notify Date',
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
