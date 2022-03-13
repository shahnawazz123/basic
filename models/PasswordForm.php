<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 28-03-2017
 * Time: 09:22
 */

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordForm extends Model {

    public $oldPass;
    public $newPass;
    public $repeatNewPass;
    public $phone;
    public $email;
    public $name;
    public $image;
    public $name_en;
    public $name_ar;

    //public $country_id;

    public function rules() {
        return [
            ['oldPass', 'findPasswords'],
            [['newPass'], 'string', 'min' => 6],
            [['name', 'phone'], 'required', 'on' => 'admin-edit-profile'],
            [['name_en', 'name_ar'], 'required', 'on' => 'shop-edit-profile'],
            [['name_en', 'name_ar'], 'required', 'on' => 'shop-admin-edit-profile'],
            //[['country_id'],'required','on'=>'boutique-edit-profile'],
            //['newPass', 'match', 'pattern' => '$\S*(?=\S{6,})(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', 'message' => 'Password should contain at least one upper case letter, one number and one special character'],
            ['repeatNewPass', 'compare', 'compareAttribute' => 'newPass', 'message' => 'Confirm password must match new password.'],
        ];
    }

    public function attributeLabels() {
        return [
                'name_en' => 'Name in English',
                'name_ar' => 'Name in Arabic'
        ];
    }

    public function findPasswords($attribute, $params) {
        $user = \Yii::$app->user->identity;
        $password = $user->password;

        if (!$user->validatePassword($this->oldPass))
            $this->addError($attribute, 'Incorrect password');
    }

}
