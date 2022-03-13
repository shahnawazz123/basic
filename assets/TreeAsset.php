<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\assets;
use yii\web\AssetBundle;
/**
 * Description of TreeAsset
 *
 * @author Akram Hossain <akram_cse@yahoo.com>
 */
class TreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/kartik-v/yii2-tree-manager/';
    public $css = [
        'assets/css/kv-tree.css',
    ];
    public $js = [
        'assets/js/kv-tree.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
