<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "labs".
 *
 * @property int $lab_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $email
 * @property string $password
 * @property float|null $home_test_charge
 * @property float|null $admin_commission
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $accepted_payment_method
 * @property int $consultation_time_interval
 * @property int $max_booking_per_lot
 * @property string $start_time
 * @property string $end_time
 * @property int $governorate_id
 * @property int $area_id
 * @property string|null $block
 * @property string|null $street
 * @property string|null $building
 *
 * @property LabAdmins[] $labAdmins
 * @property LabAppointments[] $labAppointments
 * @property LabInsurances[] $labInsurances
 * @property LabServices[] $labServices
 * @property LabTests[] $labTests
 * @property PromotionLabs[] $promotionLabs
 * @property StoreLabs[] $storeLabs
 * @property State $governorate
 * @property Area $area
 * @property LabsWorkingDays[] $labsWorkingDays
 */
class Labs extends \yii\db\ActiveRecord
{
    public $insurance_id;
    public $service_id;
    public $test_id;
    public $password_hash;
    public $country_id;
    public $days;
    public $lab_start_time;
    public $lab_end_time;
    public $distance;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'labs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_en', 'name_ar', 'email', 'consultation_time_interval', 'max_booking_per_lot', 'test_id', 'country_id','accepted_payment_method', 'governorate_id', 'area_id'], 'required'],
            [['home_test_charge', 'admin_commission'], 'number'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            [['email'], 'unique', 'message' => 'Email already exist. Please try another one.'],
            [['consultation_time_interval', 'home_test_charge','admin_commission', 'max_booking_per_lot'], 'match', 'pattern' => '/^[0-9]+$/',"message"=>"Must not be negative"],
            ['password_hash', 'required', 'on' => 'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['is_active', 'is_deleted', 'consultation_time_interval', 'max_booking_per_lot'], 'integer'],
            [['governorate_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['governorate_id' => 'state_id']],
            [['created_at', 'updated_at', 'start_time', 'end_time', 'accepted_payment_method', 'distance'], 'safe'],
            [['name_en', 'name_ar', 'email', 'image_en', 'image_ar', 'latlon', 'block', 'street', 'building'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lab_id' => 'Lab ID',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'image_en' => 'Logo in English',
            'image_ar' => 'Logo in Arabic',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'home_test_charge' => 'Home Test Charge',
            'admin_commission' => 'Admin Commission %',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'consultation_time_interval' => 'Consultation Time Interval (in minutes)',
            'max_booking_per_lot' => 'Max Booking Per Lot',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'insurance_id' => 'Insurance',
            'service_id' => 'Service',
            'test_id' => 'Tests',
            'governorate_id' => 'Governorate',
            'country_id' => 'Country',
            'area_id' => 'Area',
            'block' => 'Block',
            'street' => 'Street',
            'building' => 'Building Name/No.',
            'days' => '',
            'accepted_payment_method' => 'Accepted Payment Method'
        ];
    }

    /**
     * Gets query for [[LabAdmins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAdmins()
    {
        return $this->hasMany(LabAdmins::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[LabAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointments()
    {
        return $this->hasMany(LabAppointments::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[LabInsurances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabInsurances()
    {
        return $this->hasMany(LabInsurances::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[LabServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabServices()
    {
        return $this->hasMany(LabServices::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[LabTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabTests()
    {
        return $this->hasMany(LabTests::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[PromotionLabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionLabs()
    {
        return $this->hasMany(PromotionLabs::className(), ['lab_id' => 'lab_id']);
    }

    /**
     * Gets query for [[StoreLabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreLabs()
    {
        return $this->hasMany(StoreLabs::className(), ['lab_id' => 'lab_id']);
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
     * Gets query for [[LabsWorkingDays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabsWorkingDays()
    {
        return $this->hasMany(LabsWorkingDays::className(), ['lab_id' => 'lab_id']);
    }
}
