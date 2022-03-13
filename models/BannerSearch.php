<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Banner;

/**
 * BannerSearch represents the model behind the search form of `app\models\Banner`.
 */
class BannerSearch extends Banner
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['banner_id', 'link_id', 'is_active', 'is_deleted', 'sort_order'], 'integer'],
            [['image_ar', 'image_en', 'name_en', 'name_ar', 'sub_title_en', 'sub_title_ar', 'link_type', 'url', 'position'], 'safe'],
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
        $query = Banner::find();
        $query->andwhere(['is_deleted' => 0])->orderBy(['banner_id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort_order' => SORT_ASC, 'banner_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'banner_id' => $this->banner_id,
            'position' => $this->position,
            'link_id' => $this->link_id,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'image_ar', $this->image_ar])
            ->andFilterWhere(['like', 'image_en', $this->image_en])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'sub_title_en', $this->sub_title_en])
            ->andFilterWhere(['like', 'sub_title_ar', $this->sub_title_ar])
            ->andFilterWhere(['like', 'link_type', $this->link_type])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
