<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form about `app\models\Product`.
 */
class ProductSearch extends Product
{

    public $status_id;
    public $new;
    public $no_stock;
    public $category_id;
    public $page_size;
    public $no_stock_active;
    public $no_stock_inactive;
    public $attribute_value_search;
    public $total_amount;
    public $total_quantity;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['product_id', 'admin_id', 'sort_order', 'base_currency_id', 'remaining_quantity', 'is_featured', 'is_active', 'is_deleted', 'views', 'brand_id', 'attribute_set_id', 'is_trending', 'no_stock', 'no_stock_active', 'no_stock_inactive', 'category_id'], 'integer'],
            [
                [
                    'name_en',
                    'name_ar',
                    'name',
                    'short_description_en',
                    'short_description_ar',
                    'description_en',
                    'description_ar',
                    'SKU',
                    'barcode',
                    'supplier_barcode',
                    'posted_date',
                    'updated_date',
                    'new_from_date',
                    'new_to_date',
                    'meta_title_en',
                    'meta_title_ar',
                    'meta_keywords_en',
                    'meta_keywords_ar',
                    'meta_description_en',
                    'meta_description_ar',
                    'status_id',
                    'is_featured',
                    'is_best_seller',
                    'type',
                    'new',
                    'page_size',
                    'pharmacy_id',
                    'attribute_value_search',
                    'total_amount',
                    'total_quantity',
                ], 'safe'],
            [['regular_price', 'final_price', 'cost_price'], 'number'],
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
        $this->load($params);
        $query = Product::find()
                ->where(['product.is_deleted' => 0]);
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'product.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }
        if ($this->type == 'G') {
            $query->andWhere(['product.type' => 'G']);
        } elseif ($this->type == 'S') {
            $query->andWhere(['AND',
                ['=', 'show_as_individual', 1],
                ['=', 'product.type', 'S']
            ]);
        } elseif ($this->type == 'A') {
            $query->andWhere([
                'OR',
                [
                    'AND',
                    [
                        'AND',
                        ['=', 'show_as_individual', 1],
                        ['=', 'product.type', 'S']
                    ],
                    ['=', 'product.type', 'S']
                ],
                ['=', 'product.type', 'G'],
            ]);
        }
        if (isset($this->page_size) && $this->page_size != "" && is_numeric($this->page_size)) {
            $pageSize = $this->page_size;
        } else {
            $pageSize = 20;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort_order' => SORT_DESC, 'product_id' => SORT_DESC]],
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
            'product_id' => $this->product_id,
            'admin_id' => $this->admin_id,
            'sort_order' => $this->sort_order,
            'regular_price' => $this->regular_price,
            'final_price' => $this->final_price,
            'cost_price' => $this->cost_price,
            'base_currency_id' => $this->base_currency_id,
            'remaining_quantity' => $this->remaining_quantity,
            'posted_date' => $this->posted_date,
            'updated_date' => $this->updated_date,
            'is_featured' => $this->is_featured,
            'is_trending' => $this->is_trending,
            'is_best_seller' => $this->is_best_seller,
            'product.is_active' => $this->is_active,
            'views' => $this->views,
            'new_from_date' => $this->new_from_date,
            'new_to_date' => $this->new_to_date,
            'brand_id' => $this->brand_id,
            'attribute_set_id' => $this->attribute_set_id,
            'temp.product_status_id' => $this->status_id,
            'pharmacy_id' => $this->pharmacy_id,
        ]);

        $query->andFilterWhere(['like', 'product.name_en', $this->name_en])
                ->andFilterWhere(['like', 'product.name_ar', $this->name_ar])
                ->andFilterWhere(['like', 'short_description_en', $this->short_description_en])
                ->andFilterWhere(['like', 'short_description_ar', $this->short_description_ar])
                ->andFilterWhere(['like', 'description_en', $this->description_en])
                ->andFilterWhere(['like', 'description_ar', $this->description_ar])
                ->andFilterWhere(['like', 'SKU', $this->SKU])
                ->andFilterWhere(['like', 'barcode', $this->barcode])
                ->andFilterWhere(['like', 'supplier_barcode', $this->supplier_barcode])
                ->andFilterWhere(['like', 'meta_title_en', $this->meta_title_en])
                ->andFilterWhere(['like', 'meta_title_ar', $this->meta_title_ar])
                ->andFilterWhere(['like', 'meta_keywords_en', $this->meta_keywords_en])
                ->andFilterWhere(['like', 'meta_keywords_ar', $this->meta_keywords_ar])
                ->andFilterWhere(['like', 'meta_description_en', $this->meta_description_en])
                ->andFilterWhere(['like', 'meta_description_ar', $this->meta_description_ar]);

        if (!empty($this->category_id)) {
            $ids = [$this->category_id];
            $category = \app\models\Category::findOne($this->category_id);
            if (!empty($category)) {
                $children = $category->children()->all();
                foreach ($children as $child) {
                    $ids[] = $child->category_id;
                }
            }
            $query->join("left join", 'product_categories', 'product.product_id = product_categories.product_id');
            $query->andWhere(['product_categories.category_id' => $ids]);
        }

        if (!empty($this->no_stock) && $this->no_stock == 1) {
            $query->andWhere(['<=', 'remaining_quantity', \Yii::$app->params['bufferQty']]);
        }

        if (!empty($this->no_stock_active) && $this->no_stock_active == 1) {
            $query->andWhere(['is_active' => 1]);
        }

        if (!empty($this->no_stock_inactive) && $this->no_stock_inactive == 1) {
            $query->andWhere(['is_active' => 0]);
        }

        if (!empty($this->attribute_value_search)) {
            $query->join('LEFT JOIN', 'product_attribute_values', 'product_attribute_values.product_id = product.product_id')
                    ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                    ->andFilterWhere([
                        'OR',
                        ['like', 'attribute_values.value_en', $this->attribute_value_search],
                        ['like', 'attribute_values.value_ar', $this->attribute_value_search]
            ]);
        }

        if (!empty($this->name)) {
            $query->andWhere([
                'OR',
                ['LIKE', 'product.name_en', $this->name],
                ['LIKE', 'product.name_ar', $this->name]
            ]);
        }

        if (!empty($this->new)) {
            $query->andWhere(['<=', 'start_date', date('Y-m-d')])->andWhere(['>=', 'end_date', date('Y-m-d')]);
        }
       // echo $query->createCommand()->rawSql;die;

        return $dataProvider;
    }

    public function stockDifference($params) {
        $this->load($params);

        $query = Product::find()
                ->select(['product.product_id', 'SKU', 'sum(`product_stocks`.`quantity`) as quantity', 'product.remaining_quantity', 'product_stocks.product_stock_id'])
                ->join('LEFT JOIN', 'product_stocks', 'product.product_id = product_stocks.product_id')
                ->groupBy(['product.product_id'])
                ->andFilterHaving([
                    'OR',
                    ['!=', 'quantity', new \yii\db\Expression('`remaining_quantity`')],
                    [
                        'AND',
                        ['IS', 'product_stocks.product_stock_id', new \yii\db\Expression('NULL')],
                        ['>', 'product.remaining_quantity', 0]
                    ]
                ])
                ->orderBy(['quantity' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['product_id' => SORT_ASC]],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function export($params, $meta_export = 0) {
        $query = Product::find()
                ->select([
                    'product.*',
                ])
                ->where(['product.is_deleted' => 0])
                ->orderBy(['sort_order' => SORT_DESC, 'product_id' => SORT_DESC]);
        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'product.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }
        // grid filtering conditions
        $this->load($params);
        $query->andFilterWhere([
            'product.product_id' => $this->product_id,
            'admin_id' => $this->admin_id,
            'sort_order' => $this->sort_order,
            'regular_price' => $this->regular_price,
            'final_price' => $this->final_price,
            'cost_price' => $this->cost_price,
            'base_currency_id' => $this->base_currency_id,
            'remaining_quantity' => $this->remaining_quantity,
            'posted_date' => $this->posted_date,
            'updated_date' => $this->updated_date,
            'is_featured' => $this->is_featured,
            'manufacturer_id' => $this->manufacturer_id,
            'product.is_active' => $this->is_active,
            'views' => $this->views,
            'new_from_date' => $this->new_from_date,
            'new_to_date' => $this->new_to_date,
            'brand_id' => $this->brand_id,
            'attribute_set_id' => $this->attribute_set_id,
            'temp.product_status_id' => $this->status_id,
            'pharmacy_id' => $this->pharmacy_id,
        ]);

        $query->andFilterWhere(['like', 'product.name_en', $this->name_en])
                ->andFilterWhere(['like', 'product.name_ar', $this->name_ar])
                ->andFilterWhere(['like', 'short_description_en', $this->short_description_en])
                ->andFilterWhere(['like', 'short_description_ar', $this->short_description_ar])
                ->andFilterWhere(['like', 'description_en', $this->description_en])
                ->andFilterWhere(['like', 'description_ar', $this->description_ar])
                ->andFilterWhere(['like', 'SKU', $this->SKU])
                ->andFilterWhere(['like', 'barcode', $this->barcode])
                ->andFilterWhere(['like', 'supplier_barcode', $this->supplier_barcode]);

        if (!empty($this->name)) {
            $query->andWhere([
                'OR',
                ['LIKE', 'product.name_en', $this->name],
                ['LIKE', 'product.name_ar', $this->name]
            ]);
        }
        $result = $query->all();
        return $result;
    }

    public function searchPrescriptionMedicine($params) {
        $this->load($params);
        $query = Product::find()
                ->where(['product.is_deleted' => 0,'product.pharmacy_id'=>$this->pharmacy_id]);
        if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'product.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }
        if ($this->type == 'G') {
            $query->andWhere(['product.type' => 'G']);
        } elseif ($this->type == 'S') {
            $query->andWhere(['AND',
                ['=', 'show_as_individual', 1],
                ['=', 'product.type', 'S']
            ]);
        } elseif ($this->type == 'A') {
            $query->andWhere([
                'OR',
                [
                    'AND',
                    [
                        'AND',
                        ['=', 'show_as_individual', 1],
                        ['=', 'product.type', 'S']
                    ],
                    ['=', 'product.type', 'S']
                ],
                ['=', 'product.type', 'G'],
            ]);
        }
        if (isset($this->page_size) && $this->page_size != "" && is_numeric($this->page_size)) {
            $pageSize = $this->page_size;
        } else {
            $pageSize = 20;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sort_order' => SORT_DESC, 'product_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['>', 'remaining_quantity', 0]);
        if (!empty($this->name)) {
            $query->andWhere([
                'OR',
                ['LIKE', 'product.name_en', $this->name],
                ['LIKE', 'product.name_ar', $this->name]
            ]);
        }

       //echo $query->createCommand()->rawSql;die;

        return $dataProvider;
    }
}
