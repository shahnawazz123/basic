<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Promotions;

/**
 * PromotionsSearch represents the model behind the search form of `app\models\Promotions`.
 */
class PromotionsSearch extends Promotions
{
    public $date_range;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'promo_count', 'discount', 'shipping_included', 'is_deleted'], 'integer'],
            [['title_en', 'title_ar', 'code', 'start_date', 'end_date', 'promo_type', 'promo_for', 'registration_start_date', 'registration_end_date', 'is_active'], 'safe'],
            [['minimum_order'], 'number'],
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
        $query = Promotions::find()
            ->with('promotionDoctors')
            ->andwhere(['is_deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['promotion_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'promotion_id' => $this->promotion_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'promo_count' => $this->promo_count,
            'discount' => $this->discount,
            'is_active' => $this->is_active,
            'minimum_order' => $this->minimum_order,
            'shipping_included' => $this->shipping_included,
            'registration_start_date' => $this->registration_start_date,
            'registration_end_date' => $this->registration_end_date,
            'is_deleted' => $this->is_deleted,
        ]);
        /*if (!empty($this->date_range)) {
            $dateRange = explode(' to ', str_replace("/", "-", $this->date_range));
            $query->andFilterWhere(['BETWEEN', "DATE(`promotions`.`start_date`)", date("Y-m-d", strtotime($dateRange[0])), date("Y-m-d", strtotime(trim($dateRange[1])))]);
        }*/
        $query->andFilterWhere(['like', 'title_en', $this->title_en])
            ->andFilterWhere(['like', 'title_ar', $this->title_ar])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'promo_type', $this->promo_type])
            ->andFilterWhere(['like', 'promo_for', $this->promo_for]);

        return $dataProvider;
    }
}
