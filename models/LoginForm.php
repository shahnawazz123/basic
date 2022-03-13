<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{

    public $username;
    public $password;
    public $rememberMe = true;
    public $type;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login($type = 1) {
        $this->type = $type;
        if ($this->validate()) {
            $user = $this->getUser();

            if (!empty($user->clinic_id) && $this->type==2) 
            {
                //echo "<pre>";print_r($user);die;
                \Yii::$app->session->set('_eyadatAuth', 2);
            }else if (!empty($user->clinic_id) && $this->type==3) 
            {
                \Yii::$app->session->set('_eyadatAuth', 3);
            }else if (!empty($user->lab_id) && $this->type==4) 
            {
                //print_r($this->_user);die;
                \Yii::$app->session->set('_eyadatAuth', 4);
            }else if (!empty($user->pharmacy_id) && $this->type==5) 
            {
                //print_r($this->_user);die;
                \Yii::$app->session->set('_eyadatAuth', 5);
            }
            else if (!empty($user->translator_id) && $this->type==8)
            {

                \Yii::$app->session->set('_eyadatAuth', 8);
            }
            else {
                \Yii::$app->session->set('_eyadatAuth', $type);
            }
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            //echo $this->type;die;
            if ($this->type == 1) {
                $user = User::findByUsername($this->username);
                if (!empty($user)) {
                    $this->_user = $user;
                    \Yii::$app->session->set('_eyadatUserRole', 1);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->admin_id, 'A');
                    //debugPrint($getPermissions);exit;
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            } elseif ($this->type == 2) {
                $user = AuthClinics::findByUsername($this->username);
                if (!empty($user)) {
                    $this->_user = $user;
                    //print_r($this->_user);die;
                    \Yii::$app->session->set('_eyadatUserRole', 2);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->clinic_id, 'S');
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            } elseif ($this->type == 3) {
                $user = AuthDoctors::findByUsername($this->username);
                if (!empty($user)) {
                    $this->_user = $user;
                    \Yii::$app->session->set('_eyadatUserRole', 3);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->doctor_id, 'S');
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            }elseif ($this->type == 4) {
                $user = AuthLabs::findByUsername($this->username);
                //echo "<pre>";print_r($user);
                if (!empty($user)) {
                    $this->_user = $user;
                   // echo "<pre>";print_r($this->_user);die;
                    \Yii::$app->session->set('_eyadatUserRole', 4);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->lab_id, 'S');
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            }elseif ($this->type == 5) {
                $user = AuthPharmacies::findByUsername($this->username);
                if (!empty($user)) {
                    $this->_user = $user;
                    \Yii::$app->session->set('_eyadatUserRole', 5);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->pharmacy_id, 'S');
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            }elseif ($this->type == 8) {
                $user = AuthTranslator::findByUsername($this->username);
                if (!empty($user)) {
                    $this->_user = $user;
                    \Yii::$app->session->set('_eyadatUserRole', 8);
                    $getPermissions = \app\helpers\PermissionHelper::getUserPermission($user->translator_id, 'S');
                    \Yii::$app->session->set('_eyadatUserPermissibleItem', json_encode($getPermissions));
                }
            }
        }

        return $this->_user;
    }

    

}
