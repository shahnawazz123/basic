<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacies".
 *
 * @property int $pharmacy_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image_en
 * @property string|null $image_ar
 * @property int $minimum_order
 * @property int $is_free_delivery
 * @property int $is_featured
 * @property string|null $latlon
 * @property int $enable_login
 * @property string $email
 * @property string $password
 * @property int $governorate_id
 * @property int $area_id
 * @property string|null $block
 * @property string|null $street
 * @property string|null $building
 * @property string|null $floor
 * @property string $shop_number
 * @property float $admin_commission
 * @property float $delivery_charge
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PharmacyAdmins[] $pharmacyAdmins
 * @property PharmacyLocations[] $pharmacyLocations
 * @property PharmacyOrders[] $pharmacyOrders
 * @property Product[] $products
 * @property StorePharmacies[] $storePharmacies
 * @property State $governorate
 * @property Area $area
 */
class Pharmacies extends \yii\db\ActiveRecord
{
    public $confirm_password, $country_id, $distance;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'country_id', 'governorate_id', 'area_id', 'shop_number', 'password', 'admin_commission', 'email', 'minimum_order'], 'required'],
            [['minimum_order', 'is_free_delivery', 'is_featured', 'enable_login', 'governorate_id', 'area_id', 'is_active', 'is_deleted'], 'integer'],
            [['admin_commission', 'delivery_charge'], 'number'],
            ['email', 'email'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            [['minimum_order','admin_commission'], 'match', 'pattern' => '/^[0-9]+$/', "message" => "Must not be negative"],
            [['created_at', 'updated_at', 'distance'], 'safe'],
            [['name_en', 'name_ar', 'image_en', 'image_ar', 'latlon', 'email', 'block', 'street', 'building', 'floor', 'shop_number'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 200],
            ['accepted_payment_method', 'required'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_id' => 'Pharmacy ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image_en' => 'Image in English',
            'image_ar' => 'Image in Arabic',
            'minimum_order' => 'Minimum Order',
            'is_free_delivery' => 'Is Free Delivery',
            'is_featured' => 'Is Featured',
            'latlon' => 'Latlon',
            'enable_login' => 'Enable Login',
            'email' => 'Email',
            'password' => 'Password',
            'governorate_id' => 'Governorate ',
            'country_id' => 'Country ',
            'area_id' => 'Area ',
            'block' => 'Block',
            'street' => 'Street',
            'building' => 'Building',
            'floor' => 'Floor',
            'shop_number' => 'Shop Number',
            'admin_commission' => 'Admin Commission %',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'accepted_payment_method' => 'Accepted Payment Method',

        ];
    }

    /**
     * Gets query for [[PharmacyAdmins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyAdmins()
    {
        return $this->hasMany(PharmacyAdmins::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[PharmacyLocations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyLocations()
    {
        return $this->hasMany(PharmacyLocations::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[PharmacyOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacyOrders()
    {
        return $this->hasMany(PharmacyOrders::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    /**
     * Gets query for [[StorePharmacies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStorePharmacies()
    {
        return $this->hasMany(StorePharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }

    public function getGovernorate()
    {
        return $this->hasOne(State::className(), ['state_id' => 'governorate_id']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'area_id']);
    }
}
