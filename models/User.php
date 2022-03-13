<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $authKey;
    public $clinic_id,$name_en,$name_ar,$image_en,$image_ar,$latlon,$type,$governorate_id,$area_id,$block,$street,$building,$is_featured,$created_at,$updated_at,$country_id,$location; // clinics
    public $doctor_id,$years_experience,$qualification,$gender,$consultation_time_online,$consultation_time_offline,$consultation_price_regular,$consultation_price_final,$registration_number,$description_en,$description_ar,$sort_order,$accepted_payment_method;
    public $lab_id,$home_test_charge,$admin_commission,$consultation_time_interval,$max_booking_per_lot,$end_time,$start_time;
    public $pharmacy_id,$minimum_order,$is_free_delivery,$enable_login,$floor,$shop_number,$delivery_charge;
    public $translator_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        if (\Yii::$app->session['_eyadatAuth'] == 1) {
            return static::findOne(['admin_id' => $id, 'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED]);
        }else if (\Yii::$app->session['_eyadatAuth'] == 2) {
            $dbUser = Clinics::find()
                    ->where(['clinic_id' => $id,'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED])
                    ->one();
            return new static($dbUser);
        }else if (\Yii::$app->session['_eyadatAuth'] == 3) {
            $dbUser = Doctors::find()
                    ->where(['doctor_id' => $id,'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED])
                    ->one();
            return new static($dbUser);
        }else if (\Yii::$app->session['_eyadatAuth'] == 4) {
            $dbUser = Labs::find()
                    ->where(['lab_id' => $id,'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED])
                    ->one();
            return new static($dbUser);
        }else if (\Yii::$app->session['_eyadatAuth'] ==5) {
            $dbUser = Pharmacies::find()
                    ->where(['pharmacy_id' => $id,'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED])
                    ->one();
            return new static($dbUser);
        }
        else if (\Yii::$app->session['_eyadatAuth'] == 8) {
            $dbUser = Translator::find()
                ->where(['translator_id' => $id,'is_active' => self::STATUS_ACTIVE,'is_deleted' => self::STATUS_DELETED])
                ->one();
            return new static($dbUser);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username,'is_deleted' => self::STATUS_DELETED]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
