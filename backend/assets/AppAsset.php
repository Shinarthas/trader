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
        'css/site.css',
        'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.css'
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
