<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $gender
 * @property string|null $dob
 * @property string $email
 * @property string $password
 * @property string|null $image
 * @property string|null $phone_code
 * @property string|null $phone
 * @property string|null $code
 * @property int $is_phone_verified
 * @property int $is_email_verified
 * @property int $is_social_register
 * @property string|null $social_register_type
 * @property string|null $device_token
 * @property string|null $device_type
 * @property string|null $device_model
 * @property string|null $app_version
 * @property string|null $os_version
 * @property int|null $push_enabled
 * @property int|null $newsletter_subscribed
 * @property int $is_deleted
 * @property string|null $create_date
 *
 * @property DoctorAppointments[] $doctorAppointments
 * @property Kids[] $kids
 * @property LabAppointments[] $labAppointments
 * @property Orders[] $orders
 * @property PromotionUsers[] $promotionUsers
 * @property Insurance[] $insurance
 */
class Users extends \yii\db\ActiveRecord
{
    public $user_name,$reports,$is_approved,$req_doctor_appointment_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'password'], 'required'],
            [['gender', 'social_register_type'], 'string'],
            [['dob', 'create_date'], 'safe'],
            [['is_phone_verified', 'is_email_verified', 'is_social_register', 'push_enabled', 'newsletter_subscribed', 'is_deleted'], 'integer'],
            [['first_name', 'last_name', 'image'], 'string', 'max' => 100],
            [['email', 'phone', 'code'], 'string', 'max' => 50],
            [['password', 'device_token'], 'string', 'max' => 255],
            [['phone_code'], 'string', 'max' => 10],
            [['device_type', 'device_model', 'app_version', 'os_version'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'dob' => 'Dob',
            'email' => 'Email',
            'password' => 'Password',
            'image' => 'Image',
            'phone_code' => 'Phone Code',
            'phone' => 'Phone',
            'code' => 'Code',
            'is_phone_verified' => 'Is Phone Verified',
            'is_email_verified' => 'Is Email Verified',
            'is_social_register' => 'Is Social Register',
            'social_register_type' => 'Social Register Type',
            'device_token' => 'Device Token',
            'device_type' => 'Device Type',
            'device_model' => 'Device Model',
            'app_version' => 'App Version',
            'os_version' => 'Os Version',
            'push_enabled' => 'Push Enabled',
            'newsletter_subscribed' => 'Newsletter Subscribed',
            'is_deleted' => 'Is Deleted',
            'create_date' => 'Create Date',
        ];
    }

    /**
     * Gets query for [[DoctorAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorAppointments()
    {
        return $this->hasMany(DoctorAppointments::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Kids]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKids()
    {
        return $this->hasMany(Kids::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[LabAppointments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabAppointments()
    {
        return $this->hasMany(LabAppointments::className(), ['user_id' => 'user_id']);
    }

    public function getReports()
    {
        return $this->hasMany(UserReport::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[PromotionUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionUsers()
    {
        return $this->hasMany(PromotionUsers::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[UserPromotions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserPromotions()
    {
        return $this->hasMany(UserPromotions::className(), ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[WishLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWishLists()
    {
        return $this->hasMany(WishList::className(), ['user_id' => 'user_id']);
    }

    public function getShippingAddresses() {
        return $this->hasMany(ShippingAddresses::className(), ['user_id' => 'user_id']);
    }

    public function getRequestReports()
    {
        return $this->hasMany(DoctorReportRequest::className(), ['user_id' => 'user_id']);
    }

    public function getInsurance()
    {
        return $this->hasOne(Insurances::className(), ['insurance_id' => 'insurance_id']);
    }
}
