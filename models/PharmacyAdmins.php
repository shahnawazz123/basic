<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pharmacy_admins".
 *
 * @property int $pharmacy_admin_id
 * @property int $pharmacy_id
 * @property string $name_en
 * @property string $name_ar
 * @property string $email
 * @property string $password
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Pharmacies $pharmacy
 */
class PharmacyAdmins extends \yii\db\ActiveRecord
{

    public $password_hash;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pharmacy_admins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pharmacy_id', 'name_en', 'name_ar', 'email'], 'required'],
            [['pharmacy_id', 'is_active', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name_en', 'name_ar', 'email'], 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'unique'],
             ['password_hash', 'required', 'on'=>'create'],
            [['password_hash'],  'match', 'pattern' => '/^.*(?=^.{6,15}$).*$/', 'message' => 'Password must contain min 6 char.'],
            [['pharmacy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pharmacies::className(), 'targetAttribute' => ['pharmacy_id' => 'pharmacy_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pharmacy_admin_id' => 'Pharmacy Admin',
            'pharmacy_id' => 'Pharmacy',
            'name_en' => 'Name In English',
            'name_ar' => 'Name In Arabic',
            'email' => 'Email',
            'password' => 'Password',
            'password_hash' => 'Password',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Pharmacy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPharmacy()
    {
        return $this->hasOne(Pharmacies::className(), ['pharmacy_id' => 'pharmacy_id']);
    }
}
