<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * OrdersSearch represents the model behind the search form about `app\models\Orders`.
 */
class OrdersSearch extends Orders
{

    public $date_range;
    public $from_amt;
    public $to_amt;
    public $status_id;
    public $today;
    public $week;
    public $month;
    public $year;
    public $ios;
    public $android;
    public $website;
    public $progress;
    public $brand_id;
    public $category_id;
    public $reason;
    public $pharmacy_status_id;
    public $exclude_cancel_order;
    public $sp;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['order_id', 'user_id', 'is_processed', 'shipping_address_id', 'order_number', 'status_id', 'is_contacted', 'delivery_option_id'], 'integer'],
            [['recipient_name', 'payment_mode', 'recipient_phone', 'create_date', 'update_date', 'delivery_time', 'date_range', 'status_id', 'from_amt', 'to_amt', 'device_type', 'progress', 'today', 'week', 'month', 'year', 'ios', 'android', 'website', 'brand_id', 'category_id', 'store_id', 'delivery_option_id', 'pharmacy_status_id', 'exclude_cancel_order', 'sp'], 'safe'],
            [['pharmacy_commission', 'delivery_charge'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
            $totalBillSql = 'SUM(order_items.price*order_items.quantity)-orders.discount_price as total_bill';
        } else {
            $totalBillSql = 'IF(orders.payment_mode = "C", (SUM(order_items.price*order_items.quantity)+orders.cod_charge+orders.delivery_charge + orders.vat_charges- orders.discount_price), (SUM(order_items.price*order_items.quantity)+orders.delivery_charge+orders.vat_charges) - orders.discount_price) as total_bill';
        }
        $query = Orders::find()
                ->select([
                    'orders.order_id',
                    'orders.order_number',
                    'orders.is_contacted',
                    'orders.recipient_name',
                    'orders.device_type',
                    'orders.payment_mode',
                    'orders.create_date',
                    'orders.delivery_option_id',
                    'orders.user_id',
                    'SUM(order_items.price*order_items.quantity) AS total_amount',
                    'CONCAT(shipping_addresses.first_name, " ", shipping_addresses.last_name) AS user_name',
                    'currencies.code_en As currency_code', 'orders.shipping_address_id', 'orders.shipping_area_id',
                    'orders.delivery_charge',
                    'orders.cod_charge',
                    'orders.vat_charges',
                    $totalBillSql,
                    'SUM((order_items.price * order_items.quantity * pharmacy_orders.pharmacy_commission) / 100) AS admin_commission',
                ])
                ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'users', 'orders.user_id = users.user_id')
                ->join('left join', 'shipping_addresses', 'orders.shipping_address_id = shipping_addresses.shipping_address_id')
                ->join('LEFT JOIN', 'currencies', 'currencies.currency_id = order_items.currency_id')
                ->where(['is_processed' => 1]);

        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp', 'temp.order_id = orders.order_id');

        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM pharmacy_order_status AS t1
                                        LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.pharmacy_order_status_id < t2.pharmacy_order_status_id))
                                        WHERE t2.pharmacy_order_id IS NULL
                                        ) as temp2', 'pharmacy_orders.pharmacy_order_id = temp2.pharmacy_order_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'pharmacy_orders.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'orders.order_id' => $this->order_id,
            'orders.user_id' => $this->user_id,
            'create_date' => $this->create_date,
            'update_date' => $this->update_date,
            'is_processed' => $this->is_processed,
            'shipping_addresses.shipping_address_id' => $this->shipping_address_id,
            'delivery_time' => $this->delivery_time,
            'pharmacy_commission' => $this->vendor_commission,
            'delivery_charge' => $this->delivery_charge,
            'is_contacted' => $this->is_contacted,
            'payment_mode' => $this->payment_mode,
            'delivery_option_id' => $this->delivery_option_id
        ]);

        if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', "DATE(orders.create_date)", date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }

        if (!empty($this->from_amt)) {
            $query->andHaving('total_bill >= :from_amt')->addParams([':from_amt' => $this->from_amt]);
        }

        if (!empty($this->to_amt)) {
            $query->andHaving('total_bill <= :to_amt')->addParams([':to_amt' => $this->to_amt]);
        }

        if (!empty($this->device_type)) {
            if ($this->device_type == 'W') {
                $query->andFilterWhere(['=', 'orders.device_type', 'Web']);
            } else if ($this->device_type == 'M') {
                $query->andFilterWhere([
                    'OR',
                    ['=', 'orders.device_type', 'I'],
                    ['=', 'orders.device_type', 'A']
                ]);
            }
        }

        if (!empty($this->status_id)) {
            $query->andFilterWhere(['=', 'temp.status_id', $this->status_id]);
        }

        if (!empty($this->progress)) {
            $query->andFilterWhere(['NOT IN', 'temp.status_id', [1, 5, 6]]);
        }

        if (!empty($this->exclude_cancel_order)) {
            $query->andFilterWhere(['!=', 'temp.status_id', 6]);
        }

        if (!empty($this->today)) {
            $query->andWhere(['!=', 'temp.status_id', 6])
                    ->andWhere(['=', "DATE(orders.create_date)", date('Y-m-d')]);
        }

        if (!empty($this->week)) {
            $day = date('w');
            $weekStart = date('Y-m-d', strtotime('-' . $day . ' days'));
            $weekEnd = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));

            $query->andWhere(['BETWEEN', 'DATE(orders.create_date)', $weekStart, $weekEnd]);
        }

        if (!empty($this->month)) {
            $query->andWhere(['BETWEEN', 'DATE(orders.create_date)', date("Y-m-d", strtotime('first day of this month')), date("Y-m-d", strtotime("last day of this month"))]);
        }

        $query->andFilterWhere(['like', 'orders.order_number', $this->order_number])
                ->andFilterWhere(['like', 'recipient_name', $this->recipient_name])
                ->andFilterWhere(['like', 'recipient_phone', $this->recipient_phone]);

        if (!empty($this->pharmacy_status_id)) {
            $query->andFilterWhere(['=', 'temp2.pharmacy_status_id', $this->pharmacy_status_id]);
        }

        if (!empty($this->year)) {
            $query->andWhere(['BETWEEN', 'DATE(orders.create_date)', date("Y-m-d", strtotime('this year January 1st')), date("Y-m-d", strtotime("this year December 31st"))]);
        }

        if (!empty($this->ios)) {
            $query->andWhere(['orders.device_type' => 'I']);
        }

        if (!empty($this->android)) {
            $query->andWhere(['orders.device_type' => 'A']);
        }

        if (!empty($this->website)) {
            $query->andWhere(['orders.device_type' => ['W', null, '']]);
        }

        if (!empty($this->sp)) {
            $query->andFilterWhere(['IS', 'temp2.pharmacy_status_id', new \yii\db\Expression('NULL')]);
        }

        $query->groupBy('orders.order_id');

        $query->orderBy([
            'orders.create_date' => SORT_DESC,
        ]);
        return $dataProvider;
    }


    public function pharmacy_order_ready_for_search($params) 
    {
        
        $query = \app\models\PharmacyOrders::find()
                    ->select([
                        'pharmacy_orders.*',
                        'orders.create_date as purchase_date',
                        'SUM(order_items.quantity) as quantity',
                        'SUM(order_items.price*order_items.quantity) AS total_amount',
                        'SUM(order_items.price * order_items.quantity) AS total_bill'
                    ])
                    ->join('LEFT JOIN','(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'driver_suborders', 'driver_suborders.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->andWhere(['IN', 'temp.pharmacy_status_id', [2]])
                    ->andWhere(['IS', 'driver_suborders.driver_id', new \yii\db\Expression('NULL')]);;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', "DATE(orders.create_date)", date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pharmacy_orders.order_number' => $this->order_number,
        ]);


        $query->groupBy('order_items.pharmacy_order_id');

        $query->orderBy([
            'temp.status_date' => SORT_DESC,
        ]);
        //echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

    public function pharmacy_order_picked_by_driver_search($params) 
    {
        
        $query = \app\models\PharmacyOrders::find()
                    ->select([
                        'pharmacy_orders.*',
                        'orders.create_date as purchase_date',
                        'SUM(order_items.quantity) as quantity',
                        'SUM(order_items.price*order_items.quantity) AS total_amount',
                        'SUM(order_items.price * order_items.quantity) AS total_bill',
                        'driver_suborders.driver_id as driverId'
                    ])
                    ->join('LEFT JOIN','(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                    ->join('LEFT JOIN', 'driver_suborders', 'driver_suborders.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp1', 'temp1.order_id = orders.order_id')
                    ->andWhere(['IN', 'temp.pharmacy_status_id', [4]])
                    ->andWhere(['!=','temp1.status_id',5])
                    ->andWhere(['IS NOT', 'driver_suborders.driver_id', new \yii\db\Expression('NULL')]);;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', "DATE(orders.create_date)", date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pharmacy_orders.order_number' => $this->order_number,
        ]);


        $query->groupBy('order_items.pharmacy_order_id');

        $query->orderBy([
            'temp.status_date' => SORT_DESC,
        ]);
        //echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

    public function pharmacy_order_search($params) 
    {
        
        $this->load($params);
        $query = \app\models\PharmacyOrders::find()
                    ->select([
                        'pharmacy_orders.*',
                        'orders.create_date as purchase_date',
                        'SUM(order_items.quantity) as quantity',
                        'SUM(order_items.price*order_items.quantity) AS total_amount',
                        'SUM(order_items.price * order_items.quantity) AS total_bill'
                    ])
                    ->join('LEFT JOIN','(SELECT t1.* FROM pharmacy_order_status AS t1 LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id AND t1.pharmacy_status_id < t2.pharmacy_status_id WHERE t2.pharmacy_order_id IS NULL) as temp ON temp.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'order_items', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                    ->andwhere(['IS NOT', 'order_items.pharmacy_order_id', new \yii\db\Expression('NULL')]);
                    if(!empty($this->status_id))
                    $query->andWhere(['IN', 'temp.pharmacy_status_id', [$this->status_id]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'pharmacy_orders.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }

        if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', "DATE(orders.create_date)", date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pharmacy_orders.order_number' => $this->order_number,
        ]);


        $query->groupBy('order_items.pharmacy_order_id');

        $query->orderBy([
            //'pharmacy_order_status.status_date' => SORT_DESC,
        ]);
        //echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

    public function export($params) {
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
            $totalBillSql = 'SUM(order_items.price*order_items.quantity) as total_amount';
        } else {
            $totalBillSql = 'IF(orders.payment_mode = "C", (SUM(order_items.price*order_items.quantity)+orders.cod_charge+orders.delivery_charge + orders.vat_charges), (SUM(order_items.price*order_items.quantity)+orders.delivery_charge+orders.vat_charges)) as total_amount';
        }
        $query = Orders::find()
                ->select([
                    'orders.order_id',
                    'orders.order_number',
                    'orders.device_type',
                    'orders.create_date',
                    'orders.user_id',
                    'orders.payment_mode',
                    'SUM(order_items.price*order_items.quantity) AS order_item_amount',
                    'CONCAT(`shipping_addresses`.`first_name`, " ", `shipping_addresses`.`last_name`) AS user_name',
                    'currencies.code_en As currency_code', 'orders.shipping_address_id',
                    'orders.shipping_area_id', 'orders.shipping_block_id',
                    'orders.shipping_street', 'orders.shipping_addressline_1',
                    'orders.recipient_name',
                    $totalBillSql,
                    'SUM((`order_items`.`price` * `order_items`.`quantity` * `pharmacy_orders`.`pharmacy_commission`) / 100) AS admin_commission'
                ])
                ->join('left join', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
                ->join('left join', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                ->join('left join', 'users', 'orders.user_id = users.user_id')
                ->join('left join', 'shipping_addresses', 'orders.shipping_address_id = shipping_addresses.shipping_address_id')
                ->join('LEFT JOIN', 'currencies', 'currencies.currency_id = order_items.currency_id')
                ->where(['is_processed' => 1]);

        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM order_status AS t1
                                        LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                        WHERE t2.order_id IS NULL
                                        ) as temp', 'temp.order_id = orders.order_id');

        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM pharmacy_order_status AS t1
                                        LEFT OUTER JOIN pharmacy_order_status AS t2 ON t1.pharmacy_order_id = t2.pharmacy_order_id 
                                                AND (t1.status_date < t2.status_date 
                                                 OR (t1.status_date = t2.status_date AND t1.pharmacy_order_status_id < t2.pharmacy_order_status_id))
                                        WHERE t2.pharmacy_order_id IS NULL
                                        ) as temp2', 'pharmacy_orders.pharmacy_order_id = temp2.pharmacy_order_id');

        $this->load($params);
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'pharmacy_orders.pharmacy_id', Yii::$app->user->identity->shop_id]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'orders.order_id' => $this->order_id,
            'orders.user_id' => $this->user_id,
            'create_date' => $this->create_date,
            'update_date' => $this->update_date,
            'is_processed' => $this->is_processed,
            'shipping_addresses.shipping_address_id' => $this->shipping_address_id,
            'delivery_time' => $this->delivery_time,
            'pharmacy_commission' => $this->pharmacy_commission,
            'delivery_charge' => $this->delivery_charge,
            'orders.order_number' => $this->order_number
        ]);

        if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }

        if (!empty($this->from_amt)) {
            $query->andHaving('total_amount >= :from_amt')->addParams([':from_amt' => $this->from_amt]);
        }

        if (!empty($this->to_amt)) {
            $query->andHaving('total_amount <= :to_amt')->addParams([':to_amt' => $this->to_amt]);
        }

        if (!empty($this->device_type)) {
            if ($this->device_type == 'W') {
                $query->andFilterWhere(['=', 'orders.device_type', 'Web']);
            } else if ($this->device_type == 'M') {
                $query->andFilterWhere([
                    'OR',
                    ['=', 'orders.device_type', 'I'],
                    ['=', 'orders.device_type', 'A']
                ]);
            }
        }

        if (!empty($this->status_id)) {
            $query->andFilterWhere(['=', 'temp.status_id', $this->status_id]);
        }

        if (!empty($this->progress)) {
            $query->andFilterWhere(['NOT IN', 'temp.status_id', [1, 5, 6]]);
        }

        if (!empty($this->today)) {
            $query->andWhere(['=', 'DATE(`orders`.`create_date`)', date('Y-m-d')]);
        }

        if (!empty($this->week)) {
            $day = date('w');
            $weekStart = date('Y-m-d', strtotime('-' . $day . ' days'));
            $weekEnd = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));

            $query->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', $weekStart, $weekEnd]);
        }

        if (!empty($this->month)) {
            $query->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', date("Y-m-d", strtotime('first day of this month')), date("Y-m-d", strtotime("last day of this month"))]);
        }

        $query->andFilterWhere(['like', 'recipient_name', $this->recipient_name])
                ->andFilterWhere(['like', 'recipient_phone', $this->recipient_phone]);

        if (!empty($this->pharmacy_status_id)) {
            $query->andFilterWhere(['=', 'temp2.pharmacy_status_id', $this->pharmacy_status_id]);
        }

        if (!empty($this->year)) {
            $query->andWhere(['BETWEEN', 'DATE(`orders`.`create_date`)', date("Y-m-d", strtotime('this year January 1st')), date("Y-m-d", strtotime("this year December 31st"))]);
        }

        if (!empty($this->ios)) {
            $query->andWhere(['orders.device_type' => 'I']);
        }

        if (!empty($this->android)) {
            $query->andWhere(['orders.device_type' => 'A']);
        }

        if (!empty($this->website)) {
            $query->andWhere(['orders.device_type' => ['W', null, '']]);
        }

        if (!empty($this->sp)) {
            $query->andFilterWhere(['IS', 'temp2.pharmacy_status_id', new \yii\db\Expression('NULL')]);
        }
        $query->groupBy('orders.order_id');
        $query->orderBy([
            'orders.create_date' => SORT_DESC,
        ]);
        $result = $query->all();
        return $result;
    }

}
