<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pharmacies;

/**
 * PharmaciesSearch represents the model behind the search form of `app\models\Pharmacies`.
 */
class PharmaciesSearch extends Pharmacies
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_id', 'minimum_order', 'is_free_delivery', 'is_featured', 'enable_login', 'governorate_id', 'area_id', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar', 'image_en', 'image_ar', 'latlon', 'email', 'password', 'block', 'street', 'building', 'floor', 'shop_number', 'created_at', 'updated_at'], 'safe'],
            [['admin_commission','delivery_charge'], 'number'],
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
        $query = Pharmacies::find();
        $query->where(['is_deleted'=>0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pharmacy_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pharmacy_id' => $this->pharmacy_id,
            'minimum_order' => $this->minimum_order,
            'is_free_delivery' => $this->is_free_delivery,
            'is_featured' => $this->is_featured,
            'enable_login' => $this->enable_login,
            'governorate_id' => $this->governorate_id,
            'area_id' => $this->area_id,
            'admin_commission' => $this->admin_commission,
            'delivery_charge' => $this->delivery_charge,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'image_en', $this->image_en])
            ->andFilterWhere(['like', 'image_ar', $this->image_ar])
            ->andFilterWhere(['like', 'latlon', $this->latlon])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'block', $this->block])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'building', $this->building])
            ->andFilterWhere(['like', 'floor', $this->floor])
            ->andFilterWhere(['like', 'shop_number', $this->shop_number]);

        return $dataProvider;
    }
}
