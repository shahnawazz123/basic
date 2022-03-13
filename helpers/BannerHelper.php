<?php
namespace app\helpers;
/**
 * Description of BannerHelper
 *
 * @author Vasim oathan
 */
class BannerHelper {

    static function getBannerTypeList() {
        return [
            'P' => 'Product',
            'FP' => 'Featured Product',
            'BR' => 'Brand',
            'L' => 'External link',
        ];
    }
    public static $bannerTypes = [
        'C' => 'Clinic ',
        'H' => 'Hospital ',
        'D' => 'Doctors',
        'L' => 'Labs',
        'F' => 'Pharmacy',
    
    ];

    static function getBrandList() {
        $model = \app\models\Brands::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'brand_id', 'name_en');
        return $list;
    }

    static function getRootCategory() {
        $roots = \app\models\Category::find()
            ->where(['is_deleted' => 0, 'is_active' => 1])
            ->roots()
            ->all();
        $list = \yii\helpers\ArrayHelper::map($roots, 'category_id', 'name_en');
        return $list;
    }

    static function getRecursiveCategory() {
        $roots = \app\models\Category::find()
                ->where(['is_deleted' => 0, 'is_active' => 1, 'type' => 'P'])
                ->roots()
                ->all();

        $result = [];
        foreach ($roots as $row) {
            $d['category_id'] = $row->category_id;
            $d['name'] = $row->name_en;
            $d['children'] = self::getChildCategory($row);
            array_push($result, $d);
        }
        //$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($result));
        $nonested = self::makeNonNestedArray($result);
        $list = \yii\helpers\ArrayHelper::map($nonested, 'id', 'name');
        return $list;
    }

    static function makeNonNestedArray($data,$pass=0) {
        $result = [];
        foreach ($data as $key => $value) {
            $d = [
                'id' => $value['category_id'],
                'name' => $value['name'],
            ];
            array_push($result, $d);

            //debugPrint($result);
            if (array_key_exists('children', $value)) {
                
                $nested = self::makeNonNestedArray($value['children'],$pass);
                if(!empty($nested))
                {
                    $count = $pass+1;
                    foreach ($nested as $n)
                    {
                        $n = [
                            'id' => $n['id'],
                            'name' => str_repeat("--", $count).$n['name']
                        ];
                        array_push($result, $n);
                    }
                }
                
            }
        }
        return $result;
    }

    static function getChildCategory($parent) {
        $children = $parent->children(1)->all();
        $result = [];
        foreach ($children as $row) {
            if ($row->is_deleted == 0) {
                $d['category_id'] = $row->category_id;
                $d['name'] = $row->name_en;
                $count = $row->children()->count();
                if ($count > 0) {
                    $d['children'] = self::getChildCategory($row);
                }
                array_push($result, $d);
            }
        }
        return $result;
    }
    
}
