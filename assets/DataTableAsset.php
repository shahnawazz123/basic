<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;

/**
 * Description of DataTableAsset
 *
 * @author Akram Hossain <akram_cse@yahoo.com>
 */
use yii\web\AssetBundle;

class DataTableAsset extends AssetBundle
{

    public $baseUrl = '@web/theme/plugins/datatables/';
    public $css = [
        'media/css/dataTables.bootstrap.css',
    ];
    public $js = [
        'media/js/jquery.dataTables.js',
        'media/js/dataTables.bootstrap.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
