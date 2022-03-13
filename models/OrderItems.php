<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property integer $order_item_id
 * @property integer $boutique_order_id
 * @property integer $product_id
 * @property integer $currency_id
 * @property integer $is_preorder
 * @property string $price
 * @property integer $quantity
 * @property string $message
 *
 * @property Currencies $currency
 * @property BoutiqueOrders $boutiqueOrder
 * @property Product $product
 */
class OrderItems extends \yii\db\ActiveRecord {
    public $status_date;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['pharmacy_order_id', 'currency_id', 'price', 'quantity'], 'required'],
            [['pharmacy_order_id', 'product_id', 'currency_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['message'], 'string'],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['pharmacy_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PharmacyOrders::className(), 'targetAttribute' => ['pharmacy_order_id' => 'pharmacy_order_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'order_item_id' => 'Order Item ID',
            'pharmacy_order_id' => 'Pharmacy Order ID',
            'product_id' => 'Product ID',
            'currency_id' => 'Currency ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'message' => 'Message',
            'shop_name' => 'Supplier'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrder() {
        return $this->hasOne(PharmacyOrders::className(), ['pharmacy_order_id' => 'pharmacy_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct() {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
}
