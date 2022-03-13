<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LabAppointments;

/**
 * LabAppointmentSearch represents the model behind the search form of `app\models\LabAppointments`.
 */
class LabAppointmentSearch extends LabAppointments
{
    public $atype;
    public $today;
    public $week;
    public $month;
    public $year;
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['lab_appointment_id', 'lab_id', 'is_paid', 'is_deleted', 'user_id', 'kid_id', 'is_cancelled', 'has_gone_payment', 'duration'], 'integer'],
            [['name', 'email', 'phone_number', 'appointment_datetime', 'type', 'paymode', 'sample_collection_time', 'sample_collection_address', 'prescription_file', 'created_at', 'updated_at', 'payment_initiate_time', 'is_completed','atype','appointment_number','month','year','today','week'], 'safe'],
            [['lab_amount', 'discount', 'sub_total', 'amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params) {
        $query = LabAppointments::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['is_paid'=>1]);
                //->andWhere(['!=', 'is_paid', 0]);

        // add conditions that should always apply here

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

        $today_date = date('Y-m-d h:i:s');
        if(!empty($this->atype) && $this->atype=='U')
        {
            $query->andWhere(['is_completed'=>0,'is_paid'=>1,'is_cancelled'=>0,'not_show'=>0]);
            $query->andWhere(['>','appointment_datetime',$today_date]);
        }

        if(!empty($this->atype) && $this->atype=='C')
        {
            $query->andWhere(['not_show'=>0,'is_completed'=>1,'is_paid'=>1,'is_cancelled'=>0]);
        }

        if(!empty($this->atype) && $this->atype=='N')
        {
            $query->andWhere(['not_show'=>1,'is_completed'=>0,'is_paid'=>1,'is_cancelled'=>0]);
        }

        if(!empty($this->atype)  && $this->atype=='F')
        {
            $query->andWhere(['is_completed'=>0,'is_cancelled'=>0,'not_show'=>0]);
            $query->andwhere(['IN', 'is_paid', [0,1,2]]);
            $query->andWhere(['<','appointment_datetime',$today_date]);
        }
        if (\Yii::$app->session['_eyadatAuth'] == 4) {
            $query->andwhere(['lab_id'=>Yii::$app->user->identity->lab_id]);
        }

        if (!empty($this->today)) {
            $query->andWhere(['=', "DATE(lab_appointments.appointment_datetime)", date('Y-m-d')]);
        }

        if (!empty($this->week)) {
            $day = date('w');
            $weekStart = date('Y-m-d', strtotime('-' . $day . ' days'));
            $weekEnd = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));

            $query->andWhere(['BETWEEN', 'DATE(lab_appointments.appointment_datetime)', $weekStart, $weekEnd]);
        }

        if (!empty($this->year)) {
            $query->andWhere(['BETWEEN', 'DATE(lab_appointments.appointment_datetime)', date("Y-m-d", strtotime('this year January 1st')), date("Y-m-d", strtotime("this year December 31st"))]);
        }

        if (!empty($this->month)) {
            $query->andWhere(['BETWEEN', 'DATE(lab_appointments.appointment_datetime)', date("Y-m-d", strtotime('first day of this month')), date("Y-m-d", strtotime("last day of this month"))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lab_appointment_id' => $this->lab_appointment_id,
            'appointment_datetime' => $this->appointment_datetime,
            'lab_id' => $this->lab_id,
            //'is_paid' => $this->is_paid,
            'lab_amount' => $this->lab_amount,
            'sample_collection_time' => $this->sample_collection_time,
            //'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'kid_id' => $this->kid_id,
            'is_cancelled' => $this->is_cancelled,
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
                ->andFilterWhere(['like', 'type', $this->type])
                ->andFilterWhere(['like', 'paymode', $this->paymode])
                ->andFilterWhere(['like', 'sample_collection_address', $this->sample_collection_address])
                ->andFilterWhere(['like', 'prescription_file', $this->prescription_file]);

        //echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

}
