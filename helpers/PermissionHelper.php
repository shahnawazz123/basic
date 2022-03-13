<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Description of PermissionHelper
 *
 * @author Akram Hossain
 */
class PermissionHelper {

    static function checkUserHasRole($userId, $itemId, $type) {
        $model = \app\models\AuthAssignment::find()
                ->where(['auth_item_id' => $itemId, 'user_id' => $userId, 'user_type' => $type])
                ->one();

        if (empty($model)) {
            return 0;
        } else {
            return 1;
        }
    }

    static function countUserModuleRole($userId, $moduleId, $type) {
        $model = \app\models\AuthAssignment::find()
                ->join('LEFT JOIN', 'auth_item', 'auth_item.auth_item_id = auth_assignment.auth_item_id')
                ->where(['auth_module_id' => $moduleId, 'user_id' => $userId, 'user_type' => $type, 'is_active' => 1])
                ->count();

        return $model;
    }

    static function getUserPermission($userId, $type) {
        $modules = \app\models\AuthModule::find()
                ->orderBy(['auth_module_id' => SORT_ASC])
                ->where(['is_active' => 1])
                ->asArray()
                ->all();

        $result = [];

        foreach ($modules as $row) {
            $roles = self::getModuleAssignItem($row['auth_module_id'], $userId, $type);
            if (!empty($roles)) {
                $row['items'] = $roles;
                array_push($result, $row);
            }
        }

        return $result;
    }

    static function getModuleAssignItem($mid, $userId, $type) {
        $model = \app\models\AuthAssignment::find()
                ->select('auth_assignment.*,auth_item.auth_item_url,auth_item.auth_item_name,auth_item.auth_module_id,auth_item.rule_name,auth_item.is_active,auth_module.auth_module_name,auth_module.auth_module_url')
                ->join('left join', 'auth_item', 'auth_item.auth_item_id = auth_assignment.auth_item_id')
                ->join('left join', 'auth_module', 'auth_item.auth_module_id = auth_module.auth_module_id')
                ->where(['auth_item.auth_module_id' => $mid, 'auth_item.is_active' => 1, 'user_id' => $userId, 'user_type' => $type])
                ->orderBy(['auth_item_name' => SORT_ASC])
                ->asArray()
                ->all();
        if ($type == "SA") {
            $result = [];
            if (!empty($model)) {
                foreach ($model as $row) {
                    $shopAdminModel = \app\models\ShopAdmins::findOne($userId);
                    $hasShopAccess = self::hasShopAccess($shopAdminModel->shop_id, $row['auth_item_id'], $mid, 'S');
                    if ($hasShopAccess) {
                        array_push($result, $row);
                    }
                }
            }
            return $result;
        } else {
            return $model;
        }
    }

    static function hasShopAccess($shop_id, $auth_item_id, $mid, $type) {
        $model = \app\models\AuthAssignment::find()
                ->select('auth_assignment.*,auth_item.auth_item_url,auth_item.auth_item_name,auth_item.auth_module_id,auth_item.rule_name,auth_item.is_active,auth_module.auth_module_name,auth_module.auth_module_url')
                ->join('left join', 'auth_item', 'auth_item.auth_item_id = auth_assignment.auth_item_id')
                ->join('left join', 'auth_module', 'auth_item.auth_module_id = auth_module.auth_module_id')
                ->where(['auth_item.auth_module_id' => $mid, 'auth_item.is_active' => 1, 'user_id' => $shop_id, 'user_type' => $type, 'auth_assignment.auth_item_id' => $auth_item_id])
                ->asArray()
                ->one();
        if (empty($model)) {
            return false;
        }else{
            return true;
        }
    }

    static function getPermissibleArray() {
        $json = \Yii::$app->session['_eyadatUserPermissibleItem'];
        $perm = json_decode($json, true);
        $result = [];
        if (!empty($perm)) {
            foreach ($perm as $row) {
                foreach ($row['items'] as $itm) {
                    $url = $row['auth_module_url'] . $itm['auth_item_url'];
                    array_push($result, $url);
                }
            }
        }
        return $result;
    }

    static function getUserPermissibleAction($moduleId) {
        $json = \Yii::$app->session['_eyadatUserPermissibleItem'];
        $perm = json_decode($json, true);
        $result = [];
        $actions = [];
        if (!empty($perm)) {
            foreach ($perm as $row) {
                if ($row['auth_module_id'] == $moduleId) {
                    $result = $row;
                    break;
                }
            }
            if (!empty($result['items'])) {
                foreach ($result['items'] as $itm) {
                    $url = str_replace('/', '', $itm['auth_item_url']);
                    array_push($actions, $url);
                }
            }
        }
        if (empty($actions)) {
            $actions[] = '404';
        }
        return $actions;
    }

    public static function getAllModuleList() {
        $model = \app\models\AuthModule::find()
                ->where(['is_active' => 1])
                //->andWhere('admin_type !="S"')
                ->orderBy(['auth_module_name' => SORT_ASC])
                ->all();

        $list = ArrayHelper::map($model, 'auth_module_id', 'auth_module_name');
        return $list;
    }

    static function checkUserHasAccess($controller, $method, $user_id = '', $type = '') {
        if (\Yii::$app->session['_eyadatAuth'] == 1 || \Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 4) {
            $name = '/' . $controller;
            $json = \Yii::$app->session['_eyadatUserPermissibleItem'];
            $perm = json_decode($json, true);
            $result = [];
            $action = [];
            if (!empty($perm)) {
                foreach ($perm as $row) {
                    if ($row['auth_module_url'] == $name) {
                        $result = $row;
                        break;
                    }
                }
                if (!empty($result)) {
                    $methodName = '/' . $method;
                    if (!empty($result['items'])) {
                        foreach ($result['items'] as $itm) {
                            if ($itm['auth_item_url'] == $methodName) {
                                $action = $itm;
                                break;
                            }
                        }
                    }
                    if (!empty($action)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
        return true;
    }

}
