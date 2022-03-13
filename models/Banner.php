<?php

namespace app\models;


use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\helpers\Url;
/**
 * This is the model class for table "banner".
 *
 * @property int $banner_id
 * @property string $image_ar
 * @property string $image_en
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $sub_title_en
 * @property string|null $sub_title_ar
 * @property string $link_type
 * @property int|null $link_id
 * @property int $is_active
 * @property int $is_deleted
 * @property string|null $url
 * @property int $sort_order
 */
class Banner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banner';
    }

    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'

            ], 
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en','name_ar','image_ar', 'image_en', 'link_type','position'], 'required'],
            [['link_type', 'url'], 'string'],
            [['link_id', 'is_active', 'is_deleted', 'sort_order'], 'integer'],
            [['image_ar', 'image_en'], 'string', 'max' => 100],
            [['name_en', 'name_ar', 'sub_title_en', 'sub_title_ar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'banner_id' => 'Banner ID',
            'image_ar' => 'Image in Arabic',
            'image_en' => 'Image in English',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'sub_title_en' => 'Sub Title in English',
            'sub_title_ar' => 'Sub Title in Arabic',
            'link_type' => 'Link Type',
            'link_id' => 'Link ID',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'url' => 'Url',
            'sort_order' => 'Sort Order',
        ];
    }
}
