<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form about `app\models\Users`.
 */
class UsersSearch extends Users
{
    public $ios_user, $android_user, $website_user;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_phone_verified', 'is_email_verified', 'is_social_register', 'push_enabled', 'newsletter_subscribed', 'is_deleted','is_phone_verified'], 'integer'],
            [['first_name', 'last_name', 'user_name', 'gender', 'dob', 'email', 'password', 'image', 'phone', 'code', 'social_register_type', 'device_token', 'device_type', 'device_model', 'app_version', 'os_version', 'create_date', 'ios_user', 'android_user', 'website_user'], 'safe'],
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
    public function search($params, $is_export = 0)
    {
        $query = Users::find()
            ->select(['users.*', 'CONCAT(users.first_name," ", users.last_name) as user_name'])
            ->where(['is_deleted' => 0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['user_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->ios_user)){
            $query->andWhere(['users.device_type' => 'I']);
        }

        if(!empty($this->android_user)){
            $query->andWhere(['or', ['=', 'users.device_type', 'A'], ['=', 'users.device_type', 'user']]);
        }

        if(!empty($this->website_user)){
            $query->andWhere(['users.device_type' => ['W', null, '']]);
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'dob' => $this->dob,
            'is_phone_verified' => $this->is_phone_verified,
            'is_email_verified' => $this->is_email_verified,
            'is_social_register' => $this->is_social_register,
            'push_enabled' => $this->push_enabled,
            'newsletter_subscribed' => $this->newsletter_subscribed,
            //'is_guest' => $this->is_guest,
            'is_phone_verified' => $this->is_phone_verified,
            // 'is_deleted' => $this->is_deleted,
            'create_date' => $this->create_date,
        ]);


        if(!empty($this->user_name)){
            $userNameArr = explode(' ',trim($this->user_name));

            if(!empty($userNameArr)){
                foreach ($userNameArr as $name){
                    $query->andFilterWhere(['OR', ['like', 'first_name', $name], ['like', 'last_name', $name]]);
                }
            }
        }

        $query
            //->andFilterWhere(['like', 'first_name', $this->first_name])
            //->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'social_register_type', $this->social_register_type])
            ->andFilterWhere(['like', 'device_token', $this->device_token])
            ->andFilterWhere(['like', 'device_type', $this->device_type])
            ->andFilterWhere(['like', 'device_model', $this->device_model])
            ->andFilterWhere(['like', 'app_version', $this->app_version])
            ->andFilterWhere(['like', 'os_version', $this->os_version]);

        if($is_export) {
            $result = $query->all();
            return $result;
        }

        return $dataProvider;
    }

    public function abandonedCartUsers($params)
    {
        $query = Users::find()
            ->select(['users.*', 'orders.order_id as order_id', 'CONCAT(users.first_name," ", users.last_name) as user_name'])
            ->where(['is_deleted' => 0])
            ->join('LEFT JOIN', 'orders', 'users.user_id = orders.user_id')
            ->join('LEFT JOIN', 'shop_orders', 'orders.order_id = shop_orders.order_id')
            ->join('LEFT JOIN', 'order_items', 'shop_orders.shop_order_id = order_items.shop_order_id')
            ->andWhere(['is_processed' => [0, 2]])
            ->andWhere(['IS NOT', 'order_items.quantity', new \yii\db\Expression('NULL')])
            ->groupBy('orders.order_id')
            ->orderBy(['order_items.order_item_id' => SORT_DESC])
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['user_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'dob' => $this->dob,
            'is_phone_verified' => $this->is_phone_verified,
            'is_phone_verified' => $this->is_phone_verified,
            'is_email_verified' => $this->is_email_verified,
            'is_social_register' => $this->is_social_register,
            'push_enabled' => $this->push_enabled,
            'newsletter_subscribed' => $this->newsletter_subscribed,
            'is_deleted' => $this->is_deleted,
            'create_date' => $this->create_date,
        ]);

        if(!empty($this->user_name)){
            $userNameArr = explode(' ',trim($this->user_name));

            if(!empty($userNameArr)){
                foreach ($userNameArr as $name){
                    $query->andFilterWhere(['OR', ['like', 'first_name', $name], ['like', 'last_name', $name]]);
                }
            }
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'social_register_type', $this->social_register_type])
            ->andFilterWhere(['like', 'device_token', $this->device_token])
            ->andFilterWhere(['like', 'device_type', $this->device_type])
            ->andFilterWhere(['like', 'device_model', $this->device_model])
            ->andFilterWhere(['like', 'app_version', $this->app_version])
            ->andFilterWhere(['like', 'os_version', $this->os_version]);


        return $dataProvider;
    }
}
