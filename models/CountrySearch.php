<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Country;

/**
 * CountrySearch represents the model behind the search form about `app\models\Country`.
 */
class CountrySearch extends Country {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['country_id', 'is_active'], 'integer'],
            [['name_en', 'name_ar', 'nicename', 'iso', 'iso3', 'numcode', 'phonecode', 'is_deleted', 'is_cod_enable', 'shipping_cost', 'express_shipping_cost', 'cod_cost', 'vat', 'standard_shipping_cost_actual'], 'safe'],
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
    public function search($params) {
        $query = Country::find()->where(['is_deleted' => 0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['country_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'country_id' => $this->country_id,
            'is_cod_enable' => $this->is_cod_enable,
            'shipping_cost' => $this->shipping_cost,
            'standard_shipping_cost_actual' => $this->standard_shipping_cost_actual,
            'express_shipping_cost' => $this->express_shipping_cost,
            'cod_cost' => $this->cod_cost,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'name_en', $this->name_en])
                ->andFilterWhere(['like', 'name_ar', $this->name_ar])
                ->andFilterWhere(['like', 'nicename', $this->nicename])
                ->andFilterWhere(['like', 'iso', $this->iso])
                ->andFilterWhere(['like', 'iso3', $this->iso3])
                ->andFilterWhere(['like', 'currency_en', $this->currency_en])
                ->andFilterWhere(['like', 'currency_ar', $this->currency_ar])
                ->andFilterWhere(['like', 'numcode', $this->numcode])
                ->andFilterWhere(['like', 'phonecode', $this->phonecode])
                ->andFilterWhere(['like', 'is_deleted', $this->is_deleted]);

        return $dataProvider;
    }

}
