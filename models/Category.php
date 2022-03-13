<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\treemanager\models\TreeTrait;

/**
 * This is the model class for table "category".
 *
 * @property int $category_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image
 * @property string|null $image_ar
 * @property int $is_active
 * @property int $is_deleted
 * @property int|null $include_in_navigation_menu
 * @property string|null $meta_title_en
 * @property string|null $meta_title_ar
 * @property string|null $meta_keywords_en
 * @property string|null $meta_keywords_ar
 * @property string|null $meta_description_en
 * @property string|null $meta_description_ar
 * @property int $show_in_home
 * @property int|null $root
 * @property int $lft
 * @property int $rgt
 * @property int $lvl
 * @property string|null $icon
 * @property string|null $icon_ar
 * @property int $icon_type
 * @property int $active
 * @property int $selected
 * @property int $disabled
 * @property int $readonly
 * @property int $visible
 * @property int $collapsed
 * @property int $movable_u
 * @property int $movable_d
 * @property int $movable_l
 * @property int $movable_r
 * @property int $removable
 * @property int $removable_all
 * @property int $in_boutique
 * @property int $hide_category_in_app
 * @property string|null $deeplink_url
 * @property string|null $type
 *
 * @property ClinicCategories[] $clinicCategories
 * @property DoctorCategories[] $doctorCategories
 * @property ProductCategories[] $productCategories
 * @property TestCategories[] $testCategories
 */
class Category extends \yii\db\ActiveRecord
{
    public $push_message, $push_title,$test_id,$test_name_en,$test_name_ar,$is_home_service,$test_price;
    
    use TreeTrait {
        isDisabled as parentIsDisabled; // note the alias
    }
    
    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik  ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery

    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;

    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;

    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];

    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];

    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar','type'], 'required'],
            [['is_active', 'is_deleted', 'include_in_navigation_menu', 'show_in_home', 'root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'in_boutique', 'hide_category_in_app'], 'integer'],
            [['meta_keywords_en', 'meta_keywords_ar', 'meta_description_en', 'meta_description_ar', 'deeplink_url', 'type'], 'string'],
            [['name_en', 'name_ar', 'image', 'image_ar'], 'string', 'max' => 100],
            [['meta_title_en', 'meta_title_ar', 'icon', 'icon_ar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'Category ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image' => 'Image in English',
            'image_ar' => 'Image in Arabic',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'include_in_navigation_menu' => 'Include In Navigation Menu',
            'meta_title_en' => 'Meta Title in English',
            'meta_title_ar' => 'Meta Title in Arabic',
            'meta_keywords_en' => 'Meta Keywords in English',
            'meta_keywords_ar' => 'Meta Keywords in Arabic',
            'meta_description_en' => 'Meta Description in English',
            'meta_description_ar' => 'Meta Description in Arabic',
            'show_in_home' => 'Show In Home',
            'root' => 'Root',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'lvl' => 'Lvl',
            'icon' => 'Icon',
            'icon_ar' => 'Icon in Arabic',
            'icon_type' => 'Icon Type',
            'active' => 'Active',
            'selected' => 'Selected',
            'disabled' => 'Disabled',
            'readonly' => 'Readonly',
            'visible' => 'Visible',
            'collapsed' => 'Collapsed',
            'movable_u' => 'Movable U',
            'movable_d' => 'Movable D',
            'movable_l' => 'Movable L',
            'movable_r' => 'Movable R',
            'removable' => 'Removable',
            'removable_all' => 'Removable All',
            'in_boutique' => 'In Boutique',
            'hide_category_in_app' => 'Hide Category In App',
            'deeplink_url' => 'Deeplink Url',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[ClinicCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinicCategories()
    {
        return $this->hasMany(ClinicCategories::className(), ['category_id' => 'category_id']);
    }

    /**
     * Gets query for [[DoctorCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorCategories()
    {
        return $this->hasMany(DoctorCategories::className(), ['category_id' => 'category_id']);
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategories::className(), ['category_id' => 'category_id']);
    }

    /**
     * Gets query for [[TestCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestCategories()
    {
        return $this->hasMany(TestCategories::className(), ['category_id' => 'category_id']);
    }
}
