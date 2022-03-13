<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacy_orders".
 *
 * @property int $pharmacy_order_id
 * @property int $pharmacy_id
 * @property int $order_id
 * @property string|null $order_number
 * @property float $pharmacy_commission
 *
 * @property PharmacyOrderStatus[] $pharmacyOrderStatuses
 * @property Orders $order
 * @property Pharmacies $pharmacy
 */
class PharmacyOrders extends \yii\db\ActiveRecord
{
    public $total_bill,$quantity,$purchase_date,$driverId;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacy_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_id', 'order_id', 'pharmacy_commission'], 'required'],
            [['pharmacy_id', 'order_id'], 'integer'],
            [['pharmacy_commission'], 'number'],
            [['order_number'], 'string', 'max' => 45],
            [['order_number'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'order_id']],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_order_id' => 'Pharmacy Order ID',
            'pharmacy_id' => 'Pharmacy ID',
            'order_id' => 'Order ID',
            'order_number' => 'Order Number',
            'pharmacy_commission' => 'Pharmacy Commission',
        ];
    }

    /**
     * Gets query for [[PharmacyOrderStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrderStatuses()
    {
        return $this->hasMany(PharmacyOrderStatus::className(), ['pharmacy_order_id' => 'pharmacy_order_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['order_id' => 'order_id']);
    }

    /**
     * Gets query for [[Pharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }
    
    /**
     * Gets query for [[PharmacyOrderStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['pharmacy_order_id' => 'pharmacy_order_id']);
    }
    
}
