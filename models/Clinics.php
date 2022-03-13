<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clinics".
 *
 * @property int $clinic_id
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $image_en
 * @property string|null $image_ar
 * @property string|null $latlon
 * @property string $type
 * @property int $governorate_id
 * @property int $area_id
 * @property string|null $block
 * @property string|null $street
 * @property string|null $building
 * @property string $email
 * @property string $password
 * @property int $is_featured
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $description_en
 * @property string $description_ar
 * @property float $admin_commission
 *
 * @property ClinicCategories[] $clinicCategories
 * @property ClinicInsurances[] $clinicInsurances
 * @property State $governorate
 * @property Area $area
 * @property Doctors[] $doctors
 * @property PromotionClinics[] $promotionClinics
 * @property ClinicWorkingDays[] $clinicWorkingDays
 */
class Clinics extends \yii\db\ActiveRecord
{
    public $distance, $password_hash;
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $authKey;
    public $insurance_id, $category_id;
    public $days, $start_time, $end_time;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clinics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'country_id', 'governorate_id', 'area_id', 'email','admin_commission'], 'required'],
            [['insurance_id', 'category_id', "block", "street", "building","latlon"], 'required', "on" => "create"],
            [['type'], 'string'],
            [['governorate_id', 'area_id', 'is_active', 'is_deleted', 'is_featured'], 'integer'],
            [['created_at', 'updated_at', 'description_en', 'description_ar', 'admin_commission'], 'safe'],
            [['name_en', 'name_ar', 'latlon', 'block', 'street', 'building'], 'string', 'max' => 100],
            [['admin_commission'], 'match', 'pattern' => '/^[0-9]+$/', 'message' => 'Admin commission % must be an integer,positive, non decimal.'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email','message' => 'Invalid email address.'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            //[['password'],  'match', 'pattern' => '/^.*(?=^.{8,10}$)(?=.*\d)(?=.*[-+_!@#$%^&*., ?])(?=.*[a-z])(?=.*[A-Z]).*$/', 'message' => 'Password must contain Uppercase, Lowercase, Special Char, numberic , min 8 char.'],
            ['password_hash', 'required', 'on' => 'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['governorate_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['governorate_id' => 'state_id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'area_id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clinic_id' => 'Clinic ID',
            'admin_commission' => 'Admin Commission %',
            'name_en' => 'Name In English',
            'name_ar' => 'Name In Arabic',
            'image_en' => 'Logo in English',
            'image_ar' => 'Logo in Arabic',
            'latlon' => 'Latlon',
            'type' => 'Type',
            'governorate_id' => 'Governorate',
            'country_id' => 'Country',
            'area_id' => 'Area',
            'block' => 'Block',
            'street' => 'Street',
            'building' => 'Building Name/No.',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'is_active' => 'Is Active',
            'is_featured' => 'Is Featured',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'insurance_id' => 'Insurance Accepted',
            'category_id' => 'Categories',
            'description_en' => 'Description in English',
            'description_ar' => 'Description in Arabic',
            'days' => ''
        ];
    }



    /**
     * Undocumented function
     * @param [password] $attribute
     * @return void
     */





    /**
     * Gets query for [[ClinicCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinicCategories()
    {
        return $this->hasMany(ClinicCategories::className(), ['clinic_id' => 'clinic_id']);
    }

    /**
     * Gets query for [[ClinicInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinicInsurances()
    {
        return $this->hasMany(ClinicInsurances::className(), ['clinic_id' => 'clinic_id']);
    }

    /**
     * Gets query for [[Governorate]].
     *
     * @return \yii\db\ActiveQuery
     */
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

    /**
     * Gets query for [[Doctors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors()
    {
        return $this->hasMany(Doctors::className(), ['clinic_id' => 'clinic_id']);
    }

    /**
     * Gets query for [[PromotionClinics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionClinics()
    {
        return $this->hasMany(PromotionClinics::className(), ['clinic_id' => 'clinic_id']);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username, 'is_deleted' => self::STATUS_DELETED]);
    }

    /**
     * Gets query for [[ClinicWorkingDays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClinicWorkingDays()
    {
        return $this->hasMany(ClinicWorkingDays::className(), ['clinic_id' => 'clinic_id']);
    }
}
