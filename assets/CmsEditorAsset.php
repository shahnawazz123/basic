<?php
namespace app\assets;

use yii\web\AssetBundle;


class CmsEditorAsset extends AssetBundle
{
    public $baseUrl = '@web/theme/';

    public $css = [
        'plugins/summernote/dist/summernote.css',
        'plugins/summernote/dist/summernote-bs3.css'
    ];

    public $js = [
        'plugins/summernote/dist/summernote.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}