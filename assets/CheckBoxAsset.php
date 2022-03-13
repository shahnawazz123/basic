<?php
/**
 * Created by PhpStorm.
 * User: Chirag Panchal
 * Date: 11/9/2017
 * Time: 3:39 PM
 */

namespace app\assets;


use yii\web\AssetBundle;

class CheckBoxAsset extends AssetBundle
{

    public $baseUrl = '@web/theme/';

    public $css = [
        'css/checkbox.css'
    ];

    public $js = [
        'plugins/iCheck/icheck.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}