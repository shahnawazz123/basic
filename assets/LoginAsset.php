<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26-03-2017
 * Time: 09:00
 */

namespace app\assets;

use yii\web\AssetBundle;


class LoginAsset extends AssetBundle
{
    public $baseUrl = '@web/theme/';
    
    public $css = [
        'plugins/fontawesome/css/font-awesome.css',
        'plugins/metisMenu/dist/metisMenu.css',
        'plugins/animate.css/animate.css',
        //'vendor/bootstrap/dist/css/bootstrap.css',
        'fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css',
        'fonts/pe-icon-7-stroke/css/helper.css',
        'css/style.css',
    ];
    public $js = [
        'plugins/jquery-ui/jquery-ui.min.js',
        'plugins/slimScroll/jquery.slimscroll.min.js',
        //'vendor/bootstrap/dist/js/bootstrap.min.js',
        'plugins/metisMenu/dist/metisMenu.min.js',
        'plugins/iCheck/icheck.min.js',
        'plugins/sparkline/index.js',
        'js/homer.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}