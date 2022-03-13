<?php
namespace app\assets;

use yii\web\AssetBundle;


class SelectAsset extends AssetBundle
{

    public $sourcePath = 'theme/';

    public $css = [
        'plugins/select2-3.5.2/select2.css',
        'plugins/select2-bootstrap/select2-bootstrap.css'
    ];

    public $js = [
        'plugins/select2-3.5.2/select2.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}