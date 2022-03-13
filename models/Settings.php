<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $setting_id
 * @property string|null $contact_email
 * @property string|null $support_email
 * @property string|null $support_phone
 * @property string|null $smtp_host
 * @property string|null $smtp_username
 * @property string|null $smtp_password
 * @property string|null $smtp_port
 * @property int $enable_order_multiple_vendors
 * @property float|null $delivery_charge
 * @property string|null $delivery_interval
 * @property int|null $notify_for_quantity_below
 * @property string|null $express_delivery_interval
 * @property string|null $notify_msg_en
 * @property string|null $notify_msg_ar
 * @property int|null $return_request_max_days
 * @property int|null $buffer_quantity
 * @property int $push_cod_orders
 * @property string|null $banner_en
 * @property string|null $banner_ar
 * @property string|null $physical_consultation_image
 * @property string|null $online_consultation_image
 * @property string|null $lab_test_image
 * @property string|null $pharmacies_image
 * @property string|null $hospital_image
 * @property string|null $beauty_clinic_image
 * @property int|null $banner_height
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enable_order_multiple_vendors', 'notify_for_quantity_below', 'return_request_max_days', 'buffer_quantity', 'push_cod_orders', 'banner_height'], 'integer'],
            [['delivery_charge'], 'number'],
            [['translator_price'], 'safe'],
            [['notify_msg_en', 'notify_msg_ar'], 'string'],
            [['notify_msg_en', 'notify_msg_ar', 'online_consultation_image', 'physical_consultation_image', 'lab_test_image', 'pharmacies_image', 'hospital_image', 'beauty_clinic_image'], 'string'],
            [['contact_email', 'support_email', 'support_phone', 'smtp_host', 'smtp_username', 'express_delivery_interval'], 'string', 'max' => 50],
            [['smtp_password', 'smtp_port', 'banner_en', 'banner_ar'], 'string', 'max' => 100],
            [['delivery_interval'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'setting_id' => 'Setting ID',
            'contact_email' => 'Contact Email',
            'support_email' => 'Support Email',
            'support_phone' => 'Support Phone',
            'smtp_host' => 'Smtp Host',
            'smtp_username' => 'Smtp Username',
            'smtp_password' => 'Smtp Password',
            'smtp_port' => 'Smtp Port',
            'enable_order_multiple_vendors' => 'Enable Order Multiple Vendors',
            'delivery_charge' => 'Delivery Charge',
            'delivery_interval' => 'Delivery Interval',
            'notify_for_quantity_below' => 'Notify For Quantity Below',
            'express_delivery_interval' => 'Express Delivery Interval',
            'notify_msg_en' => 'Notify Msg En',
            'notify_msg_ar' => 'Notify Msg Ar',
            'return_request_max_days' => 'Return Request Max Days',
            'buffer_quantity' => 'Buffer Quantity',
            'push_cod_orders' => 'Push Cod Orders',
            'banner_en' => 'Banner En',
            'banner_ar' => 'Banner Ar',
            '   
        ];
    }
}
