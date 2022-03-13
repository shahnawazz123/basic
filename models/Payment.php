<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $payment_id
 * @property int $type_id
 * @property string|null $type
 * @property string|null $paymode
 * @property float|null $gross_amount
 * @property float|null $net_amount
 * @property string $currency_code
 * @property string|null $PaymentID
 * @property string|null $result
 * @property string|null $remark
 * @property string|null $payment_date
 * @property string|null $transaction_id
 * @property string|null $auth
 * @property string|null $ref
 * @property string|null $TrackID
 * @property string|null $udf1
 * @property string|null $udf2
 * @property string|null $udf3
 * @property string|null $udf4
 * @property string|null $udf5
 * @property int $status
 * @property string|null $payment_response
 * 
 * @property Orders $order
 */
class Payment extends \yii\db\ActiveRecord
{
    public $user_name,$order_admin_commission,$delivery_charge,$cod_charge,$vat_charges,$discount_price,$total_order_amount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'currency_code'], 'required'],
            [['type_id', 'status'], 'integer'],
            [['type', 'paymode', 'remark', 'payment_response'], 'string'],
            [['gross_amount', 'net_amount'], 'number'],
            [['payment_date'], 'safe'],
            [['currency_code'], 'string', 'max' => 10],
            [['PaymentID', 'TrackID'], 'string', 'max' => 200],
            //[['result', 'transaction_id', 'auth', 'ref', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_id' => 'Payment ID',
            'type_id' => 'Type ID',
            'type' => 'Type',
            'paymode' => 'Paymode',
            'gross_amount' => 'Gross Amount',
            'net_amount' => 'Net Amount',
            'currency_code' => 'Currency Code',
            'PaymentID' => 'Payment ID',
            'result' => 'Result',
            'remark' => 'Remark',
            'payment_date' => 'Payment Date',
            'transaction_id' => 'Transaction ID',
            'auth' => 'Auth',
            'ref' => 'Ref',
            'TrackID' => 'Track ID',
            'udf1' => 'Udf1',
            'udf2' => 'Udf2',
            'udf3' => 'Udf3',
            'udf4' => 'Udf4',
            'udf5' => 'Udf5',
            'status' => 'Status',
            'payment_response' => 'Payment Response',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['order_id' => 'type_id']);
    }
}
