<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'vendor/bootstrap-daterangepicker/daterangepicker.css',
        'vendor/bootstrap-select/css/bootstrap-select.min.css',
        'vendor/fontawesome/css/font-awesome.min.css',
        'css/site.css',
    ];
    public $js = [
        'vendor/moment/min/moment.min.js',
        'vendor/bootstrap-select/js/bootstrap-select.min.js',
        'vendor/bootstrap-daterangepicker/daterangepicker.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\web\JqueryAsset',
        //'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
    public $jsOptions = [ 'position' => \yii\web\View::POS_HEAD ];
}
