<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Currencies;

/**
 * CurrenciesSearch represents the model behind the search form about `app\models\Currencies`.
 */
class CurrenciesSearch extends Currencies {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['currency_id'], 'integer'],
            [['name_en', 'name_ar', 'code_en', 'code_ar'], 'safe'],
            [['currency_rate'], 'number'],
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
        $query = Currencies::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['is_base_currency' => SORT_DESC, 'code_en' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'currency_id' => $this->currency_id,
            'currency_rate' => $this->currency_rate,
            'code_en' => $this->code_en,
            'code_ar' => $this->code_ar,
        ]);

        $query->andFilterWhere(['like', 'name_en', $this->name_en])
                ->andFilterWhere(['like', 'name_ar', $this->name_ar])
                ->andFilterWhere(['like', 'code_ar', $this->code_ar])
                ->andFilterWhere(['like', 'code_en', $this->code_en]);

        return $dataProvider;
    }

}
