<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Doctors;

/**
 * DoctorsSearch represents the model behind the search form of `app\models\Doctors`.
 */
class DoctorsSearch extends Doctors
{
    public $page_size;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doctor_id', 'years_experience', 'consultation_time_online', 'consultation_time_offline', 'clinic_id', 'is_active', 'is_featured', 'is_deleted', 'category_id', 'insurance_id', 'clinic_id'], 'integer'],
            [['doctor_id', 'years_experience', 'consultation_time_online', 'consultation_time_offline', 'clinic_id', 'is_active', 'is_featured', 'is_deleted', 'category_id', 'insurance_id', 'clinic_id', 'page_size'], 'integer'],
            [['name_en', 'name_ar', 'email', 'password', 'qualification', 'image', 'gender', 'type', 'created_at', 'updated_at'], 'safe'],
            [['consultation_price_regular', 'consultation_price_final'], 'number'],
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
        $query = Doctors::find()
            /*->join('LEFT join', 'doctor_categories', 'doctor_categories.doctor_id = doctors.doctor_id')
                ->join('LEFT join', 'doctor_insurances', 'doctor_insurances.doctor_id = doctors.doctor_id')
                ->join('LEFT join', 'clinics', 'clinics.clinic_id = doctors.clinic_id');*/
            ->join('LEFT JOIN', 'doctor_categories', 'doctor_categories.doctor_id = doctors.doctor_id')
            ->join('LEFT JOIN', 'doctor_symptoms', 'doctor_symptoms.doctor_id = doctors.doctor_id')
            ->join('LEFT JOIN', 'category', 'doctor_categories.category_id = category.category_id')
            ->join('LEFT JOIN', 'symptoms', 'doctor_symptoms.symptom_id = symptoms.symptom_id');



        $query->andwhere(['doctors.is_deleted' => 0]);
        $query->groupBy('doctors.doctor_id');
        $query->orderBy(['created_at' => SORT_DESC]);

        // add conditions that should always apply here
        if (isset($this->page_size) && $this->page_size != "" && is_numeric($this->page_size)) {
            $pageSize = $this->page_size;
        } else {
            $pageSize = 20;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort_order' => SORT_ASC]],
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (\Yii::$app->session['_eyadatAuth'] == 2) {
            $query->andFilterWhere(['=', 'doctors.clinic_id', Yii::$app->user->identity->clinic_id]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'doctors.doctor_id' => $this->doctor_id,
            'doctor_categories.category_id' => $this->category_id,
            // 'doctors.insurance_id' => $this->insurance_id,
            'doctors.years_experience' => $this->years_experience,
            'doctors.consultation_time_online' => $this->consultation_time_online,
            'doctors.consultation_time_offline' => $this->consultation_time_offline,
            'doctors.clinic_id' => $this->clinic_id,
            'doctors.consultation_price_regular' => $this->consultation_price_regular,
            'doctors.consultation_price_final' => $this->consultation_price_final,
            'doctors.is_active' => $this->is_active,
            'doctors.is_featured' => $this->is_featured,
            'doctors.is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'doctors.gender' => $this->gender,
        ]);

        $query->andFilterWhere(['like', 'doctors.name_en', $this->name_en])
            ->andFilterWhere(['like', 'doctors.name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'doctors.email', $this->email])
            ->andFilterWhere(['like', 'doctors.password', $this->password])
            ->andFilterWhere(['like', 'doctors.qualification', $this->qualification])
            ->andFilterWhere(['like', 'doctors.image', $this->image])
            ->andFilterWhere(['like', 'doctors.gender', $this->gender])
            ->andFilterWhere(['like', 'doctors.type', $this->type])
            ->andFilterWhere(['like', 'doctors.updated_at', $this->updated_at]);

        if (!empty($this->insurance_id)) {
            $ids = [$this->insurance_id];
            /*$insurance = \app\models\Insurances::findOne($this->insurance_id);
            if (!empty($insurance)) {
                foreach ($insurance as $row) {
                    $ids[] = $row->insurance_id;
                }
            }*/
            $query->join("left join", 'doctor_insurances', 'doctors.doctor_id = doctor_insurances.doctor_id');
            $query->andWhere(['doctor_insurances.insurance_id' => $ids]);
        }
        // $query->orderBy(['sort_order' => SORT_ASC]);
        $query->orderBy(['doctor_id' => SORT_DESC]);
        //echo $query->createCommand()->rawSql; exit;
        return $dataProvider;
    }
}
