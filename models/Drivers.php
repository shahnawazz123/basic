<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "drivers".
 *
 * @property int $driver_id
 * @property string|null $email
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $location
 * @property string|null $device_token
 * @property string|null $device_type
 * @property string|null $device_model
 * @property string|null $app_version
 * @property string|null $os_version
 * @property int $push_enabled
 * @property string|null $image
 * @property int $is_active
 * @property int $is_deleted
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $civil_id_number
 * @property string|null $license_number
 *
 * @property DriverOrders[] $driverOrders
 */
class Drivers extends \yii\db\ActiveRecord
{

    public $password_hash;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'drivers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['push_enabled', 'is_active', 'is_deleted'], 'integer'],
            [['name_en', 'name_ar','email','phone'], 'required'],
            [['email', 'location'], 'string', 'max' => 100],
            [['phone', 'image', 'civil_id_number', 'license_number'], 'string', 'max' => 50],
            [['device_token', 'device_type', 'device_model', 'app_version'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique'],
            [['phone'], 'match', 'pattern' => '/^[0-9]+$/'],
            ['password_hash', 'required', 'on'=>'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['os_version'], 'string', 'max' => 45],
            [['name_en', 'name_ar'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'driver_id' => 'Driver ID',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'phone' => 'Phone',
            'location' => 'Location',
            'device_token' => 'Device Token',
            'device_type' => 'Device Type',
            'device_model' => 'Device Model',
            'app_version' => 'App Version',
            'os_version' => 'Os Version',
            'push_enabled' => 'Push Enabled',
            'image' => 'Image',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'name_en' => 'Name in English',
            'name_ar' => 'Name in Arabic',
            'civil_id_number' => 'Civil Id Number',
            'license_number' => 'License Number',
        ];
    }

    /**
     * Gets query for [[DriverOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriverOrders()
    {
        return $this->hasMany(DriverOrders::className(), ['driver_id' => 'driver_id']);
    }
}
