<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DoctorAppointmentMedicines;

/**
 * DoctorAppointmentMedicinesSearch represents the model behind the search form of `app\models\DoctorAppointmentMedicines`.
 */
class DoctorAppointmentMedicinesSearch extends DoctorAppointmentMedicines
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_medicine_id', 'doctor_appointment_prescription_id', 'product_id', 'qty'], 'integer'],
            [['instruction'], 'safe'],
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
        $query = DoctorAppointmentMedicines::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['doctor_appointment_medicine_id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'doctor_appointment_medicine_id' => $this->doctor_appointment_medicine_id,
            'doctor_appointment_prescription_id' => $this->doctor_appointment_prescription_id,
            'product_id' => $this->product_id,
            'qty' => $this->qty,
        ]);

        $query->andFilterWhere(['like', 'instruction', $this->instruction]);

        return $dataProvider;
    }
}
