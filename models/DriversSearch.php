<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Drivers;

/**
 * DriversSearch represents the model behind the search form of `app\models\Drivers`.
 */
class DriversSearch extends Drivers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['driver_id', 'push_enabled', 'is_active', 'is_deleted'], 'integer'],
            [['email', 'password', 'phone', 'location', 'device_token', 'device_type', 'device_model', 'app_version', 'os_version', 'image', 'name_en', 'name_ar', 'civil_id_number', 'license_number'], 'safe'],
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
        $query = Drivers::find()
                ->andwhere(['is_deleted'=>0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['driver_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'driver_id' => $this->driver_id,
            'push_enabled' => $this->push_enabled,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'device_token', $this->device_token])
            ->andFilterWhere(['like', 'device_type', $this->device_type])
            ->andFilterWhere(['like', 'device_model', $this->device_model])
            ->andFilterWhere(['like', 'app_version', $this->app_version])
            ->andFilterWhere(['like', 'os_version', $this->os_version])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'name_ar', $this->name_ar])
            ->andFilterWhere(['like', 'civil_id_number', $this->civil_id_number])
            ->andFilterWhere(['like', 'license_number', $this->license_number]);

        return $dataProvider;
    }
}
