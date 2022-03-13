<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;

use yii\web\AssetBundle;
/**
 * Description of SliderAsset
 *
 * @author Akram Hossain <akram.hossain@lezasolutions.com>
 */
class SliderAsset extends \yii\web\AssetBundle
{
    //put your code here
    public $sourcePath = '@vendor/kartik-v/yii2-slider/';
    public $css = [
        'assets/css/bootstrap-slider.min.css',
    ];
    public $js = [
        'assets/js/bootstrap-slider.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
