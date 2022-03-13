<?php
namespace app\assets;

use yii\web\AssetBundle;


class SweetAlertAsset extends AssetBundle
{
    public $baseUrl = '@web/theme/';

    public $css = [
        'plugins/sweetalert/lib/sweet-alert.css'
    ];

    public $js = [
        'plugins/sweetalert/lib/sweet-alert.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}