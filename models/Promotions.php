<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotions".
 *
 * @property int $promotion_id
 * @property string $title_en
 * @property string $title_ar
 * @property string $code
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $promo_type
 * @property int|null $promo_count
 * @property int $discount
 * @property string|null $promo_for
 * @property float|null $minimum_order
 * @property int|null $shipping_included
 * @property string|null $registration_start_date
 * @property string|null $registration_end_date
 * @property int $is_deleted
 *
 * @property Orders[] $orders
 * @property PromotionBrands[] $promotionBrands
 * @property PromotionClinics[] $promotionClinics
 * @property PromotionCountries[] $promotionCountries
 * @property PromotionDoctors[] $promotionDoctors
 * @property PromotionLabs[] $promotionLabs
 * @property PromotionUsers[] $promotionUsers
 * @property PromotionPharmacy[] $promotionPharmacy
 * @property UserPromotions[] $userPromotions
 */
class Promotions extends \yii\db\ActiveRecord
{
    public $link_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title_en', 'title_ar', 'code', 'promo_type', 'discount'], 'required'],
            [['start_date', 'end_date', 'registration_start_date', 'registration_end_date'], 'safe'],
            [['promo_type', 'promo_for'], 'string'],
            [['discount','minimum_order'], 'match', 'pattern' => '/^[0-9]+$/'],
            [['promo_count'], 'match', 'pattern' => '/^[0-9]+$/'],
            [['promo_count', 'discount', 'shipping_included', 'is_deleted'], 'integer'],
            [['minimum_order'], 'number'],
            [['title_en', 'title_ar'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_id' => 'Promotion ID',
            'title_en' => 'Title in English',
            'title_ar' => 'Title in Arabic',
            'code' => 'Code',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'promo_type' => 'Promo Type',
            'promo_count' => 'Promo Count',
            'discount' => 'Discount',
            'promo_for' => 'Promo For',
            'minimum_order' => 'Minimum Order',
            'shipping_included' => 'Shipping Included',
            'registration_start_date' => 'Registration Start Date',
            'registration_end_date' => 'Registration End Date',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionBrands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionBrands()
    {
        return $this->hasMany(PromotionBrands::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionClinics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionClinics()
    {
        return $this->hasMany(PromotionClinics::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionCountries()
    {
        return $this->hasMany(PromotionCountries::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionDoctors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionDoctors()
    {
        return $this->hasMany(PromotionDoctors::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionLabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionLabs()
    {
        return $this->hasMany(PromotionLabs::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[PromotionUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionUsers()
    {
        return $this->hasMany(PromotionUsers::className(), ['promotion_id' => 'promotion_id']);
    }

    /**
     * Gets query for [[UserPromotions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserPromotions()
    {
        return $this->hasMany(UserPromotions::className(), ['promotion_id' => 'promotion_id']);
    }

     /**
     * Gets query for [[PromotionPharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionPharmacy()
    {
        return $this->hasMany(PromotionPharmacy::className(), ['promotion_id' => 'promotion_id']);
    }

}
