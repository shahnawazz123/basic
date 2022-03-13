<?php

namespace app\components;


class UserIdentity
{

    const ROLE_ADMIN = 1;
    const ROLE_CLINIC = 2;
    const ROLE_DOCTOR = 3;
    const ROLE_LAB = 4;
    const ROLE_PHARMACY = 5;
    const ROLE_PHARMACY_ADMIN = 6;
    const ROLE_CLINIC_ADMIN = 7;
    const ROLE_TRANSLATOR = 8;

    public $username;
    public function init()
    {
    }

    static public function isUserAuthenticate($userrole)
    {

        if ($userrole == self::ROLE_ADMIN) {
            return true;
        } elseif ($userrole == self::ROLE_CLINIC) {
            return true;
        } elseif ($userrole == self::ROLE_CLINIC_ADMIN) {
            return true;
        } elseif ($userrole == self::ROLE_PHARMACY) {
            return true;
        } elseif ($userrole == self::ROLE_PHARMACY_ADMIN) {
            return true;
        } elseif ($userrole == self::ROLE_LAB) {
            return true;
        } elseif ($userrole == self::ROLE_TRANSLATOR) {
            return true;
        } else {

            return false;
        }
    }
}
