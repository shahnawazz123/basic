<?php

namespace app\models;

use Yii;

use himiklab\sortablegrid\SortableGridBehavior;

/**
 * This is the model class for table "product".
 *
 * @property int $product_id
 * @property int|null $admin_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $short_description_en
 * @property string|null $short_description_ar
 * @property string|null $description_en
 * @property string|null $description_ar
 * @property string|null $specification_en
 * @property string|null $specification_ar
 * @property string $SKU
 * @property string|null $barcode
 * @property string|null $supplier_barcode
 * @property int|null $sort_order
 * @property int $base_currency_id
 * @property float|null $regular_price
 * @property float $final_price
 * @property float|null $cost_price
 * @property float|null $product_margin
 * @property int $remaining_quantity
 * @property string $posted_date
 * @property string|null $updated_date
 * @property int|null $is_featured
 * @property int $is_active
 * @property int $is_deleted
 * @property int|null $views
 * @property string|null $type
 * @property int|null $brand_id
 * @property int|null $manufacturer_id
 * @property int|null $attribute_set_id
 * @property string|null $new_from_date
 * @property string|null $new_to_date
 * @property string|null $meta_title_en
 * @property string|null $meta_title_ar
 * @property string|null $meta_keywords_en
 * @property string|null $meta_keywords_ar
 * @property string|null $meta_description_en
 * @property string|null $meta_description_ar
 * @property int|null $free_delivery
 * @property float|null $delivery_charges
 * @property int|null $is_new
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $show_as_individual
 * @property int $is_best_seller
 * @property string|null $youtube_id
 * @property int $is_trending
 * @property int|null $pharmacy_id
 * @property string|null $video_name
 * @property int|null $sort_new_arrival
 * @property int|null $sort_trending
 * @property int|null $sort_bs
 * @property string|null $deeplink_url
 *
 * @property AssociatedProducts[] $associatedProducts
 * @property AssociatedProducts[] $associatedProducts0
 * @property Admin $admin
 * @property AttributeSets $attributeSet
 * @property Brands $brand
 * @property Pharmacies $pharmacy
 * @property Manufacturers $manufacturer
 * @property Currencies $baseCurrency
 * @property ProductAttributeValues[] $productAttributeValues
 * @property ProductCategories[] $productCategories
 * @property ProductImages[] $productImages
 * @property ProductStatusHistory[] $productStatusHistories
 * @property ProductStocks[] $productStocks
 * @property RelatedProducts[] $relatedProducts
 * @property RelatedProducts[] $relatedProducts0
 * @property RemainingQuantityNotifyFlag[] $remainingQuantityNotifyFlags
 * @property StoreProducts[] $storeProducts
 * @property WishList[] $wishLists
 */
class Product extends \yii\db\ActiveRecord
{
    public $attribute_values;
    public $quantity;
    public $images;
    public $currency;
    public $name;
    public $image;
    public $currency_code;
    public $quick_product_image;
    public $store_id;
    public $startdate_to_enddate;
    public $qf_regular_price;
    public $qf_final_price;
    public $qf_cost_price;
    public $object_type;
    public $total_amount;
    public $total_quantity;

