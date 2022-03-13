<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DoctorPrescriptions;

/**
 * DoctorPrescriptionsSearch represents the model behind the search form of `app\models\DoctorPrescriptions`.
 */
class DoctorPrescriptionsSearch extends DoctorPrescriptions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_appointment_prescription_id', 'doctor_appointment_id', 'total_usage', 'referred_pharmacy_id', 'is_deleted', 'is_active'], 'integer'],
            [['created_at'], 'safe'],
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
        $query = DoctorPrescriptions::find()->where(['is_deleted'=>0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['doctor_appointment_prescription_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'doctor_appointment_prescription_id' => $this->doctor_appointment_prescription_id,
            'doctor_appointment_id' => $this->doctor_appointment_id,
            'total_usage' => $this->total_usage,
            'referred_pharmacy_id' => $this->referred_pharmacy_id,
            'is_deleted' => $this->is_deleted,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
