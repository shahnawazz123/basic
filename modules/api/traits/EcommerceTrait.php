<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\traits;

use app\models\AssociatedProducts;
use stdClass;
use Yii;
use app\helpers\AppHelper;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use app\models\Product;
use app\models\Country;
use app\models\ProductImages;
use app\models\Settings;

/**
 *
 * @author akram
 */
trait EcommerceTrait
{

    public $cacheDuration = 3600;
    public $imgUrl = '';


    private function distance($lat1, $lat2, $lon1, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    //put your code here
    public function actionPharmacies($lang, $page = 1, $per_page = 10, $sort_by = "", $is_free_delivery = "", $latlon = "")
    {

        $request = Yii::$app->request->bodyParams;
        $governorate_id = isset($request['governorate_id']) ? $request['governorate_id'] : '';
        $latlong = explode(',', $latlon);



        if ($latlon != "") {
            $query = \app\models\Pharmacies::find()
                ->select(['pharmacies.*'])
                ->join('LEFT JOIN', 'state', 'state.state_id = pharmacies.governorate_id')
                ->where(['pharmacies.is_deleted' => 0, 'pharmacies.is_active' => 1]);
        } else {
            $query = \app\models\Pharmacies::find()
                ->join('LEFT JOIN', 'state', 'state.state_id = pharmacies.governorate_id')
                ->where(['pharmacies.is_deleted' => 0, 'pharmacies.is_active' => 1]);
        }

        if (isset($is_free_delivery) && !empty($is_free_delivery)) {
            $query->andwhere(['pharmacies.is_free_delivery' => $is_free_delivery]);
        }

        /*if (isset($sort_by) && !empty($sort_by)) 
        {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by == 1) {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } else if ($sort_by == 2) {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            }
        }else{
            $query->addOrderBy(['pharmacies.pharmacy_id' => SORT_DESC]);
        }*/

        if ($governorate_id != '') {
            $query->andwhere(['pharmacies.governorate_id' => $governorate_id]);
        }



        $countQuery = clone $query;
        $governoateValueQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $per_page,
        ]);

