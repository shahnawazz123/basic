<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

/**
 * Description of SiteHelper
 *
 * @author akram
 */
class SiteHelper
{

    static function getProductAttributeValues($product, $lang = 'en', $considerGrouped = true) {
        $tmp = array();
        if ($product->type == 'S' || $considerGrouped == false) {
            $model = \app\models\ProductAttributeValues::find()
                    ->select(['product_attribute_values.attribute_value_id', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value", 'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en", 'attributes.code AS attribute_code'])
                    ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                    ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                    ->where(['product_id' => $product->product_id])
                    ->orderBy(['attribute_values.attribute_id' => SORT_ASC])
                    ->asArray()
                    ->all();
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $tmp[$row['attribute_id']] = [
                            'type' => str_replace(" ", "_", $row['attribute']),
                            'target_element' => str_replace(" ", "_", $row['attribute_en']),
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
                $products[] = $p->child_id;
            }
            $model = \app\models\ProductAttributeValues::find()
                    ->select(['product_attribute_values.attribute_value_id', "IF(STRCMP('$lang', 'en'), `attribute_values`.`value_ar`, `attribute_values`.`value_en`) AS attribute_value",
                        'attribute_values.attribute_id', "IF(STRCMP('$lang', 'en'), `attributes`.`name_ar`, `attributes`.`name_en`) AS attribute", "attributes.name_en as attribute_en",
                        'attributes.name_en AS attribute_text',
                        'attributes.code AS attribute_code'])
                    ->join('LEFT JOIN', 'attribute_values', 'attribute_values.attribute_value_id = product_attribute_values.attribute_value_id')
                    ->join('LEFT JOIN', 'attributes', 'attributes.attribute_id = attribute_values.attribute_id')
                    ->where(['product_id' => $products])
                    ->asArray()
                    ->all();
            //debugPrint($model);
            if (!empty($model)) {
                foreach ($model as $row) {
                    if (!isset($tmp[$row['attribute_id']])) {
                        $att = str_replace(" ", "_", $row['attribute']);
                        $tmp[$row['attribute_id']] = [
                            'type' => $att,
                            //'attribute_text' => $row['attribute_text'],
                            'target_element' => str_replace(" ", "_", $row['attribute_en']),
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

}
