<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

/**
 * Description of ProductHelper
 *
 * @author Akram Hossain <akram_cse@yahoo.com>
 */

use app\models\Promotions;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\AttributeSets;
use app\models\Product;
use app\models\AttributeValues;

class ProductHelper
{

    static function getAttributeSetList()
    {
        $model = AttributeSets::find()
            ->join('inner join', 'attribute_set_groups', 'attribute_set_groups.attribute_set_id = attribute_sets.attribute_set_id')
            ->join('inner join', 'attributes', 'attribute_set_groups.attribute_id = attributes.attribute_id')
            ->join('inner join', 'attribute_values', 'attribute_values.attribute_id = attributes.attribute_id')
            ->orderBy(['name_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'attribute_set_id', 'name_en');

        return $list;
    }

    static function getAttibuteValueList()
    {
        $model = AttributeValues::find()
            ->orderBy(['value_en' => SORT_ASC])
            ->all();

        $list = ArrayHelper::map($model, 'attribute_value_id', 'value_en');

        return $list;
    }

    static function getPromotions()
    {
        $model = Promotions::find()->where(['is_deleted' => 0])->all();
        $list = ArrayHelper::map($model, 'promotion_id', 'code');
        return $list;
    }

    static function getBrandList()
    {
        $model = \app\models\Brands::find()->where(['is_active' => 1, 'is_deleted' => 0])->all();

        $list = ArrayHelper::map($model, 'brand_id', 'name_en');

        return $list;
    }

    static function getManufacturerList()
    {
        $model = \app\models\Manufacturers::find()->where(['is_active' => 1, 'is_deleted' => 0])->all();
        $list = ArrayHelper::map($model, 'manufacturer_id', 'name_en');
        return $list;
    }

    static function getPharmacyList()
    {
        $query = \app\models\Pharmacies::find()->where(['is_active' => 1, 'is_deleted' => 0]);
        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andwhere(['pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
        }

        $model = $query->all();

        $list = ArrayHelper::map($model, 'pharmacy_id', 'name_en');

        return $list;
    }

    static function getCurrencyList()
    {
        $model = \app\models\Currencies::find()->all();

        $list = ArrayHelper::map($model, 'currency_id', 'name_en');

        return $list;
    }

    static function getTypeName($name)
    {
        if ($name == 'S') {
            return 'Simple';
        } elseif ($name == 'G') {
            return 'Grouped';
        } elseif ($name == 'C') {
            return 'Configured';
        }
    }

    static function getAttibuteValueListBySet($attset)
    {
        $model = \app\models\AttributeSetGroups::find()
            ->select('attribute_set_groups.attribute_set_id,attribute_set_groups.attribute_id,attribute_values.attribute_value_id,attribute_values.value_en')
            ->join('left join', 'attribute_values', 'attribute_values.attribute_id = attribute_set_groups.attribute_id')
            ->where('attribute_set_groups.attribute_set_id = ' . $attset)
            ->asArray()
            ->all();

        $list = ArrayHelper::map($model, 'attribute_value_id', 'value_en');

        return $list;
    }

    static function getXeditableCurrencyList()
    {
        $model = \app\models\Currencies::find()->all();
        $list = [];
        foreach ($model as $currency) {
            $d['value'] = $currency->currency_id;
            $d['text'] = $currency->code;
            array_push($list, $d);
        }
        return $list;
    }

    static function getXeditableBrandList()
    {
        $model = \app\models\Brands::find()->all();
        $list = [];
        foreach ($model as $brand) {
            $d['value'] = $brand->brand_id;
            $d['text'] = $brand->name_en;
            array_push($list, $d);
        }
        return $list;
    }

    static function getXeditableAttributeSetList()
    {
        $model = AttributeSets::find()
            ->orderBy(['name_en' => SORT_ASC])
            ->all();
        $list = [];
        foreach ($model as $attSet) {
            $d['value'] = $attSet->attribute_set_id;
            $d['text'] = $attSet->name_en;
            array_push($list, $d);
        }
        return $list;
    }

    static function countProductReviews($id)
    {
        $model = \app\models\ProductReviews::find()
            ->where('product_id = ' . $id)
            ->count();
        return $model;
    }

    static function countProductPendingReviews($id)
    {
        $model = \app\models\ProductReviews::find()
            ->where('product_id = ' . $id . ' AND is_approved = 0')
            ->count();
        return $model;
    }

    static function countPendingReviews()
    {
        $model = \app\models\ProductReviews::find()
            ->where('is_approved = 0')
            ->count();
        return $model;
    }

    static function getAllEnableProduct()
    {
        $model = Product::find()
            ->where(['is_active' => 1])
            ->all();
        $list = ArrayHelper::map($model, 'product_id', 'name_en');

        return $list;
    }

    static function getStatus()
    {
        $model = \app\models\ProductStatus::find()
            ->all();
        $list = ArrayHelper::map($model, 'product_status_id', 'status_name_en');

        return $list;
    }

    static function getProductDefaultImage($productId)
    {
        $model = Product::findOne($productId);

        if (!empty($model)) {
            $image = isset($model->getProductImages()->orderBy(['sort_order' => SORT_ASC])->one()->image) ? $model->getProductImages()->orderBy(['sort_order' => SORT_ASC])->one()->image : "";
        } else {
            return '';
        }

        if (empty($image)) {
            $attribute = $model->getProductAttributeValues()
                ->join('INNER JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                ->join('INNER JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                ->orderBy(['attributes.sort_order' => SORT_ASC])->one();

            if (!empty($attribute)) {
                $image = \app\models\ProductImages::find()
                    ->join('LEFT JOIN', 'associated_products AS a', 'a.child_id = product_images.product_id')
                    ->join('LEFT JOIN', 'associated_products as b', 'a.parent_id = b.parent_id')
                    ->join('LEFT JOIN', 'product_attribute_values', 'a.child_id = product_attribute_values.product_id')
                    ->where(['b.child_id' => $model->product_id, 'attribute_value_id' => $attribute->attribute_value_id])
                    ->orderBy(['sort_order' => SORT_ASC])
                    ->asArray()->one();
            }

            if (empty($image)) {
                $image = \app\models\ProductImages::find()
                    ->join('LEFT JOIN', 'associated_products', 'associated_products.parent_id  = product_images.product_id')
                    ->where(['associated_products.child_id' => $model->product_id])
                    ->orderBy(['sort_order' => SORT_ASC])
                    ->asArray()->one();
            }

            if (!empty($image)) {
                $image = $image['image'];
            }
        }

        return isset($image) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $image) : "";
    }

    static function generateSkuProduct()
    {
        if (\Yii::$app->session['_wishlistAuth'] == 1) {
            $alphabet = 'ADM';
            $countProduct = Product::find()
                ->where(['is_deleted' => 0, 'show_as_individual' => 1])
                ->count();

            $code = self::checkCodeExist($alphabet, $countProduct);

            return $code;
        }
    }

    static function checkCodeExist($alphabet, $countProduct)
    {
        if ($countProduct < 1) {
            $counter = '0001';
        } elseif ($countProduct >= 1 && $countProduct <= 9) {
            $inc = $countProduct + 1;
            $counter = '000' . $inc;
        } elseif ($countProduct >= 10 && $countProduct <= 99) {
            $inc = $countProduct + 1;
            $counter = '00' . $inc;
        } elseif ($countProduct >= 100 && $countProduct <= 999) {
            $inc = $countProduct + 1;
            $counter = '0' . $inc;
        } else {
            $inc = $countProduct + 1;
            $counter = $inc;
        }
        $code = $alphabet . $counter;

        $model = Product::find()
            ->where(['SKU' => $code])
            ->asArray()
            ->one();

        if (empty($model)) {
            return $code;
        } else {
            return self::checkCodeExist($alphabet, $countProduct + 1);
        }
    }

    static function getModelYear()
    {
        $model = \app\models\ModelYears::find()
            ->select(['model_years.model_year_id', 'CONCAT(models.name_en," ",years.name) as model_name'])
            ->join('left join', 'models', 'models.model_id = model_years.model_id')
            ->join('left join', 'years', 'years.year_id = model_years.year_id')
            ->where(['model_years.is_deleted' => 0])
            ->orderBy(['model_name' => SORT_ASC])
            ->asArray()
            ->all();

        $list = ArrayHelper::map($model, 'model_year_id', 'model_name');

        return $list;
    }

    static function getSupplierList()
    {
        $model = \app\models\Suppliers::find()
            ->where(['is_deleted' => 0])
            ->all();

        $list = ArrayHelper::map($model, 'supplier_id', 'name_en');

        return $list;
    }

    static function getAllStoreList()
    {
        $model = \app\models\Stores::find()
            ->where(['is_deleted' => 0])
            ->all();

        $list = ArrayHelper::map($model, 'store_id', 'name_en');

        return $list;
    }

    static function getSelectedStore($pid)
    {
        $model = \app\models\StoreProducts::find()
            ->where(['product_id' => $pid])
            ->all();
        $data = [];
        if (!empty($model)) {
            foreach ($model as $row) {
                array_push($data, $row->store_id);
            }
        }
        //debugPrint($data);
        return $data;
    }

    static function getAttributesByProduct($product, $considerGrouped = true)
    {
        $data = SiteHelper::getProductAttributeValues($product, 'en', $considerGrouped);
        $result = [];
        foreach ($data as $row) {
            foreach ($row['attributes'] as $att) {
                if (!in_array($att['value'], $result)) {
                    array_push($result, $att['value']);
                }
            }
        }
        return $result;
    }

    static function getSimpleAttributes($product)
    {
        $data = SiteHelper::getSimpleAttributeValues($product);
        $result = [];
        foreach ($data as $row) {
            foreach ($row['attributes'] as $att) {
                if (!in_array($att['value'], $result)) {
                    array_push($result, $att['value']);
                }
            }
        }
        return $result;
    }

    public static function generateBarcodeProduct()
    {
        $model = \app\models\Product::find()
            ->select(['MAX(`barcode`) AS barcode'])
            ->asArray()
            ->one();
        if (!empty($model) && isset($model['barcode']) && $model['barcode'] != 0) {
            return $model['barcode'] + 1;
        } else {
            return rand(11111111, 99999999);
        }
    }

    static function createDeeplinkUrl($id, $name, $description, $image)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api2.branch.io/v1/url");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_POST, 1);

        $data = [
            'branch_key' => Yii::$app->params['branch_key'],
            'channel' => 'Bazma',
            'campaign' => 'New Product',
            'data' => [
                '$canonical_identifier' => (string) $id,
                '$og_title' => $name,
                '$og_description' => $description,
                '$og_image_url' => $image,
                '$desktop_url' => 'https://www.shop-twl.com/product/detail/' . $id
            ],
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = [
            'content-type: application/json',
            'Postman-Token: df319f7f-a0a0-4cd6-9bf8-3456caad74d0' . time(),
            'cache-control: no-cache'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        //$info = curl_getinfo($ch);
        //$err = curl_error($ch);

        curl_close($ch);

        $url = json_decode($output);
        return (isset($url->url)) ? $url->url : "";
    }

    static function getProducts()
    {
        $model = Product::findAll(['is_active' => 1, 'is_deleted' => 0]);
        return ArrayHelper::map($model, 'product_id', 'name_en');
    }

    static function getSeasonList()
    {
        $model = \app\models\Seasons::find()->where(['is_deleted' => 0])->all();

        $list = ArrayHelper::map($model, 'season_id', 'name_en');

        return $list;
    }
}
