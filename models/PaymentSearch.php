<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payment;

/**
 * PaymentSearch represents the model behind the search form about `app\models\Payment`.
 */
class PaymentSearch extends Payment
{

    public $order_id;
    public $order_number;
    public $from_amt;
    public $to_amt;
    public $invoice_date_range;
    public $order_date_range;
    public $user_name;
    public $user_id;
    public $order_admin_commission;
    public $pharmacy_id;
    public $doctor_appointment_id, $consultation_fees, $appointment_datetime, $doctor_id, $is_deleted, $created_at, $updated_at, $kid_id, $is_cancelled, $discount, $sub_total, $amount, $payment_initiate_time, $has_gone_payment, $duration, $is_completed, $appointment_number, $name, $email, $phone_number, $consultation_type, $prescription_file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'user_id', 'pharmacy_id', 'order_id', 'status', 'order_number', 'PaymentID', 'TrackID'], 'integer'],
            [['paymode', 'currency_code', 'result', 'payment_date', 'transaction_id', 'auth', 'ref', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5', 'invoice_date_range', 'order_date_range', 'from_amt', 'to_amt', 'order_number', 'user_id', 'user_name', 'result', 'payment_mode', 'invoice_date_range', 'total_order_amount', 'pharmacy_id', 'start_date', 'end_date', 'shop_order_number', 'order_num'], 'safe'],
            [['gross_amount', 'net_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_date_range' => 'Invoice Date',
            'order_date_range' => 'Order Created On'
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Payment::find()
            ->select([
                'payment.payment_id',
                'payment.payment_date',
                'payment.transaction_id',
                'payment.type_id',
                'payment.paymode',
                'orders.order_number',
                'orders.order_number',
                'orders.create_date',
                'orders.user_id',
                'CONCAT(users.first_name, " ", users.last_name) AS user_name',
                'payment.currency_code',
                'payment.net_amount',
                'payment.result',
                'payment.PaymentID',
                'payment.ref',
                'payment.TrackID',
                'payment.gross_amount',
                'pharmacy_orders.pharmacy_id',
                'orders.payment_mode',
                'orders.delivery_charge',
                'orders.shipping_address_id',
                'orders.delivery_option_id',
                'orders.cod_charge',
                'orders.vat_charges',
                'orders.discount_price as discount_price',
                'SUM(order_items.price*order_items.quantity) AS total_order_amount',
                'SUM(order_items.cost_price*order_items.quantity) AS total_cost_amount',
                'SUM(pharmacy_orders.pharmacy_commission) as order_admin_commission'
            ])
            ->join('LEFT JOIN', 'orders', 'orders.order_id = payment.type_id')
            ->join('LEFT JOIN', 'users', 'orders.user_id = users.user_id')
            ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
            ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
            ->join('left join', 'payment as t3', '(payment.type_id = t3.type_id AND payment.payment_id < t3.payment_id)')
            ->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp', 'temp.order_id = orders.order_id');
        $query->where('payment.paymode IS NOT NULL');
        $query->andWhere(['IS', 't3.payment_id', new \yii\db\Expression('NULL')]);
        $query->andWhere(['is_processed' => 1, 'payment.type' => 'O']);
        $query->andWhere(['!=', 'temp.status_id', 6]);
        $query->andWhere('temp.status_id IS NOT NULL');
        $query->groupBy('orders.order_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination'=>false,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'payment.payment_id' => $this->payment_id,
            'payment.type_id' => $this->order_id,
            'payment.gross_amount' => $this->gross_amount,
            'payment.net_amount' => $this->net_amount,
            'payment.payment_date' => $this->payment_date,
            'payment.status' => $this->status,
            'orders.user_id' => $this->user_id,
            'payment.PaymentID' => $this->PaymentID,
            'payment.ref' => $this->ref,
            'payment.TrackID' => $this->TrackID,
            'payment.result' => $this->result,
            'pharmacy_orders.pharmacy_id' => $this->pharmacy_id,
        ]);
        $query->andFilterWhere(['like', 'payment.paymode', $this->paymode])
            ->andFilterWhere(['like', 'payment.currency_code', $this->currency_code])
            ->andFilterWhere(['like', 'orders.order_number', $this->order_number])
            ->andFilterWhere(['like', 'payment.result', $this->result])
            ->andFilterWhere(['like', 'payment.transaction_id', $this->transaction_id])
            ->andFilterWhere(['like', 'payment.auth', $this->auth])
            ->andFilterWhere(['like', 'payment.ref', $this->ref])
            ->andFilterWhere(['like', 'payment.udf1', $this->udf1])
            ->andFilterWhere(['like', 'payment.udf2', $this->udf2])
            ->andFilterWhere(['like', 'payment.udf3', $this->udf3])
            ->andFilterWhere(['like', 'payment.udf4', $this->udf4])
            ->andFilterWhere(['like', 'payment.udf5', $this->udf5]);

        if (!empty($this->invoice_date_range)) {
            $invoiceDateRange = explode(' to ', str_replace("/", "-", $this->invoice_date_range));
            if (isset($invoiceDateRange[0]) && isset($invoiceDateRange[1])) {
                $query->andFilterWhere(['BETWEEN', 'DATE(`payment`.`payment_date`)', date("Y-m-d", strtotime($invoiceDateRange[0])), date("Y-m-d", strtotime(trim($invoiceDateRange[1])))]);
            }
        }

        if (!empty($this->order_date_range)) {
            $orderDateRange = explode(' to ', str_replace("/", "-", $this->order_date_range));

            if (isset($orderDateRange[0]) && isset($orderDateRange[1])) {
                $query->andFilterWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', date("Y-m-d", strtotime($orderDateRange[0])), date("Y-m-d", strtotime(trim($orderDateRange[1])))]);
            }
        }

        if (!empty($this->from_amt)) {
            if (\Yii::$app->session['_eyadatAuth'] == 2) {
                $query->andHaving('total_order_amount >= :from_amt')->addParams([':from_amt' => $this->from_amt]);
            } else {
                $query->andHaving('net_amount >= :from_amt')->addParams([':from_amt' => $this->from_amt]);
            }
        }

        if (!empty($this->to_amt)) {
            //$query->andHaving('net_amount <= :to_amt')->addParams([':to_amt' => $this->to_amt]);
            if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 4) {
                $query->andHaving('total_order_amount <= :to_amt')->addParams([':to_amt' => $this->to_amt]);
            } else {
                $query->andHaving('net_amount <= :to_amt')->addParams([':to_amt' => $this->to_amt]);
            }
        }

        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 4) {
            $query->andFilterWhere(['=', 'pharmacy_orders.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }

        $query->orderBy(['payment_id' => SORT_DESC]);

        // echo $query->createCommand()->rawSql;die;

        return $dataProvider;
    }

    public function DoctorAppointmentSearch($params)
    {
        $query = DoctorAppointments::find()
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            ->join('LEFT JOIN', 'payment', 'doctor_appointments.doctor_appointment_id=payment.type_id')
            ->andWhere(['=', 'doctor_appointments.is_paid', 1])
            ->andwhere(['payment.type' => 'DA', 'payment.result' => 'CAPTURED']);

        // add conditions that should always apply here

        if (\Yii::$app->session['_eyadatAuth'] == 3) {
            //echo "<pre>";print_r(Yii::$app->user->identity);die;
            $query->andwhere(['doctor_appointments.doctor_id' => Yii::$app->user->identity->doctor_id]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 2) {
            $query->andwhere(['doctors.clinic_id' => Yii::$app->user->identity->clinic_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['doctor_appointment_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $today_date = date('Y-m-d h:i:s');
        if (!empty($this->type) && $this->type == 'U') {
            $query->andWhere(['is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0, 'not_show' => 0]);

            $query->andWhere(['>', 'appointment_datetime', $today_date]);
        }

        if (!empty($this->type) && $this->type == 'C') {
            $query->andWhere(['not_show' => 0, 'is_completed' => 1, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($this->type) && $this->type == 'N') {
            $query->andWhere(['not_show' => 1, 'is_completed' => 0, 'is_paid' => 1, 'is_cancelled' => 0]);
        }

        if (!empty($this->type)  && $this->type == 'F') {
            $query->andWhere(['is_completed' => 0, 'is_cancelled' => 0]);
            $query->andwhere(['IN', 'is_paid', [0, 1, 2]]);
            $query->andWhere(['<', 'appointment_datetime', $today_date]);
        }

        if (!empty($this->today)) {
            $query->andWhere(['=', "DATE(doctor_appointments.appointment_datetime)", date('Y-m-d')]);
        }

        if (!empty($this->week)) {
            $day = date('w');
            $weekStart = date('Y-m-d', strtotime('-' . $day . ' days'));
            $weekEnd = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));

            $query->andWhere(['BETWEEN', 'DATE(doctor_appointments.appointment_datetime)', $weekStart, $weekEnd]);
        }

        if (!empty($this->year)) {
            $query->andWhere(['BETWEEN', 'DATE(doctor_appointments.appointment_datetime)', date("Y-m-d", strtotime('this year January 1st')), date("Y-m-d", strtotime("this year December 31st"))]);
        }

        if (!empty($this->month)) {
            $query->andWhere(['BETWEEN', 'DATE(doctor_appointments.appointment_datetime)', date("Y-m-d", strtotime('first day of this month')), date("Y-m-d", strtotime("last day of this month"))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'doctor_appointment_id' => $this->doctor_appointment_id,
            'consultation_fees' => $this->consultation_fees,
            'appointment_datetime' => $this->appointment_datetime,
            'user_id' => $this->user_id,
            'doctor_appointments.doctor_id' => $this->doctor_id,
            'doctor_appointments.is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'kid_id' => $this->kid_id,
            'is_cancelled' => $this->is_cancelled,
            //'is_paid' => $this->is_paid,
            'discount' => $this->discount,
            'sub_total' => $this->sub_total,
            'amount' => $this->amount,
            'payment_initiate_time' => $this->payment_initiate_time,
            'has_gone_payment' => $this->has_gone_payment,
            'duration' => $this->duration,
            'is_completed' => $this->is_completed,
            'appointment_number' => $this->appointment_number,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'consultation_type', $this->consultation_type])
            ->andFilterWhere(['like', 'prescription_file', $this->prescription_file]);

        return $dataProvider;
    }

    public function LabAppointmentSearch($params)
    {
        $query = LabAppointments::find()
            ->where(['lab_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'labs', 'labs.lab_id=lab_appointments.lab_id')
            ->join('LEFT JOIN', 'payment', 'lab_appointments.lab_appointment_id=payment.type_id')
            ->andWhere(['=', 'lab_appointments.is_paid', 1])
            ->andwhere(['payment.type' => 'LA', 'payment.result' => 'CAPTURED']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lab_appointment_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (\Yii::$app->session['_eyadatAuth'] == 4) {
            $query->andFilterWhere(['=', 'lab_appointments.lab_id', Yii::$app->user->identity->lab_id]);
        }

        /*
        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'phone_number', $this->phone_number])
                ->andFilterWhere(['like', 'consultation_type', $this->consultation_type])
                ->andFilterWhere(['like', 'prescription_file', $this->prescription_file]);
*/
        return $dataProvider;
    }
}
