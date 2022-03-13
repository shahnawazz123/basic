<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;
use yii\web\AssetBundle;
/**
 * Description of FullCalendar
 *
 * @author Akram Hossain <akram.lezasolutions@gmail.com>
 */
class FullCalendar extends AssetBundle{
    //put your code here
    public $baseUrl = '@web/theme/';
    
    public $css = [
        'plugins/fullcalendar/v5.8.0/lib/main.css',
    ];
    public $js = [
        'plugins/fullcalendar/v5.8.0/lib/main.min.js',
    ];
    
    public $publishOptions = [
        'forceCopy' => true
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
