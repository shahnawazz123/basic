<?php
/**
 * Created by PhpStorm.
 * User: Chirag Panchal
 * Date: 11/9/2017
 * Time: 12:30 PM
 */

namespace app\assets;


use yii\web\AssetBundle;

class LoadingButtonAsset extends AssetBundle
{
    public $baseUrl = '@web/theme/';

    public $css = [
        'plugins/ladda/dist/ladda-themeless.min.css'
    ];

    public $js = [
        'plugins/ladda/dist/spin.min.js',
        'plugins/ladda/dist/ladda.min.js',
        'plugins/ladda/dist/ladda.jquery.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}