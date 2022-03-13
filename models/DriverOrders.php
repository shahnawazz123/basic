<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "driver_orders".
 *
 * @property int $driver_order_id
 * @property int $driver_id
 * @property string $assigned_date
 * @property int $type_id
 * @property string|null $type O = Order, P = Pharmacy Order ,
 *
 * @property Drivers $driver
 */
class DriverOrders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['driver_id', 'assigned_date', 'type_id'], 'required'],
            [['driver_id', 'type_id'], 'integer'],
            [['assigned_date'], 'safe'],
            [['type'], 'string'],
            [['driver_id'], 'exist', 'skipOnError' => true, 'targetClass' => Drivers::className(), 'targetAttribute' => ['driver_id' => 'driver_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'driver_order_id' => 'Driver Order ID',
            'driver_id' => 'Driver ID',
            'assigned_date' => 'Assigned Date',
            'type_id' => 'Type ID',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Driver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriver()
    {
        return $this->hasOne(Drivers::className(), ['driver_id' => 'driver_id']);
    }
}
