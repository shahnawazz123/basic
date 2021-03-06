<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Faq;

/**
 * FaqSearch represents the model behind the search form about `app\models\Faq`.
 */
class FaqSearch extends Faq
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['faq_id'], 'integer'],
            [['question_en', 'question_ar', 'answer_en', 'answer_ar'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Faq::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['faq_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'faq_id' => $this->faq_id,
        ]);

        $query->andFilterWhere(['like', 'question_en', $this->question_en])
            ->andFilterWhere(['like', 'question_ar', $this->question_ar])
            ->andFilterWhere(['like', 'answer_en', $this->answer_en])
            ->andFilterWhere(['like', 'answer_ar', $this->answer_ar]);

        return $dataProvider;
    }
}
