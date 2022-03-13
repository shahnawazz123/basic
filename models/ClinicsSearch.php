<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Clinics;

/**
 * ClinicsSearch represents the model behind the search form of `app\models\Clinics`.
 */
class ClinicsSearch extends Clinics
{
    public $page_size;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clinic_id', 'governorate_id', 'area_id','is_featured', 'is_active', 'is_deleted','category_id','insurance_id','page_size'], 'integer'],
            [['name_en', 'name_ar', 'image_en', 'image_ar', 'latlon', 'type', 'block', 'street', 'building', 'email', 'password', 'created_at', 'updated_at'], 'safe'],
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

        $this->load($params);
        $query = Clinics::find()
                ->join('LEFT JOIN', 'clinic_categories', 'clinic_categories.clinic_id = clinics.clinic_id')
                ->join('LEFT JOIN', 'clinic_insurances', 'clinic_insurances.clinic_id = clinics.clinic_id');
        
        $query->andwhere(['clinics.is_deleted'=>0]);
        $query->groupBy('clinics.clinic_id');

        // add conditions that should always apply here

        // add conditions that should always apply here
        if(isset($this->page_size) && $this->page_size!="" && is_numeric($this->page_size)){
            $pageSize = $this->page_size;
        }
        else{
            $pageSize = 20;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['clinic_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
 
        // grid filtering conditions
        $query->andFilterWhere([
            'clinic_id' => $this->clinic_id,
            'clinic_categories.category_id' => $this->category_id,
            'insurance_id' => $this->insurance_id,
            'governorate_id' => $this->governorate_id,
            'area_id' => $this->area_id,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'image_en', $this->image_en])
            ->andFilterWhere(['like', 'image_ar', $this->image_ar])
            ->andFilterWhere(['like', 'latlon', $this->latlon])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'block', $this->block])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'building', $this->building])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password]);
            //echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }
}
