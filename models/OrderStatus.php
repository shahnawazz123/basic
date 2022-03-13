<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_status".
 *
 * @property int $order_status_id
 * @property int $order_id
 * @property int $status_id
 * @property string $status_date
 * @property string $user_type
 * @property int|null $user_id
 * @property string|null $comment
 * @property int $notify_customer
 *
 * @property Orders $order
 * @property Status $status
 */
class OrderStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'status_id', 'status_date', 'user_type'], 'required'],
            [['order_id', 'status_id', 'user_id', 'notify_customer'], 'integer'],
            [['status_date'], 'safe'],
            [['user_type', 'comment'], 'string'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'order_id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'status_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_status_id' => 'Order Status ID',
            'order_id' => 'Order ID',
            'status_id' => 'Status ID',
            'status_date' => 'Status Date',
            'user_type' => 'User Type',
            'user_id' => 'User ID',
            'comment' => 'Comment',
            'notify_customer' => 'Notify Customer',
        ];
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
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['status_id' => 'status_id']);
    }
}
