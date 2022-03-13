<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PharmacyLocations;

/**
 * PharmacyLocationsSearch represents the model behind the search form of `app\models\PharmacyLocations`.
 */
class PharmacyLocationsSearch extends PharmacyLocations
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_location_id', 'pharmacy_id', 'governorate_id', 'area_id', 'is_deleted'], 'integer'],
            [['latlon', 'block', 'street', 'building', 'name_en', 'name_ar'], 'safe'],
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
        $query = PharmacyLocations::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pharmacy_location_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pharmacy_location_id' => $this->pharmacy_location_id,
            'pharmacy_id' => $this->pharmacy_id,
            'governorate_id' => $this->governorate_id,
            'area_id' => $this->area_id,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'latlon', $this->latlon])
            ->andFilterWhere(['like', 'block', $this->block])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'building', $this->building])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar]);

        return $dataProvider;
    }
}
