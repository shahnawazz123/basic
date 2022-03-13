<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stores".
 *
 * @property int $store_id
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $flag
 * @property string|null $code
 * @property int $currency_id
 * @property int $is_deleted
 * @property int $is_default
 * @property int|null $sort_order
 *
 * @property Orders[] $orders
 * @property StoreDoctors[] $storeDoctors
 * @property StoreLabs[] $storeLabs
 * @property StorePharmacies[] $storePharmacies
 * @property StoreProducts[] $storeProducts
 * @property Currencies $currency
 */
class Stores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id'], 'required'],
            [['currency_id', 'is_deleted', 'is_default', 'sort_order'], 'integer'],
            [['name_en', 'name_ar', 'flag', 'code'], 'string', 'max' => 50],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_id' => 'Store',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'flag' => 'Flag',
            'code' => 'Code',
            'currency_id' => 'Currency ',
            'is_deleted' => 'Is Deleted',
            'is_default' => 'Is Default',
            'sort_order' => 'Sort Order',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[StoreDoctors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreDoctors()
    {
        return $this->hasMany(StoreDoctors::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[StoreLabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreLabs()
    {
        return $this->hasMany(StoreLabs::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[StorePharmacies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStorePharmacies()
    {
        return $this->hasMany(StorePharmacies::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[StoreProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProducts()
    {
        return $this->hasMany(StoreProducts::className(), ['store_id' => 'store_id']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'currency_id']);
    }
}
