<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "driver_suborders".
 *
 * @property int $driver_suborder_id
 * @property int $order_id
 * @property int $pharmacy_order_id
 * @property int $pharmacy_id
 * @property string $assigned_date
 */
class DriverSuborders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver_suborders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'pharmacy_order_id', 'pharmacy_id', 'assigned_date'], 'required'],
            [['order_id', 'pharmacy_order_id', 'pharmacy_id'], 'integer'],
            [['assigned_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'driver_suborder_id' => 'Driver Suborder ID',
            'order_id' => 'Order ID',
            'pharmacy_order_id' => 'Pharmacy Order ID',
            'pharmacy_id' => 'Pharmacy ID',
            'assigned_date' => 'Assigned Date',
        ];
    }
}
