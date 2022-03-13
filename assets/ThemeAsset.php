<?php

namespace app\assets;

use yii\web\AssetBundle;

class ThemeAsset extends AssetBundle
{
    //public $sourcePath = 'theme/';
    public $baseUrl = '@web/theme/';
    
    public $css = [
        'plugins/fontawesome/css/font-awesome.css',
        'plugins/metisMenu/dist/metisMenu.css',
        'plugins/animate.css/animate.css',
        'fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css',
        'fonts/pe-icon-7-stroke/css/helper.css',
        'plugins/sweetalert/lib/sweet-alert.css',
        'plugins/blueimp-gallery/css/blueimp-gallery.min.css',
        'plugins/toastr/build/toastr.min.css',
        'css/style.css',
    ];
    public $js = [
        //'plugins/jquery-ui/jquery-ui.min.js',
        'plugins/slimScroll/jquery.slimscroll.min.js',
        'plugins/jquery-flot/jquery.flot.js',
        'plugins/jquery-flot/jquery.flot.resize.js',
        'plugins/jquery-flot/jquery.flot.pie.js',
        'plugins/flot.curvedlines/curvedLines.js',
        'plugins/jquery.flot.spline/index.js',
        'plugins/metisMenu/dist/metisMenu.min.js',
        'plugins/iCheck/icheck.min.js',
        'plugins/peity/jquery.peity.min.js',
        'plugins/chartjs/Chart.min.js',
        'plugins/sparkline/index.js',
        'plugins/sweetalert/lib/sweet-alert.min.js',
        'plugins/blueimp-gallery/js/jquery.blueimp-gallery.min.js',
        'plugins/toastr/build/toastr.min.js',
        'js/homer.js',
        'js/charts.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