    public $qf_product_margin;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
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
            [['admin_id', 'sort_order', 'base_currency_id', 'remaining_quantity', 'is_featured', 'is_active', 'is_deleted', 'views', 'brand_id', 'manufacturer_id', 'attribute_set_id', 'free_delivery', 'is_new', 'show_as_individual', 'is_best_seller', 'is_trending', 'pharmacy_id', 'sort_new_arrival', 'sort_trending', 'sort_bs'], 'integer'],
            [['name_en', 'name_ar', 'SKU', 'base_currency_id', 'final_price', 'posted_date', 'brand_id', 'manufacturer_id', 'pharmacy_id'], 'required'],
            [['short_description_en', 'short_description_ar', 'description_en', 'description_ar', 'specification_en', 'specification_ar', 'type', 'meta_keywords_en', 'meta_keywords_ar', 'meta_description_en', 'meta_description_ar', 'deeplink_url'], 'string'],
            [['regular_price', 'final_price', 'product_margin', 'delivery_charges'], 'number'],
            [['posted_date', 'updated_date', 'new_from_date', 'new_to_date', 'start_date', 'end_date'], 'safe'],
            [['name_en', 'name_ar', 'meta_title_en', 'meta_title_ar', 'video_name'], 'string', 'max' => 255],
            [['youtube_id'], 'string', 'max' => 50],
            [['remaining_quantity'], 'match', 'pattern' => '/^[0-9]+$/'],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['admin_id' => 'admin_id']],
            [['attribute_set_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeSets::className(), 'targetAttribute' => ['attribute_set_id' => 'attribute_set_id']],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturers::className(), 'targetAttribute' => ['manufacturer_id' => 'manufacturer_id']],
            [['base_currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currencies::className(), 'targetAttribute' => ['base_currency_id' => 'currency_id']],
            [['type',  'regular_price', 'store_id'], 'required', 'on' => 'create'],
            [['type',  'regular_price', 'store_id'], 'required', 'on' => 'create'],
            [['regular_price', 'store_id'], 'required', 'on' => 'update'],
            [['qf_final_price', 'qf_regular_price'], 'required', 'on' => 'quick-product'],
            [['barcode'], 'checkUniqueBarcode'],
            [['supplier_barcode'], 'checkUniqueSupplierBarcode'],
            [['SKU'], 'checkUniqueSku'],
            [['final_price'], 'compare', 'compareAttribute' => 'regular_price', 'operator' => '<=', 'type' => 'number', 'on' => 'create'],
            [['final_price'], 'compare', 'compareAttribute' => 'regular_price', 'operator' => '<=', 'type' => 'number', 'on' => 'update'],
            [['final_price', 'regular_price', 'qf_regular_price', 'qf_final_price'], 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => "Price should not be negative"],
            [['qf_final_price'], 'compare', 'compareAttribute' => 'qf_regular_price', 'operator' => '<=', 'type' => 'number', 'on' => 'quick-product'],
            ['quantity', 'compare', 'compareValue' => '0', 'operator' => '>'],
            [['quantity'], 'integer'],
        ];
    }

    public function checkUniqueBarcode($attribute, $params, $validator)
    {
        $model = Product::find()->where(['barcode' => $this->barcode, 'is_deleted' => 0]);
        if (!empty($this->product_id)) {
            $model->andWhere(['<>', 'product_id', $this->product_id]);
        }
        $model = $model->one();
        if (!empty($model)) {
            $this->addError($attribute, 'This barcode has already been taken.');
        }
    }

    public function checkUniqueSupplierBarcode($attribute, $params, $validator)
    {
        $model = Product::find()->where(['supplier_barcode' => $this->supplier_barcode, 'is_deleted' => 0]);
        if (!empty($this->product_id)) {
            $model->andWhere(['<>', 'product_id', $this->product_id]);
        }
        $model = $model->one();
        if (!empty($model)) {
            $this->addError($attribute, 'This barcode has already been taken.');
        }
    }
    public function checkUniqueSku($attribute, $params, $validator)
    {
        $model = Product::find()->where(['SKU' => $this->SKU, 'is_deleted' => 0]);
        if (!empty($this->product_id)) {
            $model->andWhere(['<>', 'product_id', $this->product_id]);
        }
        $model = $model->one();
        if (!empty($model)) {
            $this->addError($attribute, 'This SKU has already been taken.');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'admin_id' => 'Admin',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'short_description_en' => 'Short Description in English',
            'short_description_ar' => 'Short Description in Arabic',
            'description_en' => 'Description in English',
            'description_ar' => 'Description in Arabic',
            'specification_en' => 'Specification in English',
            'specification_ar' => 'Specification in Arabic',
            'SKU' => 'Sku',
            'barcode' => 'Barcode',
            'supplier_barcode' => 'Supplier Barcode',
            'sort_order' => 'Sort Order',
            'base_currency_id' => 'Base Currency',
            'regular_price' => 'Regular Price',
            'final_price' => 'Final Price',
            'cost_price' => 'Cost Price',
            'product_margin' => 'Product Margin',
            'remaining_quantity' => 'Quantity',
            'posted_date' => 'Posted Date',
            'updated_date' => 'Updated Date',
            'is_featured' => 'Is Featured',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'views' => 'Views',
            'type' => 'Type',
            'brand_id' => 'Brand',
            'manufacturer_id' => 'Manufacturer',
            'attribute_set_id' => 'Attribute Set',
            'new_from_date' => 'New From Date',
            'new_to_date' => 'New To Date',
            'meta_title_en' => 'Meta Title in English',
            'meta_title_ar' => 'Meta Title in Arabic',
            'meta_keywords_en' => 'Meta Keywords in English',
            'meta_keywords_ar' => 'Meta Keywords in Arabic',
            'meta_description_en' => 'Meta Description in English',
            'meta_description_ar' => 'Meta Description in Arabic',
            'free_delivery' => 'Free Delivery',
            'delivery_charges' => 'Delivery Charges',
            'is_new' => 'Is New',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'show_as_individual' => 'Show As Individual',
            'is_best_seller' => 'Is Best Seller',
            'youtube_id' => 'Youtube',
            'is_trending' => 'Is Trending',
            'pharmacy_id' => 'Pharmacy',
            'video_name' => 'Video Name',
            'sort_new_arrival' => 'Sort New Arrival',
            'sort_trending' => 'Sort Trending',
            'sort_bs' => 'Sort Bs',
            'deeplink_url' => 'Deeplink Url',
            'store_id' => 'Store',
            'qf_regular_price' => 'Regular Price',
            'qf_final_price' => 'Final Price',
            'qf_cost_price' => 'Cost Price',
            'qf_product_margin' => 'Product Margin',
        ];
    }

    /**
     * Gets query for [[AssociatedProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociatedProducts()
    {
        return $this->hasMany(AssociatedProducts::className(), ['parent_id' => 'product_id']);
    }

    /**
     * Gets query for [[AssociatedProducts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociatedProducts0()
    {
        return $this->hasMany(AssociatedProducts::className(), ['child_id' => 'product_id']);
    }

    /**
     * Gets query for [[Admin]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['admin_id' => 'admin_id']);
    }

    /**
     * Gets query for [[AttributeSet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeSet()
    {
        return $this->hasOne(AttributeSets::className(), ['attribute_set_id' => 'attribute_set_id']);
    }

    /**
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brands::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * Gets query for [[Pharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[Manufacturer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManufacturer()
    {
        return $this->hasOne(Manufacturers::className(), ['manufacturer_id' => 'manufacturer_id']);
    }

    /**
     * Gets query for [[BaseCurrency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseCurrency()
    {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'base_currency_id']);
    }

    /**
     * Gets query for [[ProductAttributeValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductAttributeValues()
    {
        return $this->hasMany(ProductAttributeValues::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductCategories()
    {
        return $this->hasMany(ProductCategories::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductImages()
    {
        return $this->hasMany(ProductImages::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductStatusHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductStatusHistories()
    {
        return $this->hasMany(ProductStatusHistory::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[ProductStocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductStocks()
    {
        return $this->hasMany(ProductStocks::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[RelatedProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedProducts()
    {
        return $this->hasMany(RelatedProducts::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[RelatedProducts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedProducts0()
    {
        return $this->hasMany(RelatedProducts::className(), ['related_id' => 'product_id']);
    }

    /**
     * Gets query for [[RemainingQuantityNotifyFlags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRemainingQuantityNotifyFlags()
    {
        return $this->hasMany(RemainingQuantityNotifyFlag::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[StoreProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProducts()
    {
        return $this->hasMany(StoreProducts::className(), ['product_id' => 'product_id']);
    }

    /**
     * Gets query for [[WishLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWishLists()
    {
        return $this->hasMany(WishList::className(), ['product_id' => 'product_id']);
    }

    public function getProductImage($productId)
    {
        /*$model = Product::findOne($productId);
        if (!empty($model)) {
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
            }else{
                $imageModelQuery->andFilterWhere(['=', 'product.product_id', $productId]);
            }
            $imageModelQuery->orderBy(['sort_order' => SORT_ASC]);
            $imageModel = $imageModelQuery->asArray()
                    ->one();
            $image = !empty($imageModel) ? $imageModel['image'] : "";
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
                        ->asArray()
                        ->one();
            }
            if (!empty($image)) {
                $image = $image['image'];
            }
        }*/
        $model = Product::findOne($productId);

        if (!empty($model)) {
            $image = isset($model->getProductImages()->orderBy(['sort_order' => SORT_ASC])->one()->image) ? $model->getProductImages()->orderBy(['sort_order' => SORT_ASC])->one()->image : "";
        } else {
            return '';
        }
        return isset($image) && !empty($image) ? \app\helpers\AppHelper::getUploadUrl() . $image : "";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllImages($id)
    {
        $model = Product::findOne($id);
        $query = \app\models\ProductImages::find()
            ->select(['product_images.*', '(IF(product.type=\'G\',0,1)) as type_order'])
            ->join('LEFT JOIN', 'product', 'product.product_id = product_images.product_id')
            ->where(['product.is_deleted' => 0]);
        if ($model->type == "G") {
            $query->join('LEFT JOIN', 'associated_products', 'product.product_id = associated_products.child_id');
            $query->andFilterWhere([
                'OR',
                ['=', 'associated_products.parent_id', $id],
                ['=', 'product.product_id', $id]
            ]);
        } else {
            $query->andFilterWhere(['=', 'product.product_id', $id]);
        }
        $query->orderBy(['product_images.sort_order' => SORT_ASC]);

        return $query;
    }
}
