<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $order_id
 * @property int $order_number
 * @property int|null $user_id
 * @property string|null $recipient_name
 * @property string|null $recipient_phone
 * @property string $create_date
 * @property string|null $update_date
 * @property int $is_processed
 * @property int|null $shipping_address_id
 * @property string|null $delivery_time
 * @property string $payment_mode
 * @property float|null $vendor_commission
 * @property float|null $delivery_charge
 * @property float|null $cod_charge
 * @property float|null $vat_charges
 * @property string|null $device_token
 * @property string|null $device_type
 * @property string|null $device_model
 * @property string|null $app_version
 * @property string|null $os_version
 * @property string|null $user_ip
 * @property int $is_paid
 * @property int|null $store_id
 * @property string|null $shipping_email
 * @property string|null $shipping_alt_phone_number
 * @property int|null $shipping_area_id
 * @property int|null $shipping_block_id
 * @property string|null $shipping_street
 * @property string|null $shipping_addressline_1
 * @property string|null $shipping_location_type
 * @property string|null $shipping_notes
 * @property int|null $delivery_option_id
 * @property int $is_contacted
 * @property string|null $redirect_url
 * @property int|null $promotion_id
 * @property string|null $promo_for
 * @property float|null $discount
 * @property float $discount_price
 * @property int $payment_initiated
 * @property string|null $tracking_link
 *
 * @property OrderStatus[] $orderStatuses
 * @property Users $user
 * @property Stores $store
 * @property ShippingAddresses $shippingAddress
 * @property Promotions $promotion
 * @property PharmacyOrders[] $pharmacyOrders
 */
class Orders extends \yii\db\ActiveRecord
{
    public $pharmacy_commission;
    public $currency_code;
    public $total_bill;
    public $admin_commission;
    public $order_item_amount;
    public $total_amount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_number', 'create_date'], 'required'],
            [['order_number', 'user_id', 'is_processed', 'shipping_address_id', 'is_paid', 'store_id', 'shipping_area_id', 'shipping_block_id', 'delivery_option_id', 'is_contacted', 'promotion_id', 'payment_initiated'], 'integer'],
            [['create_date', 'update_date', 'delivery_time'], 'safe'],
            [['payment_mode', 'shipping_notes', 'redirect_url', 'promo_for'], 'string'],
            [['vendor_commission', 'delivery_charge', 'cod_charge', 'vat_charges', 'discount', 'discount_price'], 'number'],
            [['recipient_name', 'device_token', 'device_type', 'device_model', 'app_version', 'os_version'], 'string', 'max' => 255],
            [['recipient_phone', 'user_ip'], 'string', 'max' => 15],
            [['shipping_email', 'shipping_alt_phone_number', 'shipping_street', 'shipping_addressline_1', 'shipping_location_type'], 'string', 'max' => 50],
            [['tracking_link'], 'string', 'max' => 500],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'store_id']],
            [['shipping_address_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShippingAddresses::className(), 'targetAttribute' => ['shipping_address_id' => 'shipping_address_id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotions::className(), 'targetAttribute' => ['promotion_id' => 'promotion_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_number' => 'Order Number',
            'user_id' => 'User ID',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => 'Recipient Phone',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'is_processed' => 'Is Processed',
            'shipping_address_id' => 'Shipping Address',
            'delivery_time' => 'Delivery Time',
            'payment_mode' => 'Payment Mode',
            'vendor_commission' => 'Vendor Commission',
            'delivery_charge' => 'Delivery Charge',
            'cod_charge' => 'Cod Charge',
            'vat_charges' => 'Vat Charges',
            'device_token' => 'Device Token',
            'device_type' => 'Device Type',
            'device_model' => 'Device Model',
            'app_version' => 'App Version',
            'os_version' => 'Os Version',
            'user_ip' => 'User Ip',
            'is_paid' => 'Is Paid',
            'store_id' => 'Store ID',
            'shipping_email' => 'Shipping Email',
            'shipping_alt_phone_number' => 'Shipping Alt Phone Number',
            'shipping_area_id' => 'Shipping Area ID',
            'shipping_block_id' => 'Shipping Block ID',
            'shipping_street' => 'Shipping Street',
            'shipping_addressline_1' => 'Shipping Addressline 1',
            'shipping_location_type' => 'Shipping Location Type',
            'shipping_notes' => 'Shipping Notes',
            'delivery_option_id' => 'Delivery Option ID',
            'is_contacted' => 'Is Contacted',
            'redirect_url' => 'Redirect Url',
            'promotion_id' => 'Promotion ID',
            'promo_for' => 'Promo For',
            'discount' => 'Discount',
            'discount_price' => 'Discount Price',
            'payment_initiated' => 'Payment Initiated',
            'tracking_link' => 'Tracking Link',
        ];
    }

    /**
     * Gets query for [[OrderStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatuses()
    {
        return $this->hasMany(OrderStatus::className(), ['order_id' => 'order_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[ShippingAddress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShippingAddress()
    {
        return $this->hasOne(ShippingAddresses::className(), ['shipping_address_id' => 'shipping_address_id']);
    }

    /**
     * Gets query for [[Promotion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotions::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PharmacyOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrders()
    {
        return $this->hasMany(PharmacyOrders::className(), ['order_id' => 'order_id']);
    }
    
    public function formatAddress($model) {
        $string = '';

        if(!empty($model->street))
            $string .= 'Street: '.$model->street.'<br />';

        if(!empty($model->addressline_1))
            $string .= 'Address Line: '.$model->addressline_1.'<br />';

        if(!empty($model->block))
            $string .= ''.$model->block->name_en.', ';

        if(!empty($model->area))
            $string .= ''.$model->area->name_en.'<br />';

        if(!empty($model->state))
            $string .= ''.$model->state->name_en;

        return $string;
    }
    
    /**
     *
     * @param type $productId
     * @param type $lang
     * @return array
     */
    public function getProductAttributeValues($product, $lang = 'en')
    {
        $model = \app\models\ProductAttributeValues::find()
                ->select(['product_attribute_values.attribute_value_id', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value", 'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", 'attributes.code AS attribute_code'])
                ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['product_id' => $product->product_id])
                ->orderBy(['attribute_values.attribute_id' => SORT_ASC])
                ->asArray()
                ->all();
        $tmp = array();
        if (!empty($model)) {
            foreach ($model as $row) {
                if (!isset($tmp[$row['attribute_id']])) {
                    $tmp[$row['attribute_id']] = [
                        'type' => $row['attribute'],
                        'attribute_id' => $row['attribute_id'],
                        'attribute_code' => $row['attribute_code'],
                        'attributes' => [[
                        'option_id' => $row['attribute_value_id'],
                        'value' => $row['attribute_value'],
                            ]]
                    ];
                } else {
                    $tmp[$row['attribute_id']]['attributes'][] = [
                        'option_id' => $row['attribute_value_id'],
                        'value' => $row['attribute_value'],
                    ];
                }
            }
        }

        $result = array_values($tmp);
        return $result;
    }

    public function getDriverOrder()
    {
        return $this->hasOne(DriverOrders::className(), ['type_id' => 'order_id']);
        
    }
    
}
