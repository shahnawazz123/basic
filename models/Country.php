<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country".
 * 
 * @property int $country_id
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $nicename
 * @property string|null $iso
 * @property string|null $iso3
 * @property string|null $numcode
 * @property string|null $phonecode
 * @property string|null $flag
 * @property string|null $currency_en
 * @property string|null $currency_ar
 * @property float|null $shipping_cost
 * @property float $express_shipping_cost
 * @property float|null $cod_cost
 * @property int|null $is_cod_enable
 * @property string|null $delivery_interval
 * @property string|null $express_delivery_interval
 * @property float|null $free_delivery_limit
 * @property float|null $vat
 * @property string $is_deleted
 * @property int $is_active
 * @property int $standard_delivery_items
 * @property float $standard_delivery_charge
 * @property int $express_delivery_items
 * @property float $express_delivery_charge
 * @property float $standard_shipping_cost_actual
 * @property float $express_shipping_cost_actual
 * @property float $custom_duty
 * @property float $min_customs_amount
 * @property float $min_vat_amount
 * @property float $custom_admin_fee
 * @property float $min_custom_admin_amount
 * @property string|null $delivery_note_en
 * @property string|null $delivery_note_ar
 * @property int|null $is_id_mandatory
 *
 * @property PromotionCountries[] $promotionCountries
 * @property State[] $states
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en','name_ar'],'required'],
            [['shipping_cost', 'express_shipping_cost', 'cod_cost', 'free_delivery_limit', 'vat', 'standard_delivery_charge', 'express_delivery_charge', 'standard_shipping_cost_actual', 'express_shipping_cost_actual', 'custom_duty', 'min_customs_amount', 'min_vat_amount', 'custom_admin_fee', 'min_custom_admin_amount'], 'number'],
            [['is_cod_enable', 'is_active', 'standard_delivery_items', 'express_delivery_items', 'is_id_mandatory'], 'integer'],
            [['name_en', 'name_ar', 'nicename', 'iso', 'iso3', 'numcode', 'phonecode', 'currency_en', 'currency_ar', 'delivery_interval', 'express_delivery_interval', 'is_deleted'], 'string', 'max' => 50],
            [['flag'], 'string', 'max' => 150],
            [['delivery_note_en', 'delivery_note_ar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'country_id' => 'Country ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'nicename' => 'Nicename',
            'iso' => 'ISO',
            'iso3' => 'ISO 3',
            'numcode' => 'Num Code',
            'phonecode' => 'Phone Code',
            'flag' => 'Flag',
            'currency_en' => 'Currency in English',
            'currency_ar' => 'Currency in Arabic',
            'shipping_cost' => 'Shipping Cost',
            'express_shipping_cost' => 'Express Shipping Cost',
            'cod_cost' => 'Cod Cost',
            'is_cod_enable' => 'Is Cod Enable',
            'delivery_interval' => 'Delivery Interval',
            'express_delivery_interval' => 'Express Delivery Interval',
            'free_delivery_limit' => 'Free Delivery Limit',
            'vat' => 'Vat',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'standard_delivery_items' => 'Standard Delivery Items',
            'standard_delivery_charge' => 'Standard Delivery Charge',
            'express_delivery_items' => 'Express Delivery Items',
            'express_delivery_charge' => 'Express Delivery Charge',
            'standard_shipping_cost_actual' => 'Standard Shipping Cost Actual',
            'express_shipping_cost_actual' => 'Express Shipping Cost Actual',
            'custom_duty' => 'Custom Duty',
            'min_customs_amount' => 'Min Customs Amount',
            'min_vat_amount' => 'Min Vat Amount',
            'custom_admin_fee' => 'Custom Admin Fee',
            'min_custom_admin_amount' => 'Min Custom Admin Amount',
            'delivery_note_en' => 'Delivery Note En',
            'delivery_note_ar' => 'Delivery Note Ar',
            'is_id_mandatory' => 'Is Id Mandatory',
        ];
    }

    /**
     * Gets query for [[PromotionCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionCountries()
    {
        return $this->hasMany(PromotionCountries::className(), ['country_id' => 'country_id']);
    }

    /**
     * Gets query for [[States]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(State::className(), ['country_id' => 'country_id']);
    }
}