        if (isset($sort_by) && !empty($sort_by)) {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by == 1) {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } else if ($sort_by == 2) {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            } else if ($sort_by == 3) {
                // $query->addOrderBy(["latlon" => SORT_ASC]);
                $query->addOrderBy(["latlon" => SORT_DESC]);
            }
        }/*else{
            $query->addOrderBy(['pharmacies.pharmacy_id' => SORT_DESC]);
        }*/

        if ($latlon != "" && $sort_by == "") {
            // $query->addOrderBy(["latlon" => SORT_ASC]);
            $query->addOrderBy(["latlon" => SORT_DESC]);
        }

        $pharmacies = $query->limit($per_page)->offset(($page - 1) * $per_page)->all();
        $pharmacies = $query->all();

        $result = [];

        $attributesValue = [];
        if (!empty($pharmacies)) {
            foreach ($pharmacies as $model) {
                // $pharmacy_latlon = explode(',', $model->latlon);
                $image = $this->noPreviewImg;
                if ($lang == 'ar') {
                    if ($model->image_ar != null) {
                        $image = $this->imgUrl . $model->image_ar;
                    }
                } else {
                    if ($model->image_en != null) {
                        $image = $this->imgUrl . $model->image_en;
                    }
                }

                $pharma_latlon = explode(',', $model->latlon);
                $d['id'] = $model->pharmacy_id;
                $d['title'] = $model->{"name_" . $lang};
                $d['image'] = $image;
                $d['is_featured'] = $model->is_featured;
                $d['latlon'] = preg_replace('/\s+/', '', $model->latlon);
                if ($model->latlon != null &&  $model->latlon != "") {
                    $d['distance'] = number_format($this->distance($pharma_latlon[0], $latlong[0], $pharma_latlon[1], $latlong[1], "K"), 2, ".", "");
                } else {
                    $d['distance'] = 0;
                }
                $d['shop_number'] = $model->shop_number;
                $d['governorate_id'] = $model->governorate_id;
                $d['governorate_name'] = !empty($model->governorate) ? $model->governorate->{"name_" . $lang} : "";
                $d['area_id'] = $model->area_id;
                $d['area_name'] = !empty($model->area) ? $model->area->{"name_" . $lang} : "";
                $d['block'] = $model->block;
                $d['street'] = $model->street;
                $d['building'] = $model->building;
                $d['floor'] = $model->floor;

                array_push($result, $d);
            }

            $governoateValueQuery->select([
                'DISTINCT(`state`.`state_id`)',
                'state.name_en',
                'state.name_ar'
            ])
                ->andFilterWhere(['IS NOT', 'pharmacies.governorate_id', new \yii\db\Expression('NULL')]);
            $governorate = $governoateValueQuery
                ->asArray()
                ->all();

            $tmp = [];
            foreach ($governorate as $state) {
                $c = [
                    'id'    => $state['state_id'],
                    'value' => $state['name_' . $lang],
                ];
                if (!empty($state['state_id']) && !empty($state['name_' . $lang]))
                    array_push($tmp, $c);
            }

            $governorateCollected = [
                'filter_name' => ($lang == 'en') ? 'Governorate' : 'محافظة',
                'filter_type' => 'Governorate',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $governorateCollected);
        }
        $this->data = [
            'pharmacies'        => $result,
            'total_pharmacies'  => $countQuery->count(),
            'total_pages'    => $pages->pageCount,
            'filter'         => $attributesValue,
        ];
        return $this->response_array();
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionAllCategories($lang = 'en')
    {
        $query = \app\models\Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1, 'lvl' => 0, 'hide_category_in_app' => 0, 'type' => 'P'])
            ->addOrderBy('root, lft');
        $models = $query->all();
        $result = [];
        foreach ($models as $k => $row) {
            $icon = ($lang == 'en') ? $row->icon : $row->icon_ar;
            $d['id'] = $row->category_id;
            $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $d['meta_title'] = ($lang == 'en') ? $row->meta_title_en : $row->meta_title_ar;
            $d['meta_description'] = ($lang == 'en') ? $row->meta_description_en : $row->meta_description_ar;
            $d['image'] = ($icon != null) ? $this->imgUrl . $icon : $this->noPreviewImg;
            $subcategory = $this->getSubCategories($row, $lang);
            $d['has_subcategory'] = (!empty($subcategory)) ? "Yes" : "No";
            $d['subcategories'] = $subcategory;
            array_push($result, $d);
        }
        $this->data = $result;
        return $this->response();
    }

    /**
     * @param $parent
     * @param $lang
     * @param string $limit
     * @param bool $showSub
     * @return array
     */
    private function getSubCategories($parent, $lang, $limit = "", $showSub = true)
    {
        $models = $parent->children(1)
            ->all();
        $result = [];
        foreach ($models as $k => $row) {
            if ($row->is_deleted == 0 && $row->is_active == 1 && $row->hide_category_in_app == 0) {
                $icon = ($lang == 'en') ? $row->icon : $row->icon_ar;
                $d['id'] = $row->category_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['image'] = (!empty($icon)) ? $this->imgUrl . $icon : "";
                $subcategory = $this->getSubCategories($row, $lang);
                $d['has_subcategory'] = (!empty($subcategory)) ? "Yes" : "No";
                if ($showSub !== false) {
                    $d['subcategories'] = $subcategory;
                }
                array_push($result, $d);
            }
        }
        return $result;
    }

    /**
     *
     * @param type $lang
     * @return mixed
     */
    public function actionSearch($q = null, $lang = 'en', $category_id = null, $user_id = null, $attribute_id = null, $brand_id = null, $manufacturer_id = null, $pharmacy_id = null, $price_range = null, $in_stock = 0, $page = 1, $per_page = 10, $is_featured = 0, $latest = 0, $best_selling = 0, $sort_by = "", $store = "")
    {
        $request = Yii::$app->request->bodyParams;
        $this->data = new \stdClass();
        $store = $this->getStoreDetails($store);
        $query = \app\models\Product::find()
            ->select([
                'product.*',
            ])
            ->join('left join', 'brands', 'brands.brand_id = product.brand_id')
            ->join('left join', 'product_attribute_values', 'product_attribute_values.product_id = product.product_id')
            ->join('left join', 'product_categories', 'product_categories.product_id = product.product_id')
            ->join('LEFT OUTER JOIN', 'store_products', 'store_products.product_id = product.product_id')
            ->where(['product.is_active' => 1, 'product.is_deleted' => 0])
            ->andWhere(['brands.is_active' => 1, 'brands.is_deleted' => 0]);
        if (isset($q) && $q != null) {
            $q = explode(' ', $q);
            foreach ($q as $search) {
                $query->andFilterWhere([
                    'OR',
                    ['LIKE', 'product.name_en', $search],
                    ['LIKE', 'product.name_ar', $search],
                    ['LIKE', 'product.short_description_en', $search],
                    ['LIKE', 'product.short_description_ar', $search],
                    ['LIKE', 'product.description_en', $search],
                    ['LIKE', 'product.description_ar', $search]
                ]);
            }
        }
        $postCategory = [];
        if (isset($request['category_id']) && !empty($request['category_id'])) {
            $category_id = $request['category_id'];
            $postCategory = explode(',', $request['category_id']);
        }
        if (isset($category_id) && $category_id != "") {
            if (!empty($postCategory)) {
                $ids = [];
                foreach ($postCategory as $pcat) {
                    $ids[] = $pcat;
                    $category = \app\models\Category::find()->where(['category_id' => $pcat, 'hide_category_in_app' => 0])->one();
                    if (!empty($category)) {
                        $children = $category->children()->all();
                        foreach ($children as $child) {
                            if ($child->is_active == 1 && $child->is_deleted == 0 && $child->hide_category_in_app == 0) {
                                $ids[] = $child->category_id;
                            }
                        }
                    }
                }
                $query->andWhere([
                    'OR',
                    ['IN', 'product_categories.category_id', $ids],
                    ['IS', 'product_categories.category_id', new \yii\db\Expression('NULL')],
                ]);
            } else {
                $ids[] = $category_id;
                $category = \app\models\Category::find()->where(['category_id' => $category_id, 'hide_category_in_app' => 0])->one();
                if (!empty($category)) {
                    $children = $category->children()->all();
                    foreach ($children as $child) {
                        if ($child->is_active == 1 && $child->is_deleted == 0 && $child->hide_category_in_app == 0) {
                            $ids[] = $child->category_id;
                        }
                    }
                    $query->andWhere([
                        'OR',
                        ['IN', 'product_categories.category_id', $ids],
                        ['IS', 'product_categories.category_id', new \yii\db\Expression('NULL')],
                    ]);
                } else {
                    $query->andWhere(['IN', 'product_categories.category_id', $ids]);
                }
            }
        }
        if (isset($attribute_id) && $attribute_id != null) {
            $query->andWhere('product_attribute_values.attribute_value_id IN(' . $attribute_id . ')');
        }
        $parentProductArr = [];
        if (isset($request['attribute_id']) && !empty($request['attribute_id'])) {
            $attributes = explode(',', $request['attribute_id']);
            $parentProductArr = AppHelper::getParentProducts($attributes);
        }
        if (isset($brand_id) && $brand_id != null) {
            $query->andWhere('product.brand_id = ' . $brand_id);
        }
        if (isset($manufacturer_id) && $manufacturer_id != null) {
            $query->andWhere('product.manufacturer_id = ' . $manufacturer_id);
        }
        if (isset($pharmacy_id) && $pharmacy_id != null) {
            $query->andWhere('product.pharmacy_id = ' . $pharmacy_id);
        }
        if (isset($request['brand_id']) && !empty($request['brand_id'])) {
            $brands = explode(',', $request['brand_id']);
            $query->andFilterWhere(['IN', 'product.brand_id', $brands]);
        }
        if (isset($request['pharmacy_id']) && !empty($request['pharmacy_id'])) {
            $pharmacies = explode(',', $request['pharmacy_id']);
            $query->andFilterWhere(['IN', 'product.pharmacy_id', $pharmacies]);
        }
        if (isset($price_range) && $price_range != null) {
            $priceRange = explode('-', $price_range);
            $query->andWhere('(convertPrice(`final_price`, `base_currency_id`, ' . $store['currency_id'] . ') BETWEEN "' . $priceRange[0] . '" AND "' . $priceRange[1] . '")');
        }
        if ($in_stock != 0) {
            $query->andWhere('product.remaining_quantity > 0');
        }
        if ($is_featured == 1) {
            $query->andWhere(['product.is_featured' => 1]);
        }

        if ($latest == 1) {
            $query->andWhere(['<=', 'start_date', date('Y-m-d')]);
            $query->andWhere(['>=', 'end_date', date('Y-m-d')]);
        }

        if ($best_selling == 1) {
            $query->andWhere(['product.is_best_seller' => 1]);
        }

        $query->groupBy('product.product_id');



        if (isset($sort_by) && !empty($sort_by)) {
            $nameColumn = ($lang == 'en') ? "name_en" : "name_ar";
            if ($sort_by === '1') {
                $query->orderBy(["product.is_featured" => SORT_DESC, 'product.sort_order' => SORT_DESC]);
            } elseif ($sort_by === '2') {
                $query->orderBy(["posted_date" => SORT_DESC]);
            } elseif ($sort_by === '3') {
                $query->orderBy(["convertPrice(`final_price`, `base_currency_id`, '" . $store['currency_id'] . "')" => SORT_DESC]);
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by === '4') {
                $query->orderBy(["convertPrice(`final_price`, `base_currency_id`, '" . $store['currency_id'] . "')" => SORT_ASC]);
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by === '5') {
                $query->orderBy(['((product.regular_price-product.final_price)*100)/product.regular_price' => SORT_DESC]);
            } elseif ($sort_by === '6') {
                $query->addOrderBy([$nameColumn => SORT_ASC]);
            } elseif ($sort_by === '7') {
                $query->addOrderBy([$nameColumn => SORT_DESC]);
            }
        }

        if (isset($latest) && $latest === 1) {
        } else if (isset($is_featured) && $is_featured === 1) {
            $query->andWhere(['product.is_featured' => $is_featured]);
            $query->orderBy(["product.sort_order" => SORT_DESC, 'product.product_id' => SORT_DESC]);
        } else if (isset($best_selling) && $best_selling == 1) {
            $query->orderBy(["product.sort_bs" => SORT_ASC]);
        }



        if (isset($category_id) && !empty($category_id)) {
            if ($sort_by === '6') {
                $query->orderBy(["product_categories.sort_order" => SORT_DESC, 'product_categories.product_id' => SORT_DESC]);
            } elseif ($sort_by === '7') {
                $query->orderBy(["product_categories.sort_order" => SORT_ASC, 'product_categories.product_id' => SORT_ASC]);
            }
        }

        if ($latest == 0 && $sort_by == "" && $is_featured == 0 && $best_selling == 0 && $category_id == null) {
            $query->orderBy(["product.sort_order" => SORT_DESC, 'product.product_id' => SORT_DESC]);
        }
        $attributeQuery = clone $query;
        $attributeValueQuery = clone $query;
        if ($latest == 0) {
            if (!empty($attributes)) {
                if (!empty($parentProductArr)) {
                    if (isset($request['attribute_id']) && $request['attribute_id'] != '') {
                        $query->andWhere([
                            'OR',
                            [
                                'AND',
                                ['=', 'show_as_individual', 1],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                        $attributeQuery->andWhere([
                            'OR',
                            [
                                'AND',
                                [
                                    'OR',
                                    ['=', 'show_as_individual', 1],
                                    ['=', 'show_as_individual', 0]
                                ],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                        $attributeValueQuery->andWhere([
                            'OR',
                            [
                                'AND',
                                [
                                    'OR',
                                    ['=', 'show_as_individual', 1],
                                    ['=', 'show_as_individual', 0]
                                ],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                    } else {
                        $query->andWhere([
                            'OR',
                            [
                                'AND',
                                ['=', 'show_as_individual', 1],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                        $attributeQuery->andWhere([
                            'OR',
                            [
                                'AND',
                                [
                                    'OR',
                                    ['=', 'show_as_individual', 1],
                                    ['=', 'show_as_individual', 0]
                                ],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                        $attributeValueQuery->andWhere([
                            'OR',
                            [
                                'AND',
                                [
                                    'OR',
                                    ['=', 'show_as_individual', 1],
                                    ['=', 'show_as_individual', 0]
                                ],
                                ['=', 'product.type', 'S'],
                                ['product_attribute_values.attribute_value_id' => $attributes]
                            ],
                            ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                            ['product.product_id' => $parentProductArr]
                        ]);
                    }
                } else {
                    $query->andWhere([
                        'OR',
                        [
                            'AND',
                            ['=', 'show_as_individual', 1],
                            ['=', 'product.type', 'S'],
                            ['product_attribute_values.attribute_value_id' => $attributes]
                        ],
                        ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                    ]);
                    $attributeQuery->andWhere([
                        'OR',
                        [
                            'AND',
                            //['=', 'show_as_individual', 1],
                            [
                                'OR',
                                ['=', 'show_as_individual', 1],
                                ['=', 'show_as_individual', 0]
                            ],
                            ['=', 'product.type', 'S'],
                            ['product_attribute_values.attribute_value_id' => $attributes]
                        ],
                        ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                    ]);
                    $attributeValueQuery->andWhere([
                        'OR',
                        [
                            'AND',
                            //['=', 'show_as_individual', 1],
                            [
                                'OR',
                                ['=', 'show_as_individual', 1],
                                ['=', 'show_as_individual', 0]
                            ],
                            ['=', 'product.type', 'S'],
                            ['product_attribute_values.attribute_value_id' => $attributes]
                        ],
                        ['AND', ['=', 'product.type', 'G'], ['product_attribute_values.attribute_value_id' => $attributes]],
                    ]);
                }
            } else {
                $query->andWhere([
                    'OR',
                    [
                        'AND',
                        ['=', 'show_as_individual', 1],
                        ['=', 'product.type', 'S']
                    ],
                    ['=', 'product.type', 'G'],
                ]);
                $attributeQuery->andWhere([
                    'OR',
                    [
                        'AND',
                        [
                            'OR',
                            ['=', 'show_as_individual', 1],
                            ['=', 'show_as_individual', 0]
                        ],
                        //['=', 'show_as_individual', 1],
                        ['=', 'product.type', 'S']
                    ],
                    ['=', 'product.type', 'G'],
                ]);
                $attributeValueQuery->andWhere([
                    'OR',
                    [
                        'AND',
                        [
                            'OR',
                            ['=', 'show_as_individual', 1],
                            ['=', 'show_as_individual', 0]
                        ],
                        //['=', 'show_as_individual', 1],
                        ['=', 'product.type', 'S']
                    ],
                    ['=', 'product.type', 'G'],
                ]);
            }
        }

        if (isset($store) && $store != null) {
            $query->andWhere([
                'OR',
                ['IN', 'store_products.store_id', $store['store_id']],
                ['IS', 'store_products.store_id', new \yii\db\Expression('NULL')],
            ]);
        }
        $brandValueQuery = clone $query;
        $categoryValueQuery = clone $query;
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $per_page,
        ]);
        $model = $query->limit($per_page)->offset(($page - 1) * $per_page)->all();
        $result = [];
        if (!empty($model)) {
            $max_product_price = 0;
            foreach ($model as $row) {
                $d['id'] = $row->product_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['short_description'] = ($lang == 'en') ? $row->short_description_en : $row->short_description_ar;
                $d['description'] = ($lang == 'en') ? $row->description_en : $row->description_ar;
                $d['SKU'] = $row->SKU;
                $d['regular_price'] = $this->convertPrice($row->regular_price, $row->base_currency_id, $store['currency_id']);
                $d['final_price'] = $this->convertPrice($row->final_price, $row->base_currency_id, $store['currency_id']);
                $d['currency_code'] = $row->baseCurrency->code_en;
                if ($row->type == 'S') {
                    $d['remaining_quantity'] = (int) ($row->remaining_quantity - \Yii::$app->params['bufferQty']);
                } else {
                    $d['remaining_quantity'] = (int) $this->getMaxRemainingQuantity($row->product_id);
                }
                $d['is_featured'] = $row->is_featured;
                $d['is_trending'] = $row->is_trending;
                $brandName = '';
                if ($row->brand_id != "") {
                    $brandModel = \app\models\Brands::findOne($row->brand_id);
                    if (!empty($brandModel)) {
                        $brandName = ($lang == 'en') ? $brandModel->name_en : $brandModel->name_ar;
                    }
                }
                $d['brand_name'] = $brandName;
                $d['manufacturer'] = !empty($row->manufacturer) ? $row->manufacturer->{"name_" . $lang} : "";
                $d['pharmacy'] = !empty($row->pharmacy) ? $row->pharmacy->{"name_" . $lang} : "";
                $defaultImage = $this->getProductDefaultImage($row->product_id);
                $d['image'] = $defaultImage;
                $d['product_type'] = ($row->type == 'G') ? "Grouped" : "Simple";
                $sizes = [];
                $options = $this->getProductAttributeValues($row, $lang);
                if (!empty($options) && !empty($options[count($options) - 1]['attributes'])) {
                    foreach ($options[count($options) - 1]['attributes'] as $attribute) {
                        $sizes[] = $attribute['value'];
                    }
                }
                $d['sizes'] = $sizes;
                $d['item_in_wishlist'] = (isset($user_id) && !empty($user_id)) ? (int) $this->checkProductInWishList($user_id, $row->product_id) : 0;
                $d['is_saleable'] = (int) $this->isProductSaleable($row, $lang, $store);
                array_push($result, $d);
                if ($row->final_price > $max_product_price) {
                    $max_product_price = $row->final_price;
                }
            }
            $attributesValue = [];
            //get brands list
            $brandValueQuery->select([
                'DISTINCT(`product`.`brand_id`)',
                'brands.name_en',
                'brands.name_ar',
            ])
                ->andFilterWhere(['IS NOT', 'product.brand_id', new \yii\db\Expression('NULL')]);
            $brands = $brandValueQuery
                ->asArray()
                ->all();
            $tmp = [];
            foreach ($brands as $brand) {
                $b = [
                    'id' => $brand['brand_id'],
                    'value' => $brand['name_' . $lang],
                ];
                array_push($tmp, $b);
            }

            $brandsCollected = [
                'filter_name' => ($lang == 'en') ? 'Brand' : 'ماركة',
                'filter_type' => 'Brand',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $brandsCollected);
            //get category list
            $categoryValueQuery->select([
                'DISTINCT(`product_categories`.`category_id`)',
                'category.name_en',
                'category.name_ar',
            ])
                ->join('left join', 'category', 'category.category_id = product_categories.category_id')
                ->andWhere(['category.hide_category_in_app' => 0])
                ->andWhere(['!=', 'category.lvl', 0])
                ->andFilterWhere(['IS NOT', 'product_categories.category_id', new \yii\db\Expression('NULL')])
                ->groupBy(NULL);
            $categories = $categoryValueQuery
                ->asArray()
                ->all();
            $tmp = [];
            foreach ($categories as $cat) {
                $c = [
                    'id' => $cat['category_id'],
                    'value' => $cat['name_' . $lang],
                ];
                array_push($tmp, $c);
            }
            $categoriesCollected = [
                'filter_name' => ($lang == 'en') ? 'Categories' : 'الاقسام',
                'filter_type' => 'Categories',
                'filter_values' => $tmp
            ];
            array_push($attributesValue, $categoriesCollected);
            //get attribute list
            $attributeQuery->select([
                'attributes1.attribute_id',
                'attributes1.code',
                'attributes1.name_en',
                'attributes1.name_ar',
            ]);
            $attributeQuery->join('LEFT JOIN', 'attribute_values as attributeValues', 'product_attribute_values.attribute_value_id = attributeValues.attribute_value_id');
            $attributeQuery->join('LEFT JOIN', 'attributes attributes1', 'attributeValues.attribute_id = attributes1.attribute_id');
            $attributeQuery->andWhere('product_attribute_values.attribute_value_id IS NOT NULL');
            if (isset($request['attribute_id']) && $request['attribute_id'] != '') {
                $attributeQuery->andWhere('product_attribute_values.attribute_value_id IN(' . $request['attribute_id'] . ')');
            }
            $attributeQuery->groupBy('attributes1.attribute_id');
            $attributeQuery->orderBy(['attributes1.name_en' => SORT_ASC]);
            $attributes = $attributeQuery->asArray()
                ->all();
            //debugPrint($attributes);
            foreach ($attributes as $key => $att) {
                // debugPrint($att['attribute_id']);
                // exit;
                //get attribute values by attribute id
                $attributeValueQuery1 = clone $attributeValueQuery;
                $attributeValueQuery1->select([
                    'product_attribute_values.attribute_value_id',
                    'attributeValues1.value_en',
                    'attributeValues1.value_ar',
                    'attributeValues1.attribute_id',
                ]);
                $attributeValueQuery1->join('LEFT JOIN', 'attribute_values as attributeValues1', 'product_attribute_values.attribute_value_id = attributeValues1.attribute_value_id');
                $attributeValueQuery1->andWhere('product_attribute_values.attribute_value_id IS NOT NULL');
                $attributeValueQuery1->andWhere(['attributeValues1.attribute_id' => $att['attribute_id']]);
                $attributeValueQuery1->groupBy('product_attribute_values.attribute_value_id');
                //   $attributeValueQueryResult = $attributeValueQuery1->orderBy(['attributeValues1.sort_order' => SORT_ASC])->createCommand()->rawSql;
                $attributeValueQueryResult = $attributeValueQuery1->orderBy(['attributeValues1.sort_order' => SORT_ASC])->asArray()->all();
                //                debugPrint($attributeValueQueryResult);
                //                exit;
                $parentId = [];
                if (!empty($model)) {
                    foreach ($model as $prod)
                        if ($prod->type == "G") {
                            $parentId[] = $prod->product_id;
                        }
                }
                //                debugPrint($parentId);
                //                exit;
                $associatedModelAtt = AssociatedProducts::find()->select([
                    'associated_products.*',
                    'product_attribute_values.*',
                    'attribute_values.*',
                ])
                    ->join('LEFT JOIN', 'product_attribute_values', 'product_attribute_values.product_id = associated_products.child_id')
                    ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                    ->where(['parent_id' => $parentId])->asArray()->all();
                //
                $attValues = [];
                $attValueCheck = [];
                if (!empty($attributeValueQueryResult)) {
                    //   debugPrint($attributeValueQueryResult);
                    foreach ($attributeValueQueryResult as $val) {
                        $d2 = [
                            'id' => $val['attribute_value_id'],
                            'value' => $val['value_' . $lang],
                        ];
                        $attValueCheck[] = $val['attribute_value_id'];
                        array_push($attValues, $d2);
                    }
                }
                foreach ($associatedModelAtt as $childAttCheck) {
                    $d2 = [
                        'id' => $childAttCheck['attribute_value_id'],
                        'value' => $childAttCheck['value_' . $lang],
                    ];
                    if (!in_array($childAttCheck['attribute_value_id'], $attValueCheck)) {
                        array_push($attValues, $d2);
                    }
                }
                //                debugPrint($attValues);
                //                exit;
                $attributeValueQuery1 = null;
                if (!empty($attValues)) {
                    $d1 = [
                        'filter_name' => $att['name_' . $lang],
                        'filter_type' => $att['name_en'],
                        'filter_values' => $attValues
                    ];
                    array_push($attributesValue, $d1);
                }
            }
            $this->data = [
                'products' => $result,
                'total_products' => $countQuery->count(),
                'total_pages' => $pages->pageCount,
                'max_product_price' => $this->convertPrice($max_product_price, 82, $store['currency_id']),
                'filter' => $attributesValue,
            ];
        } else {
            $this->response_code = 404;
            $this->message = 'No product match with your search criteria.please try by another keyword';
        }
        return $this->response();
    }

    private function getMaxRemainingQuantity($product_id)
    {
        $model = \app\models\Product::find()
            ->select(['MAX(product.remaining_quantity) AS max_remaining_quantity'])
            ->join('LEFT JOIN', 'associated_products', 'associated_products.child_id = product.product_id')
            ->where(['product.is_deleted' => 0, 'product.is_active' => 1])
            ->andWhere(['OR', ['product.product_id' => $product_id], ['associated_products.parent_id' => $product_id]])
            ->asArray()->one();

        return (!empty($model['max_remaining_quantity'])) ? ($model['max_remaining_quantity'] - \Yii::$app->params['bufferQty']) : 0;
    }

    private function getProductDefaultImage($productId)
    {
        $db = Yii::$app->db;
        $model = $db->cache(function ($db) use ($productId) {
            $data = Product::findOne($productId);
            return $data;
        }, $this->cacheDuration);
        if (!empty($model)) {
            $imageModel = $db->cache(function ($db) use ($model, $productId) {
                $imageModelQuery = \app\models\ProductImages::find()
                    ->join('LEFT JOIN', 'product', 'product.product_id = product_images.product_id')
                    ->where(['product.is_deleted' => 0]);
                if ($model->type == "G") {
                    $imageModelQuery->join('LEFT JOIN', 'associated_products', 'product.product_id = associated_products.child_id');
                    $imageModelQuery->andFilterWhere([
                        'OR',
                        ['=', 'associated_products.parent_id', $productId],
                        ['=', 'product.product_id', $productId]
                    ]);
                } else {
                    $imageModelQuery->andFilterWhere(['=', 'product.product_id', $productId]);
                }
                $imageModelQuery->orderBy(['sort_order' => SORT_ASC]);
                $data = $imageModelQuery->asArray()->one();
                return $data;
            }, $this->cacheDuration);
            $image = !empty($imageModel) ? $imageModel['image'] : "";
        } else {
            return '';
        }
        if (empty($image)) {
            $attribute = $db->cache(function ($db) use ($model) {
                $data = $model->getProductAttributeValues()
                    ->join('INNER JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                    ->join('INNER JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                    ->orderBy(['attributes.sort_order' => SORT_ASC])
                    ->one();
                return $data;
            }, $this->cacheDuration);

            if (!empty($attribute)) {
                $image = $db->cache(function ($db) use ($model, $attribute) {
                    $data = \app\models\ProductImages::find()
                        ->join('LEFT JOIN', 'associated_products AS a', 'a.child_id = product_images.product_id')
                        ->join('LEFT JOIN', 'associated_products as b', 'a.parent_id = b.parent_id')
                        ->join('LEFT JOIN', 'product_attribute_values', 'a.child_id = product_attribute_values.product_id')
                        ->where(['b.child_id' => $model->product_id, 'attribute_value_id' => $attribute->attribute_value_id])
                        ->orderBy(['sort_order' => SORT_ASC])
                        ->asArray()
                        ->one();
                    return $data;
                }, $this->cacheDuration);
            }
            if (empty($image)) {
                $image = $db->cache(function ($db) use ($model) {
                    $data = \app\models\ProductImages::find()
                        ->join('LEFT JOIN', 'associated_products', 'associated_products.parent_id  = product_images.product_id')
                        ->where(['associated_products.child_id' => $model->product_id])
                        ->orderBy(['sort_order' => SORT_ASC])
                        ->asArray()
                        ->one();
                    return $data;
                }, $this->cacheDuration);
            }
            if (!empty($image)) {
                $image = $image['image'];
            }
        }
        //return isset($image) ? $this->imgUrl . $image : "";
        return isset($image) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $image, 'https') : "";
    }

    /**
     *
     * @param type $productId
     * @param type $lang
     * @return array
     */
    private function getProductAttributeValues($product, $lang = 'en', $considerGrouped = true)
    {
        $tmp = array();
        if ($product->type == 'S') {
            $query = \app\models\ProductAttributeValues::find()
                ->select(['product_attribute_values.attribute_value_id', 'attribute_values.sort_order as value_sort_order', 'product_attribute_values.product_id', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value", 'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en", 'attributes.code AS attribute_code'])
                ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['product_attribute_values.product_id' => $product->product_id]);
            $query->orderBy(['attributes.sort_order' => SORT_ASC, 'value_sort_order' => SORT_ASC]);
            $model = $query->asArray()->all();
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $tmp[$row['attribute_id']] = [
                            'type' => $row['attribute'],
                            'attribute_id' => $row['attribute_id'],
                            'attribute_code' => $row['attribute_code'],
                            'attributes' => [
                                [
                                    'option_id' => (string) $row['attribute_value_id'],
                                    'option_product_id' => (string) $row['product_id'],
                                    'value' => $row['attribute_value'],
                                ]
                            ]
                        ];
                    } else {
                        $tmp[$row['attribute_id']]['attributes'][] = [
                            'option_id' => (string) $row['attribute_value_id'],
                            'option_product_id' => (string) $row['product_id'],
                            'value' => $row['attribute_value'],
                        ];
                    }
                }
            }
        } elseif ($product->type == 'G' && $considerGrouped) {
            $products = [];
            $products[] = $product->product_id;
            foreach ($product->associatedProducts as $p) {
                $products[] = $p->child_id;
            }
            $products = array_unique($products);
            $query = \app\models\ProductAttributeValues::find()
                ->select([
                    'product_attribute_values.product_attribute_value_id', 'product_attribute_values.attribute_value_id', 'attribute_values.sort_order as value_sort_order', 'product_attribute_values.product_id', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value",
                    'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en",
                    'attributes.name_en AS attribute_text',
                    'attributes.code AS attribute_code',
                    'product.remaining_quantity'
                ])
                ->join('LEFT JOIN', 'product', 'product_attribute_values.product_id = product.product_id')
                ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['product_attribute_values.product_id' => $products, 'product.is_active' => 1]);
            $query->orderBy(['attributes.sort_order' => SORT_ASC, 'value_sort_order' => SORT_ASC]);
            $query->groupBy('product_attribute_values.attribute_value_id');
            $model = $query->asArray()->all();
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $tmp[$row['attribute_id']] = [
                            'type' => $row['attribute'],
                            'attribute_id' => $row['attribute_id'],
                            'attribute_code' => $row['attribute_code'],
                            'attributes' => [
                                [
                                    'option_id' => (string) $row['attribute_value_id'],
                                    'option_product_id' => (string) $row['product_id'],
                                    'value' => $row['attribute_value'],
                                ]
                            ]
                        ];
                    } else {
                        $tmp[$row['attribute_id']]['attributes'][] = [
                            'option_id' => (string) $row['attribute_value_id'],
                            'option_product_id' => (string) $row['product_id'],
                            'value' => $row['attribute_value'],
                        ];
                    }
                }
            }
        }
        $result = array_values($tmp);
        return $result;
    }

    private function isProductSaleable($product, $lang, $store)
    {
        if ($product->type == 'S') {
            return ($product->remaining_quantity > \Yii::$app->params['bufferQty']);
        } else {
            if ($product->remaining_quantity > \Yii::$app->params['bufferQty']) {
                return true;
            } else {
                $associatedProducts = $this->getAssociatedProducts($product->product_id, $lang, $store);
                foreach ($associatedProducts as $p) {
                    if ($p['remaining_quantity'] > 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getAssociatedProducts($productId, $lang = 'en', $store = "")
    {
        $model = \app\models\AssociatedProducts::find()
            ->where(['parent_id' => $productId])
            ->all();
        $result = array();
        if (!empty($model)) {
            foreach ($model as $related) {
                $row = $related->child;
                $d['id'] = $row->product_id;
                $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                $d['short_description'] = ($lang == 'en') ? (string) $row->short_description_en : (string) $row->short_description_ar;
                $d['description'] = ($lang == 'en') ? (string) $row->description_en : (string) $row->description_ar;
                $d['SKU'] = $row->SKU;
                $d['regular_price'] = $this->convertPrice($row->regular_price, $row->base_currency_id, $store['currency_id']);
                $d['final_price'] = $this->convertPrice($row->final_price, $row->base_currency_id, $store['currency_id']);
                $d['currency_code'] = $row->baseCurrency->code_en;
                $d['remaining_quantity'] = (int) ($row->remaining_quantity - \Yii::$app->params['bufferQty']);
                $d['is_featured'] = $row->is_featured;
                $d['new_from_date'] = (string) $row->new_from_date;
                $d['new_to_date'] = (string) $row->new_to_date;
                $brandName = '';
                if ($row->brand_id != null) {
                    $brandModel = \app\models\Brands::findOne($row->brand_id);
                    if (!empty($brandModel)) {
                        $brandName = ($lang == 'en') ? $brandModel->name_en : $brandModel->name_ar;
                    }
                }
                $d['brand_name'] = $brandName;
                $d['image'] = $this->getProductDefaultImage($row->product_id);
                array_push($result, $d);
            }
        }
        return $result;
    }

    /**
     *
     * @param type $product_id
     * @param type $lang
     * @param type $user_id
     * @param type $store
     * @return type
     */
    public function actionProductDetails($product_id, $lang = 'en', $user_id = "", $store = "")
    {
        $store = $this->getStoreDetails($store);
        $model = \app\models\Product::find()
            ->select([
                'product.*',
            ])
            ->where(['product.product_id' => $product_id, 'product.is_active' => 1, 'product.is_deleted' => 0])
            ->one();
        if (!empty($model)) {
            $views = $model->views;
            $model->views = $views + 1;
            $model->save();
            //
            $d = $this->getFormatedProductDetails($model, $lang, $user_id, $store);
            $this->data = $d;
        } else {
            $this->response_code = 404;
            $this->message = 'Requested product does not exist';
        }
        return $this->response();
    }

    /**
     *
     * @param type $row
     * @param type $lang
     * @return array
     */
    private function getFormatedProductDetails($row, $lang = 'en', $user_id = "", $store = "")
    {
        $store = $this->getStoreDetails($store);
        $d['id'] = $row->product_id;
        $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
        $d['manufacturer'] = ($lang == 'en' && !empty($row->manufacturer)) ? $row->manufacturer->name_en : $row->manufacturer->name_ar;
        $d['pharmacy_id'] = (!empty($row->pharmacy)) ? $row->pharmacy->pharmacy_id : '';
        $d['pharmacy_name'] = ($lang == 'en' && !empty($row->pharmacy)) ? $row->pharmacy->name_en : $row->pharmacy->name_ar;
        $d['short_description'] = (!empty($row->{"short_description_" . $lang})) ? (($lang == 'en') ? $row->short_description_en : $row->short_description_ar) : "";
        $d['description'] = (!empty($row->{"description_" . $lang})) ? (($lang == 'en') ? $row->description_en : $row->description_ar) : "";
        $d['specification'] = (!empty($row->{"specification_" . $lang})) ? (($lang == 'en') ? $row->specification_en : $row->specification_ar) : "";
        $d['SKU'] = $row->SKU;
        $d['regular_price'] = $this->convertPrice($row->regular_price, $row->base_currency_id, $store['currency_id']);
        $d['final_price'] = $this->convertPrice($row->final_price, $row->base_currency_id, $store['currency_id']);
        $d['final_price_kwd'] = (float) $row->final_price;
        $d['currency_code'] = $row->baseCurrency->code_en;
        if ($row->type == 'S') {
            $d['remaining_quantity'] = (int) ($row->remaining_quantity - \Yii::$app->params['bufferQty']);
        } else {
            $d['remaining_quantity'] = (int) $this->getMaxRemainingQuantity($row->product_id);
        }
        $d['is_featured'] = $row->is_featured;
        $d['new_from_date'] = (string) $row->new_from_date;
        $d['new_to_date'] = (string) $row->new_to_date;
        $d['brand_id'] = (string) $row->brand_id;
        $brandName = '';
        $brandImage = '';
        if ($row->brand_id != null) {
            $brandModel = \app\models\Brands::findOne($row->brand_id);
            if (!empty($brandModel)) {
                $brandName = ($lang == 'en') ? $brandModel->name_en : $brandModel->name_ar;
                $brandImage = $brandModel->image_name;
            }
        }
        $d['brand_name'] = $brandName;
        $d['brand_image'] = (!empty($brandImage)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $brandImage) : "";
        $d['image'] = $this->getProductDefaultImage($row->product_id);
        $d['images'] = $this->getProductImage($row->product_id);
        $d['video'] = (!empty($row->video_name)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->video_name) : "";
        $d['configurable_option'] = $this->getProductAttributeValues($row, $lang);
        $d['related_products'] = $this->getRelatedProducts($row->product_id, $lang, $store);
        $d['is_saleable'] = (int) $this->isProductSaleable($row, $lang, $store);
        $d['product_type'] = ($row->type == 'G') ? "Grouped" : "Simple";
        if ($row->type == 'G') {
            $d['associated_products'] = $this->getAssociatedProducts($row->product_id, $lang, $store);
        }
        $d['item_in_cart'] = (isset($user_id) && !empty($user_id)) ? (int) $this->checkProductInCart($user_id, $row->product_id) : 0;
        $d['item_in_wishlist'] = (isset($user_id) && !empty($user_id)) ? (int) $this->checkProductInWishList($user_id, $row->product_id) : 0;
        return $d;
    }


    /**
     *
     * @param type $productId
     * @return array
     */
    private function getProductImage($productId)
    {
        $product = \app\models\Product::findOne($productId);
        $query = \app\models\ProductImages::find()
            ->join('LEFT JOIN', 'product', 'product.product_id = product_images.product_id')
            ->where(['product.is_deleted' => 0]);
        if ($product->type == "G") {
            $query->join('LEFT JOIN', 'associated_products', 'product.product_id = associated_products.child_id');
            $query->andFilterWhere([
                'OR',
                ['=', 'associated_products.parent_id', $productId],
                ['=', 'product.product_id', $productId]
            ]);
        } else {
            $query->andFilterWhere(['=', 'product.product_id', $productId]);
        }
        $query->orderBy(['sort_order' => SORT_ASC]);
        $model = $query->all();
        $result = array();
        if (!empty($model)) {
            foreach ($model as $row) {
                $img = $this->imgUrl . $row->image;
                if (!in_array($img, $result)) {
                    array_push($result, $img);
                }
            }
        }
        if ($product->type == 'G') {
            foreach ($product->associatedProducts0 as $p) {
                $model = \app\models\ProductImages::find()
                    ->where(['product_id' => $p->child_id])
                    ->orderBy(['sort_order' => SORT_ASC])
                    ->all();

                if (!empty($model)) {
                    foreach ($model as $row) {
                        $img = $this->imgUrl . $row->image;
                        if (!in_array($img, $result)) {
                            array_push($result, $img);
                        }
                    }
                }
            }
        } elseif ($product->type == 'S' && $product->show_as_individual == 0) {
            $image = $product->getProductImage($productId);
            if (!empty($image)) {
                $result = [];
                array_push($result, $image);
            }
        }
        if (empty($result)) {
            array_push($result, Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png'));
        }
        return $result;
    }

    /**
     *
     * @param type $productId
     * @param type $lang
     * @return array
     */
    private function getRelatedProducts($productId, $lang = 'en', $store)
    {
        $model = \app\models\RelatedProducts::find()
            ->where([
                'OR',
                ['product_id' => $productId],
                ['related_id' => $productId]
            ])
            ->orderBy(['related_id' => SORT_DESC])
            ->all();
        $result = array();
        if (!empty($model)) {
            foreach ($model as $related) {
                if ($related->related_id == $productId) {
                    $relativeId = $related->product_id;
                } else {
                    $relativeId = $related->related_id;
                }
                $rowQuery = \app\models\Product::find()
                    ->join('LEFT JOIN', 'associated_products', 'associated_products.child_id = product.product_id')
                    ->join('LEFT JOIN', 'brands', 'product.brand_id = brands.brand_id')
                    ->where(['product.product_id' => $relativeId, 'product.is_active' => 1, 'product.is_deleted' => 0])
                    ->andWhere(['brands.is_active' => 1, 'brands.is_deleted' => 0])
                    ->andWhere([
                        'OR',
                        [
                            'AND',
                            ['=', 'show_as_individual', 1],
                            ['=', 'product.type', 'S']
                        ],
                        ['=', 'product.type', 'G'],
                    ])
                    ->andWhere(['IS', 'associated_product_id', new \yii\db\Expression('NULL')]);
                $row = $rowQuery->one();
                if (!empty($row)) {
                    $d['id'] = $row->product_id;
                    $d['name'] = ($lang == 'en') ? $row->name_en : $row->name_ar;
                    $d['short_description'] = ($lang == 'en') ? $row->short_description_en : $row->short_description_ar;
                    $d['description'] = ($lang == 'en') ? $row->description_en : $row->description_ar;
                    $d['SKU'] = $row->SKU;
                    $d['regular_price'] = $this->convertPrice($row->regular_price, $row->base_currency_id, $store['currency_id']);
                    $d['final_price'] = $this->convertPrice($row->final_price, $row->base_currency_id, $store['currency_id']);
                    $d['currency_code'] = $row->baseCurrency->{'code_' . $lang};
                    $d['is_trending'] = $row->product_id;
                    $d['remaining_quantity'] = (int) ($row->remaining_quantity - \Yii::$app->params['bufferQty']);
                    $d['is_featured'] = $row->is_featured;
                    $d['new_from_date'] = (string) $row->new_from_date;
                    $d['new_to_date'] = (string) $row->new_to_date;
                    $brandName = '';
                    if (!empty($row->brand)) {
                        $brandName = ($lang == 'en') ? $row->brand->name_en : $row->brand->name_ar;
                    }
                    $d['brand_name'] = $brandName;
                    $d['image'] = $this->getProductDefaultImage($row->product_id);
                    $d['is_saleable'] = (int) $this->isProductSaleable($row, $lang, $store);
                    array_push($result, $d);
                }
            }
        }
        return $result;
    }

    private function getProductAverageRating($id)
    {
        return \app\helpers\AppHelper::getProductAverageRating($id);
    }

    public function actionConfigurableOptions($lang = "en", $store = "")
    {
        $store = $this->getStoreDetails($store);
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $data = $this->getProductConfigurableOptions($request['product_id'], $request['attribute_id'], $request['option_id'], $lang, $store);
            $this->data = array_values($data);
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    private function getProductConfigurableOptions($product, $attributes, $values, $lang = 'en', $store)
    {
        $products = [];
        $tmp = array();
        $parentProduct = \app\models\Product::findOne($product);
        $products[] = $parentProduct->product_id;
        foreach ($parentProduct->associatedProducts as $p) {
            $products[] = $p->child_id;
        }
        $count = count(explode(",", $values));
        $optionQuery = $optionNotQuery = $optionNotQuery2 = $attributeQuery = '';
        if (!empty($values)) {
            $optionQuery = " AND attribute_value_id IN (" . $values . ")";
            $optionNotQuery = " AND a.attribute_value_id NOT IN (" . $values . ")";
            $optionNotQuery2 = " AND av.attribute_value_id NOT IN (" . $values . ")";
        }
        if (!empty($attributes)) {
            $attributeQuery = " AND av.attribute_id NOT IN (" . $attributes . ")";
        }
        $sql = "select av.attribute_value_id, IF(STRCMP('$lang', 'en'), `av`.`value_ar`, `av`.`value_en`) AS attribute_value, a.attribute_id, 
IF(STRCMP('$lang', 'en'), `a`.`name_ar`, `a`.`name_en`) AS attribute, a.name_en as target_element,a.code AS attribute_code, pav.product_id, pi.image, a.name_en AS attribute_text 
                FROM attributes as a 
                left join attribute_values as av on a.attribute_id = av.attribute_id 
                left join product_attribute_values as pav on av.attribute_value_id = pav.attribute_value_id 
                left join product_images as pi on pav.product_id = pi.product_id 
                where pav.product_id in (
                    select a.product_id from product_attribute_values as a 
                    left join attribute_values as av on av.attribute_value_id = a.attribute_value_id 
                    where a.product_id in ( 
                        select product_id from product_attribute_values where product_id in (" . implode(",", $products) . ")" . $optionQuery . "
                    ) 
                    " . $optionNotQuery . "
                    " . $attributeQuery . "
                ) 
                " . $optionNotQuery2 . "
                group by av.attribute_value_id 
                order by a.attribute_id ASC";

        $query = \app\models\ProductAttributeValues::findBySql($sql);
        $model = $query->asArray()->all();
        if (!empty($model)) {
            $imagesArray = [];
            $image = '';
            foreach ($model as $row) {
                $isOptionAvailable = $this->getProductWithSelectedOptions($row['product_id'], $row['attribute_value_id']);
                if (empty($row['image'])) {
                    $image = Product::getProductImage($row['product_id']);
                } else {
                    $image = $this->imgUrl . $row['image'];
                }
                $productImagesModel = ProductImages::find()
                    ->where(['product_id' => $row['product_id']])
                    ->asArray()
                    ->all();
                if (empty($productImagesModel)) {
                    $productImagesModel = ProductImages::find()
                        ->where(['product_id' => $product])
                        ->asArray()
                        ->all();
                }
                $productImageArr = [];
                if (!empty($productImagesModel)) {
                    foreach ($productImagesModel as $productImage) {
                        array_push($productImageArr, $this->imgUrl . $productImage['image']);
                    }
                }
                if (!isset($tmp[$row['attribute_id']])) {
                    $tmp[$row['attribute_id']] = [
                        'type' => $row['attribute'],
                        'attribute_id' => $row['attribute_id'],
                        'entity_id' => $row['product_id'],
                        'attributes' => (!$isOptionAvailable) ? [] : [
                            [
                                'option_id' => $row['attribute_value_id'],
                                'value' => $row['attribute_value'],
                                'image_url' => (string) $image,
                            ]
                        ]
                    ];
                } else {
                    if ($isOptionAvailable) {
                        $tmp[$row['attribute_id']]['attributes'][] = [
                            'option_id' => $row['attribute_value_id'],
                            'value' => $row['attribute_value'],
                            'image_url' => (string) $image,
                        ];
                    }
                }
                if (!empty($image)) {
                    $imagesArray[0] = $image;
                }
                $tmp[$row['attribute_id']]['images'] = (!empty($productImageArr)) ? $productImageArr : $imagesArray;
            }
        } else {
            $configurableProduct = $this->getConfigurableProduct($products, $values);
            if (!empty($configurableProduct)) {
                $childImages = $this->getProductImage($configurableProduct['product_id']);
                if (empty($childImages)) {
                    $childImages = $this->getProductImage($product);
                }
            } else {
                $childImages = [];
            }
            if (empty($configurableProduct['image'])) {
                $prdModel = new Product();
                $image = $prdModel->getProductImage($configurableProduct['product_id']);
            } else {
                $image = $this->imgUrl . $configurableProduct['image'];
            }
            $productImagesModel = ProductImages::find()
                ->where(['product_id' => $configurableProduct['product_id']])
                ->asArray()
                ->all();
            if (empty($productImagesModel)) {
                $productImagesModel = ProductImages::find()
                    ->where(['product_id' => $product])
                    ->asArray()
                    ->all();
            }
            $productImageArr = [];
            if (!empty($productImagesModel)) {
                foreach ($productImagesModel as $productImage) {
                    array_push($productImageArr, $this->imgUrl . $productImage['image']);
                }
            }
            $tmp[] = [
                'entity_id' => (string) $configurableProduct['product_id'],
                'regular_price' => $this->convertPrice($configurableProduct['regular_price'], $configurableProduct['base_currency_id'], $store['currency_id']),
                'final_price' => $this->convertPrice($configurableProduct['final_price'], $configurableProduct['base_currency_id'], $store['currency_id']),
                'currency_code' => (string) $configurableProduct['currency_code'],
                'sku' => (string) $configurableProduct['SKU'],
                'remaining_quantity' => (int) ($configurableProduct['remaining_quantity'] - \Yii::$app->params['bufferQty']),
                'attributes' => [],
                'image_url' => (string) $image,
                'images' => (!empty($productImageArr)) ? $productImageArr : ((!empty($image)) ? [$image] : []),
            ];
        }
        $result = array_values($tmp);
        return $result;
    }

    private function getProductWithSelectedOptions($products, $options)
    {
        $models = \app\models\Product::find()
            ->join('LEFT JOIN', 'product_attribute_values', 'product.product_id = product_attribute_values.product_id')
            ->join('LEFT JOIN', 'brands', 'product.brand_id = brands.brand_id')
            ->where(['product.product_id' => $products, 'product_attribute_values.attribute_value_id' => $options])
            ->andWhere(['brands.is_active' => 1, 'brands.is_deleted' => 0])
            ->asArray()
            ->all();
        foreach ($models as $model) {
            if ($model['remaining_quantity'] > \Yii::$app->params['bufferQty'])
                return true;
        }
        return false;
    }

    private function getConfigurableProduct($products, $values)
    {
        $count = count(explode(",", $values));
        $optionQuery = '';
        if (!empty($values)) {
            $optionQuery = " AND attribute_value_id IN (" . $values . ")";
        }
        $sql = "select a.product_id, p.regular_price, p.final_price, p.remaining_quantity,p.SKU,p.base_currency_id, pi.image, c.code_en AS currency_code 
                from product_attribute_values as a 
                left join product as p on p.product_id = a.product_id 
                left join currencies as c on c.currency_id = p.base_currency_id
                left join product_images as pi on p.product_id = pi.product_id
                where a.product_id in ( select distinct product_id from product_attribute_values where product_id in (" . implode(",", $products) . ")" . $optionQuery . "
                group by product_id 
                having count(distinct attribute_value_id) = $count )
                limit 1";

        $model = \app\models\ProductAttributeValues::findBySql($sql)->asArray()->one();
        return $model;
    }

    public function actionAddToCart($lang = "en", $store = "kw")
    {
        $store = $this->getStoreDetails($store);
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            if ($request['products'] == "" || empty($request['products'])) {
                $this->response_code = 408;
                $this->message = 'Please select value';
                $this->data = new stdClass();
                return $this->response();
            }
            $model = \app\models\Orders::find()
                ->where(['user_id' => $request['user_id'], 'is_processed' => [0, 2]])
                ->one();
            if (empty($model)) {
                $model = new \app\models\Orders();
            }
            $this->restoreItemStock($model);
            $model->user_id = $request['user_id'];
            $model->create_date = date('Y-m-d H:i:s');
            $model->update_date = date('Y-m-d H:i:s');
            $model->is_processed = 0;
            if ($model->isNewRecord) {
                $model->order_number = AppHelper::getNextOrderNumber($request['user_id']);
            }
            $products = explode(',', $request['products']);
            $quantity = explode(',', $request['quantity']);

            /**** TO CHECK OTHER PHARMACY PRODUCT EXIST ? */

            $check_other_pharmacy = $this->checkPharmacyInCart($request['user_id'], $products);
            if ($check_other_pharmacy == 1) {
                $this->response_code = 201;
                $this->message  = 'Other pharmacy product already exist';
                $d['order_id'] = $model->order_id;
                $d['user_id']  = $request['user_id'];
                $d['products'] = $request['products'];
                $d['quantity'] = $request['quantity'];
                $this->data = $d;
                return $this->response();
                die;
            }

            if ($model->save(false)) {
                $inStock = true;
                $outOfStock = 0;
                $pharmaOrderCount = count($model->pharmacyOrders);
                $pharmaOrderCount = ($pharmaOrderCount == 0) ? $pharmaOrderCount : $pharmaOrderCount + 1;
                foreach ($products as $k => $item) {
                    $product = \app\models\Product::findOne($item);
                    $orderItem = \app\models\OrderItems::find()
                        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                        ->where(['pharmacy_orders.order_id' => $model->order_id, 'order_items.product_id' => $item])
                        ->one();
                    $quantityToApply = (isset($orderItem->quantity) && !empty($orderItem->quantity)) ? $orderItem->quantity : 0;
                    $quantityToApply += $quantity[$k];

                    if (($product->remaining_quantity - $quantityToApply) < Yii::$app->params['bufferQty']) {
                        $inStock = false;
                        $outOfStock++;
                        continue;
                    }
                    //
                    $pharmaOrder = \app\models\PharmacyOrders::find()
                        ->where(['pharmacy_id' => $product->pharmacy_id, 'order_id' => $model->order_id])
                        ->one();
                    if (empty($pharmaOrder)) {
                        $pharmaOrder = new \app\models\PharmacyOrders();
                    }
                    $pharmaOrder->order_id = $model->order_id;
                    $pharmaOrder->pharmacy_id = $product->pharmacy_id;
                    if ($pharmaOrder->isNewRecord) {
                        $pharmaOrderNumber = AppHelper::checkShopOrderNumber($model->order_number, $pharmaOrderCount);
                        $pharmaOrder->order_number = $pharmaOrderNumber;
                        $pharmaOrderCount++;
                    }
                    $pharmaOrder->pharmacy_commission = (isset($product->pharmacy->admin_commission) && !empty($product->pharmacy->admin_commission)) ? $product->pharmacy->admin_commission : 0;
                    if (!$pharmaOrder->save()) {
                    }
                    if (empty($orderItem)) {
                        $orderItem = new \app\models\OrderItems();
                    }
                    $orderItem->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                    $orderItem->product_id = $item;
                    $orderItem->currency_id = $product->base_currency_id;
                    $orderItem->price = $product->final_price;
                    $orderItem->cost_price = $product->cost_price;
                    $orderItem->quantity = $quantityToApply;
                    $orderItem->save(false);
                }
                if ($inStock === true) {
                    $this->message = 'Order successfully saved.';
                    $this->data = $this->cartDetails($model->order_id, $lang, $store);
                } else {
                    $this->response_code = 412;
                    if ($outOfStock > 1) {
                        $returnMessage = $outOfStock . " products out of " . count($products) . " are out of stock";
                    } else {
                        $returnMessage = "Product out of stock";
                    }
                    $this->message = $returnMessage;
                    $this->data = $this->cartDetails($model->order_id, $lang, $store);
                }
            } else {
                $this->response_code = 500;
                $this->data = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDeleteItemsFromCart($lang = "en", $store = "", $user_id = "", $order_id = "", $products = [])
    {
        if (!empty($user_id)) {
            $store = $this->getStoreDetails($store);
            $model = \app\models\Orders::find()
                ->where(['user_id' => $user_id, 'order_id' => $order_id, 'is_processed' => [0, 2]])
                ->one();
            if (!empty($model)) {
                $this->restoreItemStock($model);
                $orderItem = \app\models\OrderItems::find()
                    ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                    ->where(['pharmacy_orders.order_id' => $order_id])
                    ->all();

                if (!empty($orderItem)) {
                    foreach ($orderItem as $prod) {
                        $prod->delete();
                    }
                }

                $this->message = 'Order successfully updated.';
            } else {
                $this->response_code = 404;
                $this->message = 'Requested order does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }
    private function cartDetails($order, $lang, $store, $skip_remaining_quantity = 1, $considerGrouped = true)
    {
        $data = [
            'id' => (string) $order,
            'items' => []
        ];
        $model = \app\models\Orders::findOne($order);
        if (!empty($model)) {
            foreach ($model->pharmacyOrders as $pharmacyOrder) {
                foreach ($pharmacyOrder->orderItems as $item) {
                    if (!empty($item->product)) {
                        if ($model->is_processed == 0 && ($item->product->is_active == 0 || $item->product->is_deleted == 1)) {
                            $item->delete();
                            continue;
                        }
                        array_push($data['items'], $this->getFormatedCartProductDetails($item->product, $item->quantity, $lang, $store, $skip_remaining_quantity, $considerGrouped, $item->price));
                    }
                }
            }
            return $data;
        } else {
            return $data;
        }
    }

    public function getFormatedCartProductDetails($row, $quantity, $lang = 'en', $store = "", $skip_remaining_quantity, $considerGrouped = true, $item_price = 0)
    {
        $query = \app\models\Product::find()
            ->select(['associated_products.parent_id AS product_id'])
            ->join('LEFT JOIN', 'associated_products', 'associated_products.child_id = product.product_id')
            ->join('LEFT JOIN', 'brands', 'product.brand_id = brands.brand_id')
            ->where(['product.is_deleted' => 0, 'product.is_active' => 1, 'product.product_id' => $row->product_id])
            ->andWhere(['brands.is_active' => 1, 'brands.is_deleted' => 0])
            ->andWhere([
                'OR',
                ['=', 'show_as_individual', 0],
                ['IS NOT', 'associated_product_id', new \yii\db\Expression('NULL')]
            ]);
        $product = $query->asArray()->one();
        if (!empty($product) && !empty($product['product_id'])) {
            $product = \app\models\Product::findOne($product['product_id']);
            $name = ($lang == 'en') ? $product->name_en : $product->name_ar;
            $parentId = (string) $product->product_id;
            $productType = "Grouped";
        } else {
            $name = ($lang == 'en') ? $row->name_en : $row->name_ar;
            $parentId = (string) $row->product_id;
            $productType = ($row->type == 'G') ? "Grouped" : "Single";
        }
        $shortDescription = ($lang == 'en') ? (string) $row->short_description_en : (string) $row->short_description_ar;
        $description = ($lang == 'en') ? (string) $row->description_en : (string) $row->description_ar;
        $sku = $row->SKU;
        $regularPrice = $this->convertPrice($row->regular_price, $row->base_currency_id, $store['currency_id']);
        $finalPrice = $this->convertPrice($row->final_price, $row->base_currency_id, $store['currency_id']);
        //
        $storeKW = $this->getStoreDetails("KW");
        $regularKwPrice = $this->convertPrice($row->regular_price, $row->base_currency_id, $storeKW['currency_id']);
        $finalKwPrice = $this->convertPrice($row->final_price, $row->base_currency_id, $storeKW['currency_id']);
        //
        $currencyCode = $row->baseCurrency->code_en;
        $isFeatured = $row->is_featured;
        $d['id'] = $row->product_id;
        $d['name'] = $name;
        $d['short_description'] = $shortDescription;
        $d['description'] = $description;
        $d['SKU'] = $sku;
        $d['parent_id'] = $parentId;
        $d['regular_price'] = $regularPrice;
        $d['final_price'] = ($item_price == 0) ? 0 : $finalPrice;
        $d['final_price_kwd'] = ($item_price == 0) ? 0 : (float) $row->final_price;
        $d['base_currency_id'] = $row->base_currency_id;
        $d['currency_code'] = $currencyCode;
        $d['remaining_quantity'] = (int) ($row->remaining_quantity - \Yii::$app->params['bufferQty']);
        $d['quantity'] = $quantity;
        $d['is_featured'] = $isFeatured;
        $d['image'] = $this->getProductDefaultImage($row->product_id);
        $attributValues = $this->getCartProductAttributeValues($row, $lang, $considerGrouped, $skip_remaining_quantity);
        $d['configurable_option'] = $attributValues;
        $d['product_type'] = $productType;
        $d['image'] = $this->getProductDefaultImage($row->product_id);
        $d['is_saleable'] = ($row->remaining_quantity > Yii::$app->params['bufferQty']) ? 1 : 0;
        $brandName = '';
        $brandId = '';
        if (!empty($row->brand)) {
            $brandName = ($lang == 'en') ? $row->brand->name_en : $row->brand->name_ar;
            $brandId = $row->brand_id;
        }
        $d['brand_name'] = $brandName;
        $d['brand_id'] = $brandId;
        $pharmacyName = '';
        $pharmacyId = '';
        if (!empty($row->pharmacy)) {
            $pharmacyId = $row->pharmacy_id;
            $pharmacyName = ($lang == 'en') ? $row->pharmacy->name_en : $row->pharmacy->name_ar;
        }
        $d['pharmacy_name'] = $pharmacyName;
        $d['pharmacy_id'] = $pharmacyId;
        $d['regular_price_kw'] = $regularKwPrice;
        $d['final_price_kw'] = ($item_price == 0) ? 0 : $finalKwPrice;

        return $d;
    }

    public function getCartProductAttributeValues($product, $lang = 'en', $considerGrouped = true, $skip_remaining_quantity)
    {
        $tmp = array();
        if ($product->type == 'S' || $considerGrouped == false) {
            $query = \app\models\ProductAttributeValues::find()
                ->select(['product_attribute_values.attribute_value_id', 'attribute_values.sort_order as value_sort_order', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value", 'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en", 'attributes.code AS attribute_code'])
                ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['product_attribute_values.product_id' => $product->product_id]);
            $query->orderBy(['attributes.sort_order' => SORT_ASC, 'value_sort_order' => SORT_ASC]);
            $model = $query->asArray()->all();
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $tmp[$row['attribute_id']] = [
                            'type' => $row['attribute'],
                            'attribute_id' => $row['attribute_id'],
                            'attribute_code' => $row['attribute_code'],
                            'attributes' => [
                                [
                                    'option_id' => $row['attribute_value_id'],
                                    'value' => $row['attribute_value'],
                                ]
                            ]
                        ];
                    } else {
                        $tmp[$row['attribute_id']]['attributes'][] = [
                            'option_id' => $row['attribute_value_id'],
                            'value' => $row['attribute_value'],
                        ];
                    }
                }
            }
        } elseif ($product->type == 'G' && $considerGrouped) {
            $products = [];
            $products[] = $product->product_id;
            foreach ($product->associatedProducts as $p) {
                $products[] = $p->parent_id;
            }
            $products = array_unique($products);
            $query = \app\models\ProductAttributeValues::find()
                ->select([
                    'product_attribute_values.attribute_value_id', 'attribute_values.sort_order as value_sort_order', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value",
                    'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en",
                    'attributes.name_en AS attribute_text',
                    'attributes.code AS attribute_code',
                    'product.remaining_quantity'
                ])
                ->join('LEFT JOIN', 'product', 'product_attribute_values.product_id = product.product_id')
                ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['product_attribute_values.product_id' => $products]);

            if ($skip_remaining_quantity == 0) {
                $query->andWhere(['>', 'product.remaining_quantity', (string) Yii::$app->params['bufferQty']]);
            }
            $query->orderBy(['attributes.sort_order' => SORT_ASC, 'value_sort_order' => SORT_ASC]);
            $query->groupBy('product_attribute_values.attribute_value_id');
            $model = $query->asArray()->all();
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $tmp[$row['attribute_id']] = [
                            'type' => $row['attribute'],
                            'attribute_id' => $row['attribute_id'],
                            'attribute_code' => $row['attribute_code'],
                            'attributes' => [
                                [
                                    'option_id' => $row['attribute_value_id'],
                                    'value' => $row['attribute_value'],
                                ]
                            ]
                        ];
                    } else {
                        $tmp[$row['attribute_id']]['attributes'][] = [
                            'option_id' => $row['attribute_value_id'],
                            'value' => $row['attribute_value'],
                        ];
                    }
                }
            }
        }
        $result = array_values($tmp);
        return $result;
    }

    private function restoreItemStock($cart)
    {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backTrace[1]['function']) ? $backTrace[1]['function'] : "restore-item-stock";

        if (!$cart->isNewRecord && $cart->is_processed == 2) {
            foreach ($cart->pharmacyOrders as $pharmacyOrder) {
                foreach ($pharmacyOrder->orderItems as $item) {
                    $product = \app\models\Product::findOne($item->product_id);
                    $product->updateCounters(['remaining_quantity' => $item->quantity]);
                    AppHelper::adjustStock($item->product_id, 0, "Restoring {$item->quantity} quantity for order #{$cart->order_number}. Remaining quantity is {$product->remaining_quantity}. : v1/{$caller}");
                }
            }

            $cart->is_processed = 0;
            $cart->payment_initiated = 0;
            $cart->save(false);
        }
    }

    public function actionUpdateCart($lang = "en", $store = "")
    {
        $store = $this->getStoreDetails($store);
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = \app\models\Orders::find()
                ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id'], 'is_processed' => [0, 2]])
                ->one();
            if (!empty($model)) {
                $this->restoreItemStock($model);
                $products = explode(',', $request['products']);
                $quantity = explode(',', $request['quantity']);
                $inStock = true;
                $outOfStock = 0;
                $pharmaOrderCount = count($model->pharmacyOrders);
                $pharmaOrderCount = ($pharmaOrderCount == 0) ? $pharmaOrderCount : $pharmaOrderCount + 1;

                foreach ($products as $k => $item) {
                    $product = \app\models\Product::findOne($item);
                    if (($product->remaining_quantity - $quantity[$k]) < Yii::$app->params['bufferQty'] && $product->is_preorder != 1) {
                        $inStock = false;
                        $outOfStock++;
                        continue;
                    }
                    $pharmaOrder = \app\models\PharmacyOrders::find()
                        ->where(['pharmacy_id' => $product->pharmacy_id, 'order_id' => $request['order_id']])
                        ->one();

                    if (empty($pharmaOrder))
                        $pharmaOrder = new \app\models\PharmacyOrders();

                    $pharmaOrder->order_id = $request['order_id'];
                    $pharmaOrder->pharmacy_id = $product->pharmacy_id;
                    if ($pharmaOrder->isNewRecord) {
                        $pharmaOrder->order_number = AppHelper::checkShopOrderNumber($model->order_number, $pharmaOrderCount);
                        $pharmaOrderCount++;
                    }
                    $pharmaOrder->pharmacy_commission = (!empty($product->pharmacy->admin_commission)) ? $product->pharmacy->admin_commission : 0;
                    if (!$pharmaOrder->save()) {
                    }
                    $orderItem = \app\models\OrderItems::find()
                        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                        ->where(['pharmacy_orders.order_id' => $request['order_id'], 'order_items.product_id' => $item])
                        ->one();
                    if (empty($orderItem)) {
                        $orderItem = new \app\models\OrderItems();
                    }
                    $orderItem->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                    $orderItem->product_id = $item;
                    $orderItem->currency_id = $product->base_currency_id;
                    $orderItem->price = $product->final_price;
                    $orderItem->cost_price = $product->cost_price;
                    $orderItem->quantity = $quantity[$k];
                    $orderItem->save();
                }
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                $subTotal = $totalItems = 0;
                $pharmacy_id = "";
                $pharmacy_name = "";
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                        $pharmacy_id = $item['pharmacy_id'];
                        $pharmacy_name = $item['pharmacy_name'];
                    }
                }
                $shippingAddress = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $model->user_id, 'is_default' => 1])
                    ->one();
                if (empty($shippingAddress)) {
                    $shippingAddress = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0])
                        ->orderBy(['shipping_address_id' => SORT_DESC])
                        ->one();
                }
                $vatPct = 0;
                /*if (!empty($shippingAddress)) {
                    $baseCurrency = \app\models\Currencies::find()
                            ->where(['is_base_currency' => 1])
                            ->one();
                    if (!empty($shippingAddress->country))
                        $vatPct = $shippingAddress->country->vat;
                    if ($shippingAddress->country->free_delivery_limit != "") {
                        $freeDeliveryLimit = $this->convertPrice($shippingAddress->country->free_delivery_limit, 82, $store['currency_id']);
                        if ($subTotal > $freeDeliveryLimit) {
                            $shippingCost = 0;
                        } else {
                            $shippingCost = $this->convertPrice($shippingAddress->country->shipping_cost, 82, $store['currency_id']);
                        }
                    } else if (!empty($totalItems) && $totalItems > $shippingAddress->country->standard_delivery_items && $shippingAddress->country->standard_delivery_charge > 0) {
                        $price = (!empty($shippingAddress->country->shipping_cost)) ? $shippingAddress->country->shipping_cost : '0';
                        $price += ($price * $shippingAddress->country->standard_delivery_charge) / 100;
                        $shippingCost = (string) $this->convertPrice($price, 82, $store['currency_id']);
                    } else {
                        $shippingCost = $this->convertPrice($shippingAddress->country->shipping_cost, 82, $store['currency_id']);
                    }
                } else {
                    $shippingCost = 0;
                }*/

                if ($pharmacy_id != '') {
                    $pharmacyModel = \app\models\Pharmacies::find()
                        ->where(['pharmacy_id' => $pharmacy_id])
                        ->one();
                } else {
                    $pharmacyModel = false;
                }
                $cartDetails['pharmacy_id'] = (string) $pharmacy_id;
                $cartDetails['pharmacy_name'] = (string) $pharmacy_name;
                $cartDetails['minimum_order'] = (string) (!empty($pharmacyModel)) ? $pharmacyModel->minimum_order : '0';
                $cartDetails['delivery_charge'] = (!empty($pharmacyModel)) ? $pharmacyModel->delivery_charge : '0';
                $cartDetails['vat_pct'] = $vatPct;
                $vatCharges = 0;
                $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                $decimals = 2;
                if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                    $decimals = 3;
                }
                if ($vatPct != 0) {
                    $vatCharges = ($vatPct / 100) * $subTotal;
                }
                $cartDetails['vat_charges'] = (string) (($store['currency_id'] == 82 || $store['currency_id'] == 15) ? $vatCharges : ceil($vatCharges));
                if ($inStock === true) {
                    $this->message = 'Order successfully updated.';
                    $this->data = $cartDetails;
                } else {
                    if ($outOfStock > 1) {
                        $returnMessage = $outOfStock . " products out of " . count($products) . " are out of stock";
                    } else {
                        $returnMessage = "Product out of stock";
                    }
                    $this->message = $returnMessage;
                    $this->data = $this->cartDetails($request['order_id'], $lang, $store);
                }
            } else {
                $this->response_code = 404;
                $this->message = 'Requested order does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionDeleteFromCart($lang = "en", $store = "")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $store = $this->getStoreDetails($store);
            $model = \app\models\Orders::find()
                ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id'], 'is_processed' => [0, 2]])
                ->one();
            if (!empty($model)) {
                $this->restoreItemStock($model);
                $products = explode(',', $request['products']);
                foreach ($products as $k => $item) {
                    $orderItem = \app\models\OrderItems::find()
                        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                        ->where(['pharmacy_orders.order_id' => $request['order_id'], 'order_items.product_id' => $item])
                        ->one();
                    if (!empty($orderItem)) {
                        $orderItem->delete();
                    }
                }
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);

                $subTotal = $totalItems = 0;
                $pharmacy_id = "";
                $pharmacy_name = "";
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                        $pharmacy_id = $item['pharmacy_id'];
                        $pharmacy_name = $item['pharmacy_name'];
                    }
                }
                $shippingAddress = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $model->user_id, 'is_default' => 1])
                    ->one();
                if (empty($shippingAddress)) {
                    $shippingAddress = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $model->user_id, 'is_deleted' => 0])
                        ->orderBy(['shipping_address_id' => SORT_DESC])
                        ->one();
                }
                if ($pharmacy_id != '') {
                    $pharmacyModel = \app\models\Pharmacies::find()
                        ->where(['pharmacy_id' => $pharmacy_id])
                        ->one();
                } else {
                    $pharmacyModel = false;
                }
                $cartDetails['pharmacy_id'] = (string) $pharmacy_id;
                $cartDetails['pharmacy_name'] = (string) $pharmacy_name;
                $cartDetails['minimum_order'] = (string) (!empty($pharmacyModel)) ? $pharmacyModel->minimum_order : '0';
                $cartDetails['delivery_charge'] = (!empty($pharmacyModel)) ? $pharmacyModel->delivery_charge : '0';

                $vatPct = 0;
                $cartDetails['vat_pct'] = $vatPct;
                $vatCharges = 0;
                $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                $decimals = 2;
                if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                    $decimals = 3;
                }
                if ($vatPct != 0) {
                    $vatCharges = ($vatPct / 100) * $subTotal;
                }
                $cartDetails['vat_charges'] = (string) (($store['currency_id'] == 82 || $store['currency_id'] == 15) ? $vatCharges : ceil($vatCharges));

                $this->message = 'Order successfully updated.';
                $this->data = $cartDetails;
            } else {
                $this->response_code = 404;
                $this->message = 'Requested order does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionCartItems($user_id, $lang = "en", $store = "")
    {
        $store = $this->getStoreDetails($store);
        $cart = \app\models\Orders::find()
            ->where(['user_id' => $user_id, 'is_processed' => [0, 2]])
            ->one();
        if (!empty($cart)) {
            if (!empty($cart->promotion_id)) {
                $cart->promo_for = null;
                $cart->discount = null;
                $cart->discount_price = 0;
                $cart->save();
            }
            $this->restoreItemStock($cart);
            $cartDetails = $this->cartDetails($cart->order_id, $lang, $store);


            if (!empty($cartDetails)) {
                if (empty($cartDetails['items'])) {
                    $this->message = 'No items in user cart';
                }
                $subTotal = $totalItems = 0;
                $subTotalKw = 0;
                $pharmacy_id = '';
                $pharmacy_name = '';
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                        $pharmacy_id = $item['pharmacy_id'];
                        $pharmacy_name = $item['pharmacy_name'];
                    }
                }
                $vatPct = 0;
                $shippingAddress = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $user_id, 'is_default' => 1])
                    ->one();
                if (empty($shippingAddress)) {
                    $shippingAddress = \app\models\ShippingAddresses::find()
                        ->where(['user_id' => $user_id, 'is_deleted' => 0])
                        ->orderBy(['shipping_address_id' => SORT_DESC])
                        ->one();
                }

                if ($pharmacy_id != '') {
                    $pharmacyModel = \app\models\Pharmacies::find()
                        ->where(['pharmacy_id' => $pharmacy_id])
                        ->one();
                } else {
                    $pharmacyModel = false;
                }

                $cartDetails['pharmacy_id'] = (string) $pharmacy_id;
                $cartDetails['pharmacy_name'] = (string) $pharmacy_name;
                $cartDetails['minimum_order'] = (string) (!empty($pharmacyModel)) ? $pharmacyModel->minimum_order : '0';
                $cartDetails['delivery_charge'] = (!empty($pharmacyModel)) ? $pharmacyModel->delivery_charge : '0';
                $cartDetails['vat_pct'] = $vatPct;
                $vatCharges = 0;
                $vatChargesKw = 0;
                $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                $decimals = 2;
                if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                    $decimals = 3;
                }
                if ($vatPct != 0) {
                    $vatCharges = ($vatPct / 100) * $subTotal;
                    $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                }

                $cartDetails['vat_charges'] = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
                $this->data = $cartDetails;
            } else {
                $this->response_code = 200;
                $this->data = ['id' => '', 'items' => []];
                $this->message = 'No items in user cart';
            }
        } else {
            $this->response_code = 200;
            $this->data = ['id' => '', 'items' => []];
            $this->message = 'No items in user cart';
        }
        return $this->response();
    }

    private function getStoreCartItems($order, $store)
    {
        $model = \app\models\Orders::findOne($order);
        if (!empty($model)) {
            foreach ($model->pharmacyOrders as $pharmacyOrder) {
                foreach ($pharmacyOrder->orderItems as $item) {
                    if (!empty($item->product)) {
                        $product = $item->product;
                        $storeArr = [];
                        foreach ($product->storeProducts as $pStore) {
                            $storeArr[] = $pStore->store_id;
                        }
                        if (!empty($storeArr)) {
                            if (!in_array($store, $storeArr)) {
                                $item->delete();
                                continue;
                            }
                        }
                    }
                }
            }
        }
    }

    public function actionCheckItemStock($lang = "en", $store = "")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $store = $this->getStoreDetails($store);
            $model = \app\models\Orders::find()
                ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id'], 'is_processed' => [0, 2]])
                ->one();
            if (!empty($model)) {
                $this->getStoreCartItems($model->order_id, $store['store_id']);
                $model->payment_initiated = 0;
                $model->save(false);
                $products = explode(',', $request['products']);
                $quantity = explode(',', $request['quantity']);
                $inStock = true;
                $deletedProducts = [];
                $updatedProducts = [];

                $pharmaOrderCount = count($model->pharmacyOrders);
                $pharmaOrderCount = ($pharmaOrderCount == 0) ? $pharmaOrderCount : $pharmaOrderCount + 1;
                //$pharmacy_id = (!empty($model->pharmacyOrders)) ? $model->pharmacyOrders[0]['pharmacy_id'] : ''; 
                $pharmacy_id = "";
                if ($model->is_processed == 0) {
                    foreach ($products as $k => $item) {
                        $product = \app\models\Product::findOne($item);
                        //
                        $orderItem = \app\models\OrderItems::find()
                            ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                            ->join('LEFT JOIN', 'orders', 'pharmacy_orders.order_id = orders.order_id')
                            ->where(['orders.order_id' => $request['order_id'], 'order_items.product_id' => $item])
                            ->one();
                        //if remaining quantity is 0 then remove the item from the order
                        if (($product->remaining_quantity - Yii::$app->params['bufferQty']) <= 0 && $product->is_preorder != 1) {
                            $deletedProducts[] = ($lang == 'en') ? $product->name_en : $product->name_ar;
                            if (!empty($orderItem)) {
                                $orderItem->delete();
                            }
                            $inStock = false;
                        } else {
                            $pharmaOrder = \app\models\PharmacyOrders::find()
                                ->where(['pharmacy_id' => $product->pharmacy_id, 'order_id' => $model->order_id])
                                ->one();
                            if (empty($pharmaOrder))
                                $pharmaOrder = new \app\models\PharmacyOrders();
                            $pharmaOrder->order_id = $model->order_id;
                            $pharmaOrder->pharmacy_id = $product->pharmacy_id;
                            if ($pharmaOrder->isNewRecord) {
                                $pharmaOrder->order_number = AppHelper::checkShopOrderNumber($model->order_number, $pharmaOrderCount);
                                $pharmaOrderCount++;
                            }
                            $pharmaOrder->pharmacy_commission = (!empty($product->pharmacy->admin_commission)) ? $product->pharmacy->admin_commission : 0;
                            if (!$pharmaOrder->save()) {
                            }
                            if (empty($orderItem)) {
                                $orderItem = new \app\models\OrderItems();
                            }
                            $orderItem->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                            $orderItem->product_id = $item;
                            $orderItem->currency_id = $product->base_currency_id;
                            $orderItem->price = $product->final_price;
                            $orderItem->cost_price = $product->cost_price;
                            $availableQuantity = $quantity[$k];
                            /*
                             * if requested quantity is not available but remaining quantity is not 0 then add remaining quantity as item quantity.
                             * */
                            if ((($product->remaining_quantity - $quantity[$k]) < Yii::$app->params['bufferQty'] && (($product->remaining_quantity - Yii::$app->params['bufferQty']) > 0))) {
                                $availableQuantity = $product->remaining_quantity - Yii::$app->params['bufferQty'];
                                $inStock = false;
                                $updatedProducts[] = "Product {$product->{'name_' .$lang}} has only {$availableQuantity} quantity remaining.";
                            }
                            $orderItem->quantity = $availableQuantity;
                            $orderItem->save();
                            if ($model->is_processed == 0) {
                                $product->updateCounters(['remaining_quantity' => -$availableQuantity]);
                                AppHelper::adjustStock($item, 0, "Holding " . (-$availableQuantity) . " quantity for order #{$model->order_number}. Remaining quantity is {$product->remaining_quantity}. : v1/check-item-stock");
                            }
                        }
                    }
                }
                if ($inStock === true) {
                    $model->update_date = date("Y-m-d H:i:s");
                }
                $model->is_processed = 2;
                $user_Address_id = (!empty($request['shipping_address_id'])) ? $request['shipping_address_id'] : null;
                $defaultAddress = $this->getUserDefaultAddress($request['user_id'], $lang, $store, $user_Address_id, $model->order_id);
                $totalShippingAddress = \app\models\ShippingAddresses::find()
                    ->where(['user_id' => $request['user_id'], 'is_deleted' => 0])
                    ->count();
                //
                $deliveryCharges = $codCost = $vatPct = 0;


                if (!empty($defaultAddress)) {
                    //$deliveryCharges = $defaultAddress['shipping_cost_kw'];
                    $codCost = ($defaultAddress['is_cod_enable'] == 1) ? $defaultAddress['cod_cost_kw'] : '0';
                    $country = $defaultAddress['country_id'];
                    $vatPct = $defaultAddress['vat'];
                } /*else {
                    $deliveryCharges = $codCost = 0;
                    $vatPct = 0;
                }*/
                //echo $deliveryCharges;die;
                $model->delivery_charge = $deliveryCharges;
                $model->save();
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                $subTotal = $total = $totalItems = 0;
                $subTotalKw = 0;
                $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                $baseCurrencyName = $baseCurrency->code_en;
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $subTotalKw += ($item['final_price_kw'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                        $pharmacy_id = $item['pharmacy_id'];
                    }
                }


                if ($pharmacy_id != '') {
                    $pharmacyModel = \app\models\Pharmacies::find()
                        ->where(['pharmacy_id' => $pharmacy_id])
                        ->one();
                    if (!empty($pharmacyModel)) {
                        if ($pharmacyModel->is_free_delivery == 0) {
                            $deliveryCharges  = $pharmacyModel->delivery_charge;
                        }
                    }
                }

                // Prodo code Start //
                $isFreeShipping = 0;
                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $minimumOrder = !empty($promotion) ? $this->convertPrice($promotion->minimum_order, 82, $store['currency_id']) : "";
                //echo $minimumOrder; exit;
                if (!empty($promotion) && ((!empty($minimumOrder) && $subTotal > $minimumOrder) || empty($minimumOrder))) {
                    if ($promotion->shipping_included == 1) {
                        if (empty($promotion->promotionCountries)) {
                            $deliveryCharges = 0;
                            $isFreeShipping = 1;
                        } else {
                            if (isset($country) && $country != "") {
                                $promotionCountry = \app\models\PromotionCountries::find()
                                    ->where(['promotion_id' => $promotion->promotion_id, 'country_id' => $country])
                                    ->one();
                                if (!empty($promotionCountry)) {
                                    $deliveryCharges = 0;
                                    $isFreeShipping = 1;
                                }
                            }
                        }
                        //$model->delivery_charge = $deliveryCharges;
                    } else {
                        //$model->delivery_charge = $deliveryCharges;
                    }
                } else {
                    //$model->delivery_charge = $deliveryCharges;
                    $oldPromoId = $model->promotion_id;
                    $model->promotion_id = null;
                    $model->promo_for = null;
                    $model->discount = null;
                    //
                    $userPromotionModel = \app\models\UserPromotions::find()
                        ->where(['user_id' => $model->user_id, 'promotion_id' => $oldPromoId])
                        ->one();
                    if (!empty($userPromotionModel)) {
                        $userPromotionModel->status = 0;
                        $userPromotionModel->save(false);
                    }
                }
                $model->delivery_charge = $deliveryCharges;
                // Prodo code end //
                $model->save(false);
                $discountPrice = 0;
                $discountPriceKw = 0;
                $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                $baseCurrencyName = $baseCurrency->code_en;
                $promoFor = $model->promo_for;
                $promoId = $model->promotion_id;
                $discount = 0;
                $discount = $model->discount;
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        if (isset($promoFor) && !empty($promoFor)) {
                            if ($promoFor == 'P') {
                                $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                $discountPriceKw += ((($item['final_price_kw'] * $discount) / 100) * $item['quantity']);
                            } elseif ($promoFor == 'B') {
                                if (isset($item['brand_id']) && $item['brand_id'] != null) {
                                    if (empty($promotion->promotionBrands)) {
                                        $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                        $discountPriceKw += ((($item['final_price_kw'] * $discount) / 100) * $item['quantity']);
                                    } else {
                                        $hasPromoBrand = 1;
                                        $promoBrands = \app\models\PromotionBrands::find()
                                            ->where(['promotion_id' => $promoId, 'brand_id' => $item['brand_id']])
                                            ->one();
                                        if (!empty($promoBrands)) {
                                            $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                            $discountPriceKw += ((($item['final_price_kw'] * $discount) / 100) * $item['quantity']);
                                            $hasPromotionBrandProduct[] = $item['brand_id'];
                                        } else {
                                            //$discountPrice = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $totalKw = ($subTotalKw - $discountPriceKw) + $deliveryCharges;
                $deliveryChargeConverted = $this->convertPrice($deliveryCharges, 82, $store['currency_id']);
                $discountPriceConverted = $this->convertPrice($discountPriceKw, 82, $store['currency_id']);
                $total = ($subTotal - $discountPriceConverted) + $deliveryChargeConverted;
                if ($store['code'] != "kw") {
                    $paymentTypes = [
                        [
                            'type' => ($lang == 'en') ? 'Cash On Delivery' : 'الدفع عند الاستلام',
                            'code' => 'C',
                            'success_url' => '',
                            'fail_url' => '',
                            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/cod.png'),
                            'is_enable' => ((!empty($defaultAddress) && $defaultAddress['is_cod_enable'] == 1) || (strtolower($store['code']) == 'kw' && empty($defaultAddress))) ? 1 : 0
                        ],
                        [
                            'type' => ($lang == 'en') ? 'Visa/MasterCard' : 'فيزا / ماستر كارد',
                            'code' => 'CC',
                            'success_url' => 'http://admin.3eyadat.com',
                            'fail_url' => 'http://admin.3eyadat.com',
                            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/visa.png'),
                            'is_enable' => 1
                        ],
                    ];
                } else {
                    $paymentTypes = [
                        [
                            'type' => ($lang == 'en') ? 'Cash On Delivery' : 'الدفع عند الاستلام',
                            'code' => 'C',
                            'success_url' => '',
                            'fail_url' => '',
                            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/cod.png'),
                            'is_enable' => ((!empty($defaultAddress) && $defaultAddress['is_cod_enable'] == 1) || (strtolower($store['code']) == 'kw' && empty($defaultAddress))) ? 1 : 0
                        ],
                        [
                            'type' => ($lang == 'en') ? 'K-Net' : "كي نت",
                            'code' => 'K',
                            'success_url' => 'http://admin.3eyadat.com',
                            'fail_url' => 'http://admin.3eyadat.com',
                            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/knet.png'),
                            'is_enable' => 1
                        ],
                        [
                            'type' => ($lang == 'en') ? 'Visa/MasterCard' : 'فيزا / ماستر كارد',
                            'code' => 'CC',
                            'success_url' => 'http://admin.3eyadat.com',
                            'fail_url' => 'http://admin.3eyadat.com',
                            'image' => Yii::$app->urlManager->createAbsoluteUrl('/images/visa.png'),
                            'is_enable' => 1
                        ],
                    ];
                }
                $codCostConverted = $this->convertPrice($codCost, 82, $store['currency_id']);
                $deliveryOptions = (!empty($country)) ? AppHelper::getDeliveryOptions($lang, $country, $store, $subTotal, $totalItems) : [];
                $country_model = "";
                if (!empty($country)) {
                    $country_model = Country::find()->where(['country_id' => $country])->one();
                }
                if ($inStock === false) {
                    $this->response_code = 412;
                    if (!empty($deletedProducts)) {
                        if (count($deletedProducts) > 1) {
                            $string = ($lang == 'ar') ? 'غير متوفر حاليا ' : ' are currently out of stock';
                            $this->message = implode(', ', $deletedProducts) . $string;
                        } else {
                            $string = ($lang == 'ar') ? 'غير متوفر حاليا ' : ' is currently out of stock';
                            $this->message = implode(', ', $deletedProducts) . $string;
                        }
                    }
                    if (count($updatedProducts) > 0) {
                        if (!empty($this->message)) {
                            $this->message .= ', ' . implode(', ', $updatedProducts);
                        } else {
                            $this->message = implode(', ', $updatedProducts);
                        }
                    }
                }
                $vatCharges = 0;
                $vatChargesKw = 0;
                if ($vatPct != 0) {
                    $vatCharges = ($vatPct / 100) * $subTotal;
                    $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                }
                $model->vat_charges = $discountPriceKw;
                $model->vat_charges = $vatChargesKw;
                $model->discount_price = $discountPriceKw;
                $model->save(false);
                $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                $decimals = 2;
                if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                    $decimals = 3;
                }
                if ($model->promotion_id != null) {
                    $addressId = (!empty($defaultAddress)) ? $defaultAddress['address_id'] : "";
                    $verifyPromotion = $this->verifyPromotion($model->promotion_id, $request['user_id'], $cartDetails, $lang, $store, $addressId);
                    if (isset($verifyPromotion['code']) && $verifyPromotion['code'] == 500) {
                        $oldPromoId = $model->promotion_id;
                        $model->promotion_id = null;
                        $model->promo_for = null;
                        $model->discount = null;
                        $model->save(false);
                        $userPromotionModel = \app\models\UserPromotions::find()
                            ->where(['user_id' => $model->user_id, 'promotion_id' => $oldPromoId])
                            ->one();
                        if (!empty($userPromotionModel)) {
                            $userPromotionModel->status = 0;
                            $userPromotionModel->save(false);
                        }
                    }
                    $couponModel = \app\models\Promotions::find()
                        ->where(['promotion_id' => $model->promotion_id])
                        ->one();
                    if (!empty($couponModel)) {
                        $is_coupon_applied = 1;
                        $coupon = [
                            'title' => (string) $couponModel->{"title_" . $lang},
                            'discount' => (string) $couponModel->discount . '%',
                            'code' => (string) $couponModel->code,
                        ];
                    } else {
                        $is_coupon_applied = 0;
                        $coupon = new \stdClass();
                    }
                } else {
                    $is_coupon_applied = 0;
                    $coupon = new \stdClass();
                }
                $this->data = [
                    'cart' => $cartDetails,
                    'default_address' => !empty($defaultAddress) ? $defaultAddress : new \stdClass(),
                    'total_addresses' => (string) $totalShippingAddress,
                    'payment_types' => $paymentTypes,
                    'baseCurrencyName' => $baseCurrencyName,
                    'sub_total' => (string) $subTotal,
                    'total' => (string) $total,
                    'delivery_charges' => (string) $deliveryChargeConverted,
                    'cod_cost' => (string) $codCostConverted,
                    'delivery_options' => $deliveryOptions,
                    'vat_pct' => $vatPct,
                    'vat_charges' => number_format($vatCharges, $decimals),
                    'is_coupon_applied' => $is_coupon_applied,
                    'coupon' => $coupon,
                    'discount_price' => $discountPriceConverted,
                    'pharmacy_id' => $pharmacy_id,
                    'pharmacy_name' => (!empty($pharmacyModel)) ? $pharmacyModel->{'name_' . $lang} : '',
                    'minimum_order' => (!empty($pharmacyModel)) ? $pharmacyModel->minimum_order : '',
                ];
            } else {
                $this->response_code = 404;
                $this->message = 'Requested order does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }

        return $this->response();
    }

    public function actionCheckout($lang = "en", $store = "")
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $store = $this->getStoreDetails($store);
            $model = \app\models\Orders::find()
                ->where(['user_id' => $request['user_id'], 'order_id' => $request['order_id'], 'is_processed' => [0, 2]])
                ->one();
            if (!empty($model)) {
                $this->getStoreCartItems($model->order_id, $store['store_id']);
                if ($model->is_processed == 0) {
                    $this->response_code = 500;
                    $this->message = 'There was an error processing the request. Please try again later.';
                    return $this->response();
                }
                $storeKW = $this->getStoreDetails("KW");
                $cartDetailsKW = $this->cartDetails($request['order_id'], $lang, $storeKW);
                if (empty($cartDetailsKW['items'])) {
                    $this->response_code = 500;
                    $this->message = 'No items found in the cart.';
                    return $this->response();
                }
                //
                $model = \app\models\Orders::find()
                    ->where(['order_id' => $model->order_id])
                    ->one();
                //
                $orderUserId = $model->user_id;
                $model->create_date = date("Y-m-d H:i:s");
                $model->update_date = date("Y-m-d H:i:s");
                $model->store_id = $store['store_id'];
                $model->shipping_address_id = $request['shipping_address_id'];
                //
                $model->device_token = (isset($request['device_token']) && !empty($request['device_token'])) ? $request['device_token'] : "";
                $model->device_type = (isset($request['device_type']) && !empty($request['device_type'])) ? $request['device_type'] : "";
                $model->device_model = (isset($request['device_model']) && !empty($request['device_model'])) ? $request['device_model'] : "";
                $model->app_version = (isset($request['app_version']) && !empty($request['app_version'])) ? $request['app_version'] : "";
                $model->os_version = (isset($request['os_version']) && !empty($request['os_version'])) ? $request['os_version'] : "";
                $model->user_ip = Yii::$app->request->userIP;
                //
                $model->recipient_name = !empty($request['recipient_name']) ? $request['recipient_name'] : "";
                $model->recipient_phone = !empty($request['recipient_phone']) ? $request['recipient_phone'] : "";
                if (!empty($request['device_token'])) {
                    $userModel = \app\models\Users::findOne($orderUserId);
                    $userModel->device_token = $request['device_token'];
                    $userModel->save(false);
                }
                $payMode = NULL;
                if (isset($request['pay_mode']) && !empty($request['pay_mode'])) {
                    $payMode = $request['pay_mode'];
                    if ($payMode == 'V') {
                        $payMode = 'CC';
                    }
                }
                $model->payment_mode = $payMode;
                $codCost = $codCostKw = 0;
                if ($model->payment_mode == 'C') {
                    $codCostKw = (!empty($model->shippingAddress->country)) ? $model->shippingAddress->country->cod_cost : 0;
                    $model->cod_charge = $codCostKw;
                    $model->is_processed = 1;
                    $model->is_paid = 1;
                    if ($codCostKw > 0) {
                        $codCost = $this->convertPrice($codCostKw, 82, $store['currency_id']);
                    }
                }
                $model->delivery_option_id = (isset($request['delivery_option']) && !empty($request['delivery_option'])) ? $request['delivery_option'] : "";
                $model->redirect_url = (isset($request['redirect_url']) && !empty($request['redirect_url'])) ? $request['redirect_url'] : "";
                $model->save(false);
                //$deliveryCharges = 0;
                $deliveryCharges = $model->delivery_charge;
                // for discount amount //
                $promotion = \app\models\Promotions::findOne($model->promotion_id);
                $promoFor = $model->promo_for;
                $promoId = $model->promotion_id;
                $itemSubTotal = 0;
                $totalItems = 0;
                $discountPriceKw = 0;
                $discount = (!empty($model->promotion_id)) ? $model->promotion->discount : 0;
                if (isset($cartDetailsKW['items'])) {
                    foreach ($cartDetailsKW['items'] as $item) {
                        $itemSubTotal += ($item['final_price'] * $item['quantity']);
                        // $itemTotalCostPrice += ($item['cost_price'] * $item['quantity']);
                        $totalItems += $item['quantity'];
                        if (isset($promoFor) && !empty($promoFor)) {
                            if ($promoFor == 'F') {
                                $discountPriceKw += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } else if ($promoFor == 'P') {
                                $discountPriceKw += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } elseif ($promoFor == 'B') {
                                if (isset($item['brand_id']) && $item['brand_id'] != null) {
                                    if (empty($promotion->promotionBrands)) {
                                        $discountPriceKw += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                    } else {
                                        $hasPromoBrand = 1;
                                        $promoBrands = \app\models\PromotionBrands::find()
                                            ->where(['promotion_id' => $promoId, 'brand_id' => $item['brand_id']])
                                            ->one();
                                        if (!empty($promoBrands)) {
                                            $discountPriceKw += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                            $hasPromotionBrandProduct[] = $item['brand_id'];
                                        } else {
                                            //$discountPriceKw = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //promotion model
                $minimumOrder = !empty($promotion) ? $promotion->minimum_order : "";
                $isFreeShipping = 0;
                $country = !empty($model->shippingAddress) ? $model->shippingAddress->country_id : "";
                if (!empty($promotion) && $promotion->shipping_included == 1) {
                    if (empty($promotion->promotionCountries)) {
                        $isFreeShipping = 1;
                    } else {
                        if (isset($country) && $country != "") {
                            $promotionCountry = \app\models\PromotionCountries::find()
                                ->where(['promotion_id' => $promotion->promotion_id, 'country_id' => $country])
                                ->one();
                            if (!empty($promotionCountry)) {
                                $isFreeShipping = 1;
                            }
                        }
                    }
                }
                $cartDetails = $this->cartDetails($request['order_id'], $lang, $store);
                $itemSubTotalKw = 0;
                $discountPrice = 0;
                $vatPct = 0;
                if ($model->delivery_option_id == 1) {
                    $vatPct = $model->shippingAddress->country->vat;
                    $custom_dutyPct = $model->shippingAddress->country->custom_duty;
                    if ($isFreeShipping == 1) {
                        //$deliveryCharges = 0;
                    } else {
                        if ($model->shippingAddress->country->free_delivery_limit != "") {
                            $freeDeliveryLimit = $model->shippingAddress->country->free_delivery_limit;
                            if ($itemSubTotalKw > $freeDeliveryLimit) {
                                //$deliveryCharges = 0;
                            } else {
                                //$deliveryCharges = $model->shippingAddress->country->shipping_cost;
                            }
                        } else {
                            //$deliveryCharges = $model->shippingAddress->country->shipping_cost;
                        }
                    }
                } else if ($model->delivery_option_id == 2) {
                    //$deliveryCharges = $model->shippingAddress->country->express_shipping_cost;
                    $vatPct = $model->shippingAddress->country->vat;
                }
                $model->delivery_charge = $deliveryCharges;
                $model->save(false);
                $shipingAddressModel = $model->shippingAddress;
                $settings = Settings::findOne(1);
                $defaultStatus = $this->getDefaultOrderStatus();
                $orderStatusModel = \app\models\OrderStatus::find()
                    ->where(['order_id' => $model->order_id, 'status_id' => $defaultStatus->status_id])
                    ->one();
                if (empty($orderStatusModel)) {
                    $orderStatusModel = new \app\models\OrderStatus();
                }
                $orderStatusModel->order_id = $model->order_id;
                $orderStatusModel->status_id = ($settings->push_cod_orders) ? 2 : $defaultStatus->status_id;
                $orderStatusModel->status_date = date("Y-m-d H:i:s");
                $orderStatusModel->user_type = 'U';
                $orderStatusModel->user_id = $request['user_id'];
                $orderStatusModel->comment = 'Initial status after COD';
                $orderStatusModel->save();
                //
                $cartDetailsMail = $this->cartDetails($request['order_id'], 'en', $store, 1, false);
                $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
                $subTotal = $total = 0;
                $subTotalKw = 0;
                $baseCurrencyName = $baseCurrency->code_en;
                if (isset($cartDetails['items'])) {
                    foreach ($cartDetails['items'] as $item) {
                        $subTotal += ($item['final_price'] * $item['quantity']);
                        $subTotalKw += ($item['final_price_kw'] * $item['quantity']);

                        if (isset($promoFor) && !empty($promoFor)) {
                            if ($promoFor == 'F') {
                                $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } else if ($promoFor == 'P') {
                                $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                            } elseif ($promoFor == 'B') {
                                if (isset($item['brand_id']) && $item['brand_id'] != null) {
                                    if (empty($promotion->promotionBrands)) {
                                        $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                    } else {
                                        $hasPromoBrand = 1;
                                        $promoBrands = \app\models\PromotionBrands::find()
                                            ->where(['promotion_id' => $promoId, 'brand_id' => $item['brand_id']])
                                            ->one();
                                        if (!empty($promoBrands)) {
                                            $discountPrice += ((($item['final_price'] * $discount) / 100) * $item['quantity']);
                                            $hasPromotionBrandProduct[] = $item['brand_id'];
                                        } else {
                                            //$discountPrice = 0;
                                        }
                                    }
                                }
                            }
                        }
                        /*
                         * If payment type is cash on delivery then remove stock from the stock movement table
                         * */
                        if ($model->payment_mode == 'C') {
                            $productStockModel = new \app\models\ProductStocks();
                            $productStockModel->product_id = $item['id'];
                            $productStockModel->quantity = -$item['quantity'];
                            $productStockModel->message = "Removing stock to fulfill the order #{$model->order_number}. Remaining quantity is {$productStockModel->product->remaining_quantity}.";
                            $productStockModel->created_date = date('Y-m-d H:i:s');
                            $productStockModel->save();
                        }
                    }
                }
                // Promotoin end
                $vatCharges = 0;
                $vatChargesKw = 0;
                $countryModel = $model->shippingAddress->country;
                if ($vatPct != 0) {
                    $vatCharges = ($vatPct / 100) * $subTotal;
                    $vatChargesKw = ($vatPct / 100) * $subTotalKw;
                }
                $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
                $decimals = 2;
                if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                    $decimals = 3;
                }
                $deliveryChargesConverted = $this->convertPrice($deliveryCharges, 82, $store['currency_id']);
                $discountPriceConverted = $this->convertPrice($discountPriceKw, 82, $store['currency_id']);
                $total = ($subTotal - $discountPriceConverted) + $deliveryChargesConverted + $vatCharges;
                $totalKw = ($subTotalKw - $discountPriceKw) + $deliveryCharges + $vatChargesKw;
                //saving vat charges to the order
                $model->vat_charges = ($vatChargesKw > 0) ? $vatChargesKw : 0;
                $model->discount_price = $discountPriceKw;
                $model->payment_initiated = 1;
                $model->save(false);
                $transactionNumber = uniqid('O-');
                if ($model->payment_mode == 'C' || $model->payment_mode == 'W') {
                    /*if ($model->payment_mode == 'C') {
                        $total += $codCost;
                        $totalKw += $codCostKw;
                    }*/
                    $paymentModel = \app\models\Payment::find()
                        ->where(['type_id' => $model->order_id, 'type' => 'O'])
                        ->one();
                    if (empty($paymentModel)) {
                        $paymentModel = new \app\models\Payment();
                    }
                    $paymentModel->transaction_id = $this->generateTransactionId();
                    $paymentModel->type_id = $model->order_id;
                    $paymentModel->type = 'O';
                    $paymentModel->paymode = $request['pay_mode'];
                    $paymentModel->gross_amount = $totalKw;
                    $paymentModel->net_amount = $totalKw;
                    $paymentModel->currency_code = "KWD";
                    $paymentModel->result = 'CAPTURED';
                    $paymentModel->payment_date = date("Y-m-d H:i:s");
                    $paymentModel->save(false);
                    $subject = 'Thank you! your 3eyadat order #' . $model->order_number . ' has been placed';
                    $deliveryCharges = $deliveryChargesConverted;

                    debugPrint($paymentModel);
                    die;

                    try {
                        Yii::$app->mailer->compose('@app/mail/checkout', [
                            "cartDetails" => $cartDetailsMail,
                            'baseCurrencyName' => $baseCurrencyName,
                            'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                            'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                            'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                            'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                            'name' => $model->user->first_name . ' ' . $model->user->last_name,
                            'order_number' => $model->order_number,
                            'payment_mode' => $model->payment_mode,
                            'order_date' => $model->create_date,
                            'vat_pct' => (string) $vatPct,
                            'vat_charges' => (string) number_format($vatCharges, $decimals),
                            'discount_price' => $discountPrice,
                            'payment' => $paymentModel,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($model->user->email)
                            ->setSubject($subject)
                            ->send();

                        Yii::$app->mailer->compose('@app/mail/order-details', [
                            "cartDetails" => $cartDetailsMail,
                            'baseCurrencyName' => $baseCurrencyName,
                            'sub_total' => (string) AppHelper::formatPrice($subTotal, $baseCurrencyName),
                            'total' => (string) AppHelper::formatPrice($total, $baseCurrencyName),
                            'delivery_charges' => (string) AppHelper::formatPrice($deliveryCharges, $baseCurrencyName),
                            'cod_cost' => (string) AppHelper::formatPrice($codCost, $baseCurrencyName),
                            'name' => $model->user->first_name . " " . $model->user->last_name,
                            'user' => $model->user,
                            'order_number' => $model->order_number,
                            'order' => $model,
                            'payment_mode' => $model->payment_mode,
                            'shippingAddress' => $shipingAddressModel,
                            'vat_pct' => (string) $vatPct,
                            'vat_charges' => (string) number_format($vatCharges, $decimals),
                            'discount_price' => $discountPrice,
                        ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo(Yii::$app->params['adminEmail'])
                            ->setSubject("3eyadat order Confirmation #{$model->order_number}")
                            ->send();
                    } catch (\Exception $e) {
                        //todo code
                    }
                }

                if ($model->payment_mode == "K") {
                    $src = 'src_kw.knet';
                    //$paymentResponse = \app\helpers\PaymentHelper::tapPayment('O', $model->order_id, $model->user_id, $totalKw, $transactionNumber, $store['store_id'], $lang, $src, $model->payment_mode);
                    $myfatorah = \app\helpers\PaymentHelper::payThroughMyfatoorahExecutePayment('O', $model->order_id, $model->user_id, $totalKw, '', $lang, $model->payment_mode, 'KWD', $transactionNumber, $store['store_id']);
                    $paymentResponse = [
                        'status' => 200,
                        'url' => $myfatorah['payment_url'],
                        'success' => $myfatorah['success_url'],
                        'error' => $myfatorah['error_url'],
                        'gateway_response' => $myfatorah['gateway_response']
                    ];
                } elseif ($model->payment_mode == 'CC') {
                    $src = 'src_card';
                    //$paymentResponse = \app\helpers\PaymentHelper::tapPayment('O', $model->order_id, $model->user_id, $totalKw, $transactionNumber, $store['store_id'], $lang, $src, $model->payment_mode);

                    $myfatorah = \app\helpers\PaymentHelper::payThroughMyfatoorahExecutePayment('O', $model->order_id, $model->user_id, $totalKw, '', $lang, $model->payment_mode, 'KWD', $transactionNumber, $store['store_id']);
                    $paymentResponse = [
                        'status' => 200,
                        'url' => $myfatorah['payment_url'],
                        'success' => $myfatorah['success_url'],
                        'error' => $myfatorah['error_url'],
                        'gateway_response' => $myfatorah['gateway_response']
                    ];
                }
                if ($model->promotion_id != null) {
                    $couponModel = \app\models\Promotions::find()
                        ->where(['promotion_id' => $model->promotion_id])
                        ->one();
                    $is_coupon_applied = 1;
                    $coupon = [
                        'title' => (string) $couponModel->{"title_" . $lang},
                        'discount' => (string) $couponModel->discount . '%',
                        'code' => (string) $couponModel->code,
                    ];
                } else {
                    $is_coupon_applied = 0;
                    $coupon = new \stdClass();
                }
                $grandTotal = (($subTotal - $discountPriceConverted) + $deliveryChargesConverted + $codCost + $vatCharges);
                $this->data = [
                    'order_details' => [
                        'order_id' => $model->order_id,
                        'order_number' => $model->order_number,
                        'order_date' => $model->create_date,
                        'order_status' => ($lang == "en") ? $defaultStatus->name_en : $defaultStatus->name_ar,
                        'status_color' => $defaultStatus->color
                    ],
                    'cart' => $cartDetails,
                    'shipping_address' => $this->getAddressDetails($request['shipping_address_id'], $lang),
                    'base_currency_name' => $baseCurrencyName,
                    'sub_total' => (string) $subTotal,
                    'total' => (string) number_format($grandTotal, $decimals),
                    'delivery_charges' => (string) $deliveryChargesConverted,
                    'cod_cost' => (string) $codCost,
                    'vat_pct' => (string) $vatPct,
                    'vat_charges' => (string) number_format($vatCharges, $decimals),
                    'payment_mode' => $model->payment_mode,
                    'payment_url' => (!empty($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['url'] : '',
                    'success_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['success'] : "",
                    'error_url' => (isset($paymentResponse['status']) && $paymentResponse['status'] == 200) ? $paymentResponse['error'] : "",
                    'grand_total_kwd' => $totalKw,
                    'coupon' => $coupon,
                    'is_coupon_applied' => $is_coupon_applied,
                    'discount_price' => $discountPriceConverted,
                ];
            } else {
                $this->response_code = 404;
                $this->message = 'Requested order does not exist';
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    private function getDefaultOrderStatus()
    {
        $status = \app\models\Status::find()
            ->orderBy(['status_id' => SORT_ASC])
            ->limit(1)
            ->one();
        return $status;
    }

    private function generateTransactionId()
    {
        $transactionNumber = $this->generateCode(8);
        $transactionExists = \app\models\Payment::find()->where(['transaction_id' => $transactionNumber])->count();
        if ($transactionExists > 0) {
            while (true) {
                $transactionNumber = $this->generateCode(8);
                $transactionExists = \app\models\Payment::find()->where(['transaction_id' => $transactionNumber])->count();
                if ($transactionExists <= 0) {
                    break;
                }
            }
        }
        return $transactionNumber;
    }

    private function generateCode($length)
    {
        $character_set = array('count' => $length, 'characters' => '0123456789');
        $temp_array = array();
        for ($i = 0; $i < $character_set['count']; $i++) {
            $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
        }
        shuffle($temp_array);
        return implode('', $temp_array);
    }

    private function getAddressDetails($id, $lang = "en")
    {
        $model = \app\models\ShippingAddresses::find()
            ->where(['shipping_address_id' => $id])
            ->one();
        $address = [];
        if (!empty($model)) {
            $address = [
                'address_id' => $model->shipping_address_id,
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'area_name' => !empty($model->area) ? (($lang == 'en') ? $model->area->name_en : $model->area->name_ar) : "",
                'governorate_name' => !empty($model->state) ? (($lang == 'en') ? $model->state->name_en : $model->state->name_ar) : "",
                'country_name' => !empty($model->country) ? (($lang == 'en') ? $model->country->name_en : $model->country->name_ar) : "",
                'block_id' => (string) $model->block_id,
                'block_name' => (!empty($model->block)) ? (($lang == 'en') ? $model->block->name_en : $model->block->name_ar) : '',
                'street' => $model->street,
                'avenue' => (!empty($model->avenue)) ? $model->avenue : "",
                'landmark' => (!empty($model->landmark)) ? $model->landmark : "",
                'flat' => (!empty($model->flat)) ? $model->flat : "",
                'floor' => (!empty($model->floor)) ? $model->floor : "",
                'building' => (!empty($model->building)) ? $model->building : "",
                'addressline_1' => $model->addressline_1,
                'mobile_number' => $model->mobile_number,
                'alt_phone_number' => $model->alt_phone_number,
                'location_type' => $model->location_type,
                'notes' => $model->notes,
                'shipping_cost' => !empty($model->country) ? $model->country->shipping_cost : "",
                'cod_cost' => !empty($model->country) ? $model->country->cod_cost : "",
                'is_cod_enable' => !empty($model->country) ? $model->country->is_cod_enable : 0,
                'phonecode' => !empty($model->country) ? $model->country->phonecode : "",
            ];
        }
        return $address;
    }

    private function convertPrice($price, $productCurrency, $storeCurrency)
    {
        if (empty($price)) {
            return "0";
        }
        $ceil = ($storeCurrency == 82 || $storeCurrency == 15) ? 0 : 1; //No ceil for KWD and BHD
        $storeCurrencyRate = \app\models\Currencies::getDb()->cache(function ($db) use ($storeCurrency) {
            return \app\models\Currencies::find()->where(['currency_id' => $storeCurrency])->asArray()->one();
        });
        $baseCurrencyRate = \app\models\Currencies::getDb()->cache(function ($db) use ($productCurrency) {
            return \app\models\Currencies::find()->where(['currency_id' => $productCurrency])->asArray()->one();
        });
        $decimals = 2;
        if ($storeCurrencyRate['code_en'] == 'BHD' || $storeCurrencyRate['code_en'] == 'KWD' || $storeCurrencyRate['code_en'] == 'KD') {
            $decimals = 3;
        }
        if ($ceil) {
            $price = ($storeCurrencyRate['currency_rate'] / $baseCurrencyRate['currency_rate']) * $price;
            return (string) number_format(round(ceil($price), 0), $decimals, '.', '');
        } else {
            $price = ($storeCurrencyRate['currency_rate'] / $baseCurrencyRate['currency_rate']) * $price;
            return (string) number_format(round($price, $decimals), $decimals, '.', '');
        }
    }

    private function getStoreDetails($store = "")
    {
        $query = \app\models\Stores::find();
        if (!empty($store)) {
            $query->where(['code' => $store]);
        } else {
            $query->where(['is_default' => 1]);
        }
        $store = $query->limit(1)->asArray()->one();
        return $store;
    }

    public function actionUserOrders($user_id, $lang = "en", $store = "")
    {
        $store = $this->getStoreDetails($store);
        /* @var $orders Orders[] */
        $orders = \app\models\Orders::find()
            ->join('LEFT JOIN', 'order_status', 'orders.order_id = order_status.order_id')
            ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id= pharmacy_orders.order_id')
            ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
            ->where([
                'orders.user_id' => $user_id,
                'is_processed' => [1, 3]
            ])
            ->andWhere(['IS NOT', 'order_items.order_item_id', new \yii\db\Expression('NULL')])
            ->orderBy(['status_date' => SORT_DESC])
            ->all();
        $data = [];
        $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
        foreach ($orders as $order) {

            $pharmacy_order_model = \app\models\PharmacyOrders::find()
                ->andWhere(['order_id' => $order->order_id])->one();
            $pharmacy_id = (!empty($pharmacy_order_model)) ? $pharmacy_order_model->pharmacy_id : '';
            $pharmacy_Model = \app\models\Pharmacies::findOne($pharmacy_id);
            $pharmacy_name = (!empty($pharmacy_Model)) ? $pharmacy_Model->{'name_' . $lang} : '';

            $deliveryCharges = $this->convertPrice($order->delivery_charge, 82, $store['currency_id']);
            $codCost = $this->convertPrice($order->cod_charge, 82, $store['currency_id']);
            $discountPrice = $this->convertPrice($order->discount_price, 82, $store['currency_id']);
            $items = $this->cartDetails($order->order_id, $lang, $store);
            $shippingAddress = $order->shippingAddress;
            $subTotal = $total = 0;
            $subTotalKw = 0;
            $baseCurrencyName = $baseCurrency->code_en;
            if (isset($items['items'])) {
                foreach ($items['items'] as $item) {
                    $subTotal += $item['final_price'] * $item['quantity'];
                    $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                }
            }
            if ($order->payment_mode == 'C') {
                $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
            } else {
                $total = ($subTotal - $discountPrice) + $deliveryCharges;
            }
            $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
            $vatCharges = 0;
            $vatChargesKw = 0;
            if ($vatPct != 0) {
                $vatCharges = ($vatPct / 100) * $subTotal;
                $vatChargesKw = ($vatPct / 100) * $subTotalKw;
            }
            $total += $vatCharges;
            $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
            $decimals = 2;
            if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                $decimals = 3;
            }
            $orderStatus = $order->getOrderStatuses()
                ->orderBy(['status_date' => SORT_DESC])
                ->limit(1)
                ->one();
            $vatCharges = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
            $data[] = [
                'id' => $order->order_id,
                'order_number' => $order->order_number,
                'pharmacy_name' => $pharmacy_name,
                'recipient_name' => (string) $order->recipient_name,
                'recipient_phone' => (string) $order->recipient_phone,
                'created_date' => $order->create_date,
                'payment_mode' => $order->payment_mode,
                'sub_total' => (string) $subTotal,
                'total' => (string) $total,
                'cod_cost' => (string) $codCost,
                'delivery_charges' => (string) $deliveryCharges,
                'vat_pct' => (string) $vatPct,
                'vat_charges' => (string) $vatCharges,
                'discount_price' => (string) $discountPrice,
                'base_currency_name' => $baseCurrencyName,
                'items' => (isset($items['items'])) ? $items['items'] : [],
                'status_id' => !empty($orderStatus) ? $orderStatus->status_id : 0,
                'status' => !empty($orderStatus) ? $orderStatus->status->{"name_" . $lang} : "",
                'status_color' => !empty($orderStatus) ? $orderStatus->status->color : "",
                'is_cancel_active' => !empty($orderStatus) ? (($orderStatus->status_id < 2) ? 1 : 0) : 0,
                'shipping_address' => [
                    'first_name' => (!empty($shippingAddress)) ? $shippingAddress->first_name : "",
                    'last_name' => (!empty($shippingAddress)) ? $shippingAddress->last_name : "",
                    'area_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->area) ? $shippingAddress->area->name_en : "") : "",
                    'governorate_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->state) ? $shippingAddress->state->name_en : "") : "",
                    'country_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->name_en : "") : "",
                    'phonecode' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->phonecode : "") : "",
                    'block_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->block) ? $shippingAddress->block->name_en : "") : "",
                    'street' => (!empty($shippingAddress)) ? $shippingAddress->street : "",
                    'addressline_1' => (!empty($shippingAddress)) ? $shippingAddress->addressline_1 : "",
                    'mobile_number' => (!empty($shippingAddress)) ? $shippingAddress->mobile_number : "",
                    'alt_phone_number' => (!empty($shippingAddress)) ? $shippingAddress->alt_phone_number : "",
                    'location_type' => (!empty($shippingAddress)) ? $shippingAddress->location_type : "",
                    'notes' => (!empty($shippingAddress)) ? $shippingAddress->notes : "",
                ],
                'payment_details' => $this->getOrderPaymentDetails($order->order_id),
            ];
        }
        if (!empty($data)) {
            $this->data = $data;
        } else {
            $this->response_code = 404;
            $this->message = 'No orders for this user.';
        }
        return $this->response();
    }

    public function getOrderPaymentDetails($order_id)
    {
        $payment = \app\models\Payment::find()
            ->where(['type_id' => $order_id, 'type' => 'O'])
            ->orderBy(['payment_id' => SORT_DESC])
            ->one();
        if (!empty($payment)) {
            if ($payment->paymode == "K" || $payment->paymode == "CC") {
                $result = [
                    'id' => $payment->payment_id,
                    'paymode' => $payment->paymode,
                    'amount' => $payment->gross_amount,
                    'payment_id' => (string) $payment->PaymentID,
                    'result' => $payment->result,
                    'payment_date' => $payment->payment_date,
                    'transaction_id' => $payment->transaction_id,
                    'auth' => (string) $payment->auth,
                    'ref' => (string) $payment->ref,
                    'track_id' => (string) $payment->TrackID,
                ];
                return $result;
            } else {
                return new \stdClass();
            }
        } else {
            return new \stdClass();
        }
    }

    public function actionOrderDetails($id, $user_id, $lang = "en", $store = "")
    {
        $store = $this->getStoreDetails($store);
        /* @var $order Orders */
        $order = \app\models\Orders::find()
            ->join('LEFT JOIN', 'order_status', 'orders.order_id = order_status.order_id')
            ->join('LEFT JOIN', 'pharmacy_orders', 'orders.order_id = pharmacy_orders.order_id')
            ->join('LEFT JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
            ->where([
                'orders.user_id' => $user_id,
                'orders.order_id' => $id,
                'is_processed' => [1, 3]
            ])
            ->andWhere(['IS NOT', 'order_items.order_item_id', new \yii\db\Expression('NULL')])
            ->orderBy(['status_date' => SORT_DESC])
            ->one();

        if (!empty($order)) {
            $baseCurrency = \app\models\Currencies::findOne($store['currency_id']);
            $deliveryCharges = $this->convertPrice($order->delivery_charge, 82, $store['currency_id']);
            $codCost = $this->convertPrice($order->cod_charge, 82, $store['currency_id']);
            $discountPrice = $this->convertPrice($order->discount_price, 82, $store['currency_id']);
            $items = $this->cartDetails($order->order_id, $lang, $store);
            $shippingAddress = $order->shippingAddress;
            $subTotal = $subTotalKw = $total = 0;
            $baseCurrencyName = $baseCurrency->code_en;
            if (isset($items['items'])) {
                foreach ($items['items'] as $item) {
                    $subTotal += $item['final_price'] * $item['quantity'];
                    $subTotalKw += $item['final_price_kw'] * $item['quantity'];
                }
            }
            if ($order->payment_mode == 'C') {
                $total = ($subTotal - $discountPrice) + $deliveryCharges + $codCost;
            } else {
                $total = ($subTotal - $discountPrice) + $deliveryCharges;
            }

            $vatPct = (!empty($shippingAddress)) ? $shippingAddress->country->vat : 0;
            $vatCharges = $vatChargesKw = 0;
            if ($vatPct != 0) {
                $vatChargesKw = ($vatPct / 100) * $subTotalKw;
            }
            $vatCharges = $this->convertPrice($vatChargesKw, 82, $store['currency_id']);
            $total += $vatCharges;
            $currencyModel = \app\models\Currencies::findOne($store['currency_id']);
            $decimals = 2;
            if ($currencyModel->code_en == 'BHD' || $currencyModel->code_en == 'KWD') {
                $decimals = 3;
            }
            $orderStatus = $order->getOrderStatuses()
                ->orderBy(['status_date' => SORT_DESC])
                ->limit(1)
                ->one();
            $grand_total_kwd = $this->convertPrice($total, $store['currency_id'], 82);
            $coupon = new \stdClass();
            if (!empty($order->promotion_id)) {
                $couponModel = \app\models\Promotions::find()
                    ->where(['promotion_id' => $order->promotion_id])
                    ->one();
                if (!empty($couponModel)) {
                    $coupon = [
                        'title' => (string) $couponModel->{"title_" . $lang},
                        'discount' => (string) $couponModel->discount . '%',
                        'code' => (string) $couponModel->code,
                    ];
                }
            }
            $data = [
                'id' => $order->order_id,
                'order_number' => $order->order_number,
                'recipient_name' => (string) $order->recipient_name,
                'recipient_phone' => (string) $order->recipient_phone,
                'tracking_link' => (string) (!empty($order->tracking_link)) ? $order->tracking_link : '',
                'created_date' => $order->create_date,
                'payment_mode' => $order->payment_mode,
                'sub_total' => (string) $subTotal,
                'total' => (string) $total,
                'grand_total_kwd' => $grand_total_kwd,
                'cod_cost' => (string) $codCost,
                'delivery_charges' => (string) $deliveryCharges,
                'discount_price' => (string) $discountPrice,
                'vat_pct' => (string) $vatPct,
                'vat_charges' => (string) $vatCharges,
                'base_currency_name' => $baseCurrencyName,
                'items' => (isset($items['items'])) ? $items['items'] : [],
                'status_id' => $orderStatus->status_id,
                'status' => $orderStatus->status->{"name_" . $lang},
                'status_color' => $orderStatus->status->color,
                'is_cancel_active' => ($orderStatus->status_id <= 3) ? 1 : 0,
                'coupon' => $coupon,
                'shipping_address' => [
                    'first_name' => (!empty($shippingAddress)) ? $shippingAddress->first_name : "",
                    'last_name' => (!empty($shippingAddress)) ? $shippingAddress->last_name : "",
                    'area_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->area) ? $shippingAddress->area->name_en : "") : "",
                    'governorate_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->state) ? $shippingAddress->state->name_en : "") : "",
                    'country_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->name_en : "") : "",
                    'phonecode' => (!empty($shippingAddress)) ? (!empty($shippingAddress->country) ? $shippingAddress->country->phonecode : "") : "",
                    'block_name' => (!empty($shippingAddress)) ? (!empty($shippingAddress->block) ? $shippingAddress->block->name_en : "") : "",
                    'street' => (!empty($shippingAddress)) ? $shippingAddress->street : "",
                    'avenue' => (!empty($shippingAddress->avenue)) ? $shippingAddress->avenue : "",
                    'landmark' => (!empty($shippingAddress->landmark)) ? $shippingAddress->landmark : "",
                    'flat' => (!empty($shippingAddress->flat)) ? $shippingAddress->flat : "",
                    'floor' => (!empty($shippingAddress->floor)) ? $shippingAddress->floor : "",
                    'building' => (!empty($shippingAddress->building)) ? $shippingAddress->building : "",
                    'addressline_1' => (!empty($shippingAddress)) ? $shippingAddress->addressline_1 : "",
                    'mobile_number' => (!empty($shippingAddress)) ? $shippingAddress->mobile_number : "",
                    'alt_phone_number' => (!empty($shippingAddress)) ? $shippingAddress->alt_phone_number : "",
                    'location_type' => (!empty($shippingAddress)) ? $shippingAddress->location_type : "",
                    'notes' => (!empty($shippingAddress)) ? $shippingAddress->notes : "",
                    'id_number' => (!empty($shippingAddress->id_number)) ? $shippingAddress->id_number : "",
                ],
                'payment_details' => $this->getOrderPaymentDetails($order->order_id),
            ];
            $this->data = $data;
        } else {
            $this->response_code = 404;
            $this->message = 'Order does not exist.';
        }
        return $this->response();
    }

    private function checkProductInWishList($user, $product)
    {
        $wishlist = \app\models\WishList::find()
            ->where(['user_id' => $user, 'product_id' => $product])
            ->count();

        return ($wishlist > 0);
    }

    private function checkProductInCart($user, $product)
    {
        $cartItem = \app\models\OrderItems::find()
            ->join('LEFT JOIN', 'pharmacy_orders', 'order_items.pharmacy_order_id= pharmacy_orders.pharmacy_order_id')
            ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
            ->where(['is_processed' => [0, 2], 'user_id' => $user, 'product_id' => $product])
            ->count();

        return ($cartItem > 0);
    }

    public function actionPrescriptionList($lang = 'en', $user_id = '')
    {

        if ($user_id == '') {
            $this->response_code = 201;
            $this->message = 'User id required';
            $this->data = "";
            return $this->response_array();
        }
        $query = \app\models\DoctorPrescriptions::find()
            ->andwhere(['doctor_prescriptions.is_active' => 1, 'doctor_prescriptions.is_deleted' => 0, 'user_id' => $user_id])
            ->orderby('doctor_prescriptions.doctor_appointment_prescription_id', 'ASC');
        $model = $query->all();
        $result = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                $d['doctor_name'] = (!empty($row->appointment->doctor)) ? $row->appointment->doctor->{'name_' . $lang} : '';
                $d['doctor_appointment_prescription_id'] = $row->doctor_appointment_prescription_id;
                $d['referred_pharmacy_id'] = $row->referred_pharmacy_id;
                $d['pharmacy_name'] = (!empty($row->pharmacy)) ? $row->pharmacy->{'name_' . $lang} : '';
                $d['created_at'] = $row->created_at;
                $d['medicine_list'] = $this->getMedicineList($row->doctor_appointment_prescription_id, $lang);
                array_push($result, $d);
            }
        } else {
            $this->response_code = 201;
            $this->message = 'No prescription list found';
            $this->data = new \stdClass();
            return $this->response_array();
        }
        $this->data = [
            'prescription'        => $result,
        ];
        return $this->response_array();
    }

    public function getMedicineList($doctor_appointment_prescription_id, $lang = "en")
    {
        $models = \app\models\DoctorAppointmentMedicines::find()
            ->andwhere(['doctor_appointment_prescription_id' => $doctor_appointment_prescription_id])
            ->orderby('doctor_appointment_medicine_id', 'ASC')
            ->all();
        $res = [];

        if (!empty($models)) {
            foreach ($models as $row) {
                $temp['medicine_id'] = $row->doctor_appointment_medicine_id;
                $temp['product_id'] = $row->product_id;
                $temp['product_name'] = (string)(!empty($row->product)) ? $row->product->{'name_' . $lang} : '';
                $temp['qty'] = (string)$row->qty;
                $temp['instruction'] = (string)$row->instruction;
                $temp['image'] = $this->getProductDefaultImage($row->product->product_id);
                array_push($res, $temp);
            }
        }

        return $res;
    }

    public function actionPlacePrescriptionOrder($lang = "en", $store = "kw", $prescription_id, $user_id)
    {
        $models = \app\models\DoctorAppointmentMedicines::find()
            ->andwhere(['doctor_appointment_prescription_id' => $prescription_id])
            ->orderby('doctor_appointment_medicine_id', 'ASC')
            ->all();
        $res = [];
        $products_arr = [];
        $qty_arr = [];
        if (!empty($models)) {
            foreach ($models as $row) {
                $product_id  = $row->product_id;
                $qty         = $row->qty;
                array_push($products_arr, $row->product_id);
                array_push($qty_arr, $row->qty);
            }
        }
        $products = (!empty($products_arr)) ? implode(',', $products_arr) : '';
        $quantity = (!empty($qty_arr)) ? implode(',', $qty_arr) : '';
        return $this->AddPrescriptionToCart($lang, $store, $products, $quantity, $prescription_id, $user_id);
    }

    public function AddPrescriptionToCart($lang = "en", $store = "kw", $req_products, $req_quantity, $prescription_id, $user_id)
    {
        $store = $this->getStoreDetails($store);
        if (!empty($req_products)) {
            $model = \app\models\Orders::find()
                ->where(['user_id' => $user_id, 'is_processed' => [0, 2]])
                ->one();
            if (empty($model)) {
                $model = new \app\models\Orders();
            }
            $this->restoreItemStock($model);
            $model->user_id = $user_id;
            $model->create_date = date('Y-m-d H:i:s');
            $model->update_date = date('Y-m-d H:i:s');
            $model->is_processed = 0;
            $model->prescription_id = $prescription_id;
            if ($model->isNewRecord) {
                $model->order_number = AppHelper::getNextOrderNumber($user_id);
            }

            $products = explode(',', $req_products);
            $quantity = explode(',', $req_quantity);

            /**** TO CHECK OTHER PHARMACY PRODUCT EXIST ? */
            $check_other_pharmacy = $this->checkPharmacyInCart($user_id, $products);
            if ($check_other_pharmacy == 1) {
                $this->response_code = 201;
                $this->message  = 'Other pharmacy product already exist';
                $d['order_id'] = $model->order_id;
                $d['user_id']  = $user_id;
                $d['products'] = $req_products;
                $d['quantity'] = $req_quantity;
                $this->data = $d;
                return $this->response();
                die;
            }
            /**** TO CHECK OTHER PHARMACY PRODUCT EXIST ? */

            if ($model->save(false)) {
                $inStock = true;
                $outOfStock = 0;
                $pharmaOrderCount = count($model->pharmacyOrders);
                $pharmaOrderCount = ($pharmaOrderCount == 0) ? $pharmaOrderCount : $pharmaOrderCount + 1;
                foreach ($products as $k => $item) {
                    $product = \app\models\Product::findOne($item);
                    $orderItem = \app\models\OrderItems::find()
                        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                        ->where(['pharmacy_orders.order_id' => $model->order_id, 'order_items.product_id' => $item])
                        ->one();
                    $quantityToApply = (isset($orderItem->quantity) && !empty($orderItem->quantity)) ? $orderItem->quantity : 0;
                    $quantityToApply += $quantity[$k];
                    //echo ($product->remaining_quantity - $quantityToApply);
                    /*if (($product->remaining_quantity - $quantityToApply) < Yii::$app->params['bufferQty']) {
                        $inStock = false;
                        $outOfStock++;
                        continue;
                    }*/
                    //
                    $pharmaOrder = \app\models\PharmacyOrders::find()
                        ->where(['pharmacy_id' => $product->pharmacy_id, 'order_id' => $model->order_id])
                        ->one();
                    if (empty($pharmaOrder)) {
                        $pharmaOrder = new \app\models\PharmacyOrders();
                    }

                    $pharmaOrder->order_id = $model->order_id;
                    $pharmaOrder->pharmacy_id = $product->pharmacy_id;
                    if ($pharmaOrder->isNewRecord) {
                        $pharmaOrderNumber = AppHelper::checkShopOrderNumber($model->order_number, $pharmaOrderCount);
                        $pharmaOrder->order_number = $pharmaOrderNumber;
                        $pharmaOrderCount++;
                    }
                    $pharmaOrder->pharmacy_commission = (isset($product->pharmacy->admin_commission) && !empty($product->pharmacy->admin_commission)) ? $product->pharmacy->admin_commission : 0;
                    if (!$pharmaOrder->save(false)) {
                    }
                    if (empty($orderItem)) {
                        $orderItem = new \app\models\OrderItems();
                    }
                    $orderItem->pharmacy_order_id = $pharmaOrder->pharmacy_order_id;
                    $orderItem->product_id = $item;
                    $orderItem->currency_id = $product->base_currency_id;
                    $orderItem->price = $product->final_price;
                    $orderItem->cost_price = $product->cost_price;
                    $orderItem->quantity = $quantityToApply;
                    $orderItem->save(false);
                }
                if ($inStock === true) {
                    $this->message = 'Order successfully saved.';
                    $this->data = $this->cartDetails($model->order_id, $lang, $store);
                } else {
                    $this->response_code = 412;
                    if ($outOfStock > 1) {
                        $returnMessage = $outOfStock . " products out of " . count($products) . " are out of stock";
                    } else {
                        $returnMessage = "Product out of stock";
                    }
                    $this->message = $returnMessage;
                    $this->data = $this->cartDetails($model->order_id, $lang, $store);
                }
            } else {
                $this->response_code = 500;
                $this->data = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }


    public function actionConfirmToCart($lang = "en", $store = "kw")
    {
        $request = Yii::$app->request->bodyParams;
        $result = [];
        if (!empty($request)) {
            $products = explode(',', $request['products']);
            $response = $this->actionDeleteItemsFromCart($lang, $store, $request['user_id'], $request['order_id'], $request['products']);
            if ($response['status'] == 200) {
                $this->actionAddToCart($lang, $store);
            }
        }
        return $this->response();
    }

    public function checkPharmacyInCart($user_id, $products)
    {
        $exist_cart_pharmacy_id = '';
        $current_cart_pharmacy_id = '';
        if ($user_id != '') {
            $cartItem = \app\models\OrderItems::find()
                ->join('LEFT JOIN', 'pharmacy_orders', 'order_items.pharmacy_order_id= pharmacy_orders.pharmacy_order_id')
                ->join('LEFT JOIN', 'orders', 'orders.order_id = pharmacy_orders.order_id')
                ->where(['is_processed' => [0, 2], 'user_id' => $user_id])
                ->one();
            $exist_cart_pharmacy_id = (!empty($cartItem)) ? $cartItem->product->pharmacy_id : 0;
        }
        if (!empty($products)) {
            foreach ($products as $k => $item) {
                $product = \app\models\Product::findOne($item);
                $current_cart_pharmacy_id = $product->pharmacy_id;
            }
        }
        if ($exist_cart_pharmacy_id == 0) {
            return '0';
        } else if ($exist_cart_pharmacy_id == $current_cart_pharmacy_id) {
            return '0';
        } else {
            return '1';
        }
    }


    public function actionSuggestions($q = null, $lang = "en", $store = "", $pharmacy_id = "", $clinic_id = "", $lab_id = "", $test_id = "", $type = "", $type_id = "")
    {
        $store = $this->getStoreDetails($store);
        $result = [];
        $query = \app\models\Product::find()
            ->select([
                'product.*',
                'pharmacies.name_' . $lang . ' as pharmacy_name',
                '(select count(*) from associated_products where child_id = product.product_id) as count_used_as_child'
            ])
            ->join('LEFT JOIN', 'associated_products', 'associated_products.child_id = product.product_id')
            ->join('LEFT JOIN', 'brands', 'product.brand_id = brands.brand_id')
            ->join('LEFT JOIN', 'pharmacies', 'pharmacies.pharmacy_id = product.pharmacy_id')
            ->where(['product.is_active' => 1, 'product.is_deleted' => 0, 'pharmacies.is_active' => 1]);

        $query->andWhere([
            'OR',
            ['LIKE', 'product.name_en', $q],
            ['LIKE', 'product.name_ar', $q],
        ]);

        if (!empty($pharmacy_id)) {
            $query->andWhere(['product.pharmacy_id' => $pharmacy_id]);
        }
        $query->groupBy('product.product_id');
        $model = $query->asArray()->all();
        if ($type == "P" || $type == "" || ($type == "F" && $type_id != "")) {
            if (!empty($model)) {
                foreach ($model as $row) {
                    if ($type_id == "" || ($type_id == $row['pharmacy_id'] && $type == "F" && $row['pharmacy_id'] != "")) {
                        array_push($result, [
                            'id' => (string) $row['product_id'],
                            'type' => 'P',
                            'name' => $row['name_' . $lang],
                            'product_total' => (string) 1,
                            'price' => $this->convertPrice($row['final_price'], $row['base_currency_id'], $store['currency_id']),
                            'type_id'   => (string) $row['pharmacy_id'],
                            'subtitle_name' => (string) $row['pharmacy_name'],
                            'subtitle_description' => '',
                            'image' => $this->getProductDefaultImage($row['product_id'])
                        ]);
                    }
                }
            }
        }


        if (empty($pharmacy_id) && !empty($q) && ($type == "F" || $type == "") && $type_id == "") {
            $pharmacyModel = \app\models\Pharmacies::find()
                ->select(['pharmacies.*', 'sum(product_id) as total_pharmacy'])
                ->join('LEFT JOIN', 'product', 'pharmacies.pharmacy_id = product.pharmacy_id')
                ->where(['pharmacies.is_active' => 1, 'pharmacies.is_deleted' => 0, 'product.is_active' => 1, 'product.is_deleted' => 0])
                ->groupBy('pharmacies.pharmacy_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'pharmacies.name_en', $q],
                    ['LIKE', 'pharmacies.name_ar', $q],
                ])
                ->all();


            if (!empty($pharmacyModel)) {
                foreach ($pharmacyModel as $row) {
                    array_push(
                        $result,
                        [
                            'id'        => (string) $row->pharmacy_id,
                            'type'      => 'F',
                            'name'      => $row->{'name_' . $lang},
                            'product_total' => (string) 1,
                            'price'     => '',
                            'type_id'   => '',
                            'subtitle_name' => '',
                            'subtitle_description' => '',
                            'image'     => (!empty($row->{'image_' . $lang})) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->{'image_' . $lang}) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png')
                        ]
                    );
                }
            }
        }

        //Lab


        //Clinic
        if (empty($clinic_id) && !empty($q) && ($type == "C" || $type == "H" || $type == "") && $type_id == "") {
            $clinicsModel = \app\models\Clinics::find()
                ->select(['clinics.*', 'sum(clinic_id) as total'])
                ->where(['clinics.is_active' => 1, 'clinics.is_deleted' => 0])
                ->groupBy('clinics.clinic_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'clinics.name_en', $q],
                    ['LIKE', 'clinics.name_ar', $q],
                ])
                ->all();

            if (!empty($clinicsModel)) {
                foreach ($clinicsModel as $row) {
                    if ($type == "" || $type == $row->type) {
                        array_push(
                            $result,
                            [
                                'id'        => (string) $row->clinic_id,
                                'type'      => ($type == "") ? 'C' : $type,
                                'name'      => $row->{'name_' . $lang},
                                'product_total' => (string) 1,
                                'price'     => '',
                                'type_id'   => '',
                                'subtitle_name' => '',
                                'subtitle_description' => '',
                                'image'     => (!empty($row->{'image_' . $lang})) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->{'image_' . $lang}) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png')
                            ]
                        );
                    }
                }
            }
        }

        //Doctor
        if (empty($doctor_id) && !empty($q) && ($type == "D" || $type == "") || (($type == "C" || $type == "H") && $type_id != "")) {
            $doctorsModel = \app\models\Doctors::find()
                ->select(['doctors.*', 'sum(doctor_id) as total'])
                ->where(['doctors.is_active' => 1, 'doctors.is_deleted' => 0])
                ->groupBy('doctors.doctor_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'doctors.name_en', $q],
                    ['LIKE', 'doctors.name_ar', $q],
                ])
                ->all();

            if (!empty($doctorsModel)) {
                foreach ($doctorsModel as $row) {
                    if ($type_id == "" || ($type_id == $row->clinic->clinic_id && ($type == "C" || $type == "H")))
                        array_push(
                            $result,
                            [
                                'id'        => (string) $row->doctor_id,
                                'type'      => 'D',
                                'name'      => $row->{'name_' . $lang},
                                'product_total' => (string) 1,
                                'price'     => '',
                                'type_id'   => (!empty($row->clinic)) ? $row->clinic->clinic_id : '',
                                'subtitle_name' => (!empty($row->clinic)) ? $row->clinic->{'name_' . $lang} : '',
                                'subtitle_description' => '',
                                'image'     => (!empty($row->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png')
                            ]
                        );
                }
            }
        }

        if (empty($lab_id) && !empty($q) && ($type == "L" || $type == "") && $type_id == "") {
            $labModel = \app\models\Labs::find()
                ->select(['labs.*', 'sum(lab_id) as total_lab'])
                ->where(['labs.is_active' => 1, 'labs.is_deleted' => 0])
                ->groupBy('labs.lab_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'labs.name_en', $q],
                    ['LIKE', 'labs.name_ar', $q],
                ])
                ->all();

            if (!empty($labModel)) {
                foreach ($labModel as $row) {
                    array_push(
                        $result,
                        [
                            'id'        => (string) $row->lab_id,
                            'type'      => 'L',
                            'name'      => $row->{'name_' . $lang},
                            'product_total' => (string) 1,
                            'price'     => '',
                            'type_id'   => '',
                            'subtitle_name' => '',
                            'subtitle_description' => '',
                            'image'     => (!empty($row->{'image_' . $lang})) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $row->{'image_' . $lang}) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png')
                        ]
                    );
                }
            }
        }

        //lab test
        if (empty($test_id) && !empty($q) && ($type == "T" || $type == "") || ($type == "L"  && $type_id != "")) {
            $testsModel = \app\models\Tests::find()
                ->select(['tests.*', 'sum(tests.test_id) as total'])
                ->join('left join', 'lab_tests', 'tests.test_id=lab_tests.test_id')
                ->where(['tests.is_active' => 1, 'tests.is_deleted' => 0])
                ->groupBy('tests.test_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'tests.name_en', $q],
                    ['LIKE', 'tests.name_ar', $q],
                ])
                ->all();

            if (!empty($testsModel)) {
                foreach ($testsModel as $row) {
                    $labId = (!empty($row->labTests)) ? $row->labTests[0]->lab_id : "";
                    if ($type_id == "" || ($type_id == $labId && $type == "L"))
                        array_push(
                            $result,
                            [
                                'id'        => (string) $row->test_id,
                                'type'      => 'T',
                                'name'      => $row->{'name_' . $lang},
                                'product_total' => (string) 1,
                                'price'     => '',
                                'type_id'   => (!empty($row->labTests)) ? $row->labTests[0]->lab_id : "",
                                'subtitle_name' => (!empty($row->labTests)) ? $row->labTests[0]->lab->name_en : "",
                                'subtitle_description' => '',
                                'image'     => Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png')
                            ]
                        );
                }
            }
        }

        $this->data = $result;
        if (empty($result)) {
            $this->response_code = 200;
            $this->message = 'No match with your search criteria.please try by another keyword';
            $this->data = [];
        }
        return $this->response_array();
    }
}
