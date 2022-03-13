<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currencies".
 *
 * @property int $currency_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $code_en
 * @property string|null $code_ar
 * @property float|null $currency_rate
 * @property int $is_base_currency
 *
 * @property Product[] $products
 * @property Stores[] $stores
 */
class Currencies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currencies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'code_en'], 'required'],
            [['currency_rate'], 'number'],
            [['is_base_currency'], 'integer'],
            [['name_en', 'name_ar', 'code_en', 'code_ar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'currency_id' => 'Currency ID',
            'name_en' => 'Name En',
            'name_ar' => 'Name Ar',
            'code_en' => 'Code En',
            'code_ar' => 'Code Ar',
            'currency_rate' => 'Currency Rate',
            'is_base_currency' => 'Is Base Currency',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['base_currency_id' => 'currency_id']);
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Stores::className(), ['currency_id' => 'currency_id']);
    }
}
