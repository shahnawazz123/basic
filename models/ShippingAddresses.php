<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipping_addresses".
 *
 * @property int $shipping_address_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property int $user_id
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $area_id
 * @property int|null $block_id
 * @property string $street
 * @property string|null $avenue
 * @property string|null $landmark
 * @property string|null $flat
 * @property string|null $floor
 * @property string|null $building
 * @property string|null $addressline_1
 * @property string|null $mobile_number
 * @property string|null $alt_phone_number
 * @property string|null $location_type
 * @property string|null $notes
 * @property string|null $id_number
 * @property int $is_default
 * @property int $is_deleted
 *
 * @property Orders[] $orders
 * @property Area $area
 * @property Users $user
 */
class ShippingAddresses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipping_addresses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'street'], 'required'],
            [['user_id', 'country_id', 'state_id', 'area_id', 'block_id', 'is_default', 'is_deleted'], 'integer'],
            [['addressline_1', 'notes'], 'string'],
            [['first_name', 'last_name', 'street', 'avenue', 'landmark', 'flat', 'floor', 'building', 'id_number'], 'string', 'max' => 255],
            [['mobile_number', 'alt_phone_number'], 'string', 'max' => 45],
            [['location_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'shipping_address_id' => 'Shipping Address ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'user_id' => 'User ID',
            'country_id' => 'Country ID',
            'state_id' => 'State ID',
            'area_id' => 'Area ID',
            'block_id' => 'Block ID',
            'street' => 'Street',
            'avenue' => 'Avenue',
            'landmark' => 'Landmark',
            'flat' => 'Flat',
            'floor' => 'Floor',
            'building' => 'Building',
            'addressline_1' => 'Addressline 1',
            'mobile_number' => 'Mobile Number',
            'alt_phone_number' => 'Alt Phone Number',
            'location_type' => 'Location Type',
            'notes' => 'Notes',
            'id_number' => 'Id Number',
            'is_default' => 'Is Default',
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
        return $this->hasMany(Orders::className(), ['shipping_address_id' => 'shipping_address_id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['country_id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['state_id' => 'state_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'area_id']);
    }

    public function getBlock()
    {
        return $this->hasOne(Block::className(), ['block_id' => 'block_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
