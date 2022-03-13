<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DoctorAppointments;

/**
 * DoctorAppointmentSearch represents the model behind the search form of `app\models\DoctorAppointments`.
 */
class DoctorAppointmentSearch extends DoctorAppointments
{
    public $type;
    public $today;
    public $week;
    public $month;
    public $year;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_id', 'user_id', 'doctor_id', 'is_deleted', 'kid_id', 'is_cancelled', 'is_paid', 'has_gone_payment', 'duration'], 'integer'],
            [['name', 'email', 'phone_number', 'consultation_type', 'appointment_datetime', 'prescription_file', 'created_at', 'updated_at', 'payment_initiate_time', 'is_completed', 'type', 'appointment_number', 'month', 'year', 'today', 'week'], 'safe'],
            [['consultation_fees', 'discount', 'sub_total', 'amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = DoctorAppointments::find()
            ->where(['doctor_appointments.is_deleted' => 0])
            ->join('LEFT JOIN', 'doctors', 'doctors.doctor_id=doctor_appointments.doctor_id')
            ->join('LEFT JOIN', 'clinics', 'clinics.clinic_id=doctors.clinic_id')
            //->andWhere(['!=', 'is_paid', 0]);
            ->andWhere(['is_paid' => 1]);

        // add conditions that should always apply here

        if (\Yii::$app->session['_eyadatAuth'] == 3) {
            //echo "<pre>";print_r(Yii::$app->user->identity);die;
            $query->andwhere(['doctor_appointments.doctor_id' => Yii::$app->user->identity->doctor_id]);
        }


        /* ------------------------------- Translator ------------------------------- */
        if (\Yii::$app->session['_eyadatAuth'] == 8) {
            $query->andwhere(['doctor_appointments.translator_id' => Yii::$app->user->identity->translator_id]);
        }
        /* ------------------------------- Translator ------------------------------- */



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
            'translator_id' => $this->translator_id,
        ]);

        $query->andFilterWhere(['like', 'doctor_appointments.name', $this->name])
            ->andFilterWhere(['like', 'doctor_appointments.email', $this->email])
            ->andFilterWhere(['like', 'doctor_appointments.phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'doctor_appointments.consultation_type', $this->consultation_type])
            ->andFilterWhere(['like', 'doctor_appointments.prescription_file', $this->prescription_file]);

        return $dataProvider;
    }
}
