<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Labs;

/**
 * LabsSearch represents the model behind the search form of `app\models\Labs`.
 */
class LabsSearch extends Labs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lab_id', 'is_active', 'is_deleted', 'consultation_time_interval', 'max_booking_per_lot'], 'integer'],
            [['name_en', 'name_ar', 'email', 'password', 'created_at', 'updated_at', 'start_time', 'end_time'], 'safe'],
            [['home_test_charge', 'admin_commission'], 'number'],
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
        $query = Labs::find();

        // add conditions that should always apply here
  $query->where(["is_deleted" => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lab_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lab_id' => $this->lab_id,
            'home_test_charge' => $this->home_test_charge,
            'admin_commission' => $this->admin_commission,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'consultation_time_interval' => $this->consultation_time_interval,
            'max_booking_per_lot' => $this->max_booking_per_lot,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password]);

        return $dataProvider;
    }
}
