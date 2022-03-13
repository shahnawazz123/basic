<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacy_order_status".
 *
 * @property int $pharmacy_order_status_id
 * @property int $pharmacy_order_id
 * @property int $pharmacy_status_id
 * @property string $status_date
 * @property string $user_type
 * @property int $user_id
 * @property string|null $comment
 *
 * @property PharmacyOrders $pharmacyOrder
 * @property PharmacyStatus $pharmacyStatus
 */
class PharmacyOrderStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacy_order_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_order_id', 'pharmacy_status_id', 'status_date', 'user_type', 'user_id'], 'required'],
            [['pharmacy_order_id', 'pharmacy_status_id', 'user_id'], 'integer'],
            [['status_date'], 'safe'],
            [['user_type', 'comment'], 'string'],
            [['pharmacy_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PharmacyOrders::className(), 'targetAttribute' => ['pharmacy_order_id' => 'pharmacy_order_id']],
            [['pharmacy_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => PharmacyStatus::className(), 'targetAttribute' => ['pharmacy_status_id' => 'pharmacy_status_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_order_status_id' => 'Pharmacy Order Status ID',
            'pharmacy_order_id' => 'Pharmacy Order ID',
            'pharmacy_status_id' => 'Pharmacy Status ID',
            'status_date' => 'Status Date',
            'user_type' => 'User Type',
            'user_id' => 'User ID',
            'comment' => 'Comment',
        ];
    }

    /**
     * Gets query for [[PharmacyOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrder()
    {
        return $this->hasOne(PharmacyOrders::className(), ['pharmacy_order_id' => 'pharmacy_order_id']);
    }

    /**
     * Gets query for [[PharmacyStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyStatus()
    {
        return $this->hasOne(PharmacyStatus::className(), ['pharmacy_status_id' => 'pharmacy_status_id']);
    }
}
