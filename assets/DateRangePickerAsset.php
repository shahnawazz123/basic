<?php
namespace app\assets;

use yii\web\AssetBundle;

class DateRangePickerAsset extends \yii\web\AssetBundle
{
    public $baseUrl = '@web/theme/';
    public $css = [
        'plugins/daterangepicker/daterangepicker.css',
    ];
    public $js = [
        'plugins/daterangepicker/daterangepicker.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}