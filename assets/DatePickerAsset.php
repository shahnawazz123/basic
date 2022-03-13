<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;
use yii\web\AssetBundle;
/**
 * Description of DatePickerAsset
 *
 * @author Akram Hossain <akram_cse@yahoo.com>
 */
class DatePickerAsset extends AssetBundle
{
    public $baseUrl = '@web/theme/';
    public $css = [
        'plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.css',
    ];
    public $js = [
        'plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
