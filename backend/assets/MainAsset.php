<?php

namespace backend\assets;

use yii\web\AssetBundle;



class MainAsset extends AssetBundle {

    // The directory that contains the source asset files for this asset bundle
    // public $sourcePath = '@app/module/admin/web';
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    // List of CSS files that this bundle contains
    public $css = ['css/main.css'];
    public $js = [
        'js/app.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
