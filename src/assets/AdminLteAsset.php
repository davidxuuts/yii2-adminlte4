<?php
namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;

class AdminLteAsset extends AssetBundle
{
    public $sourcePath = '@vendor/davidxu/yii2-adminlte4/src/adminlte4';

    public $css = [
        'css/adminlte.min.css',
        'css/select2-bs5.css',
    ];

    public $js = [
        'js/adminlte.min.js',
        'js/custom-adminlte4.js',
        'js/color-mode-toggle.js',
    ];

    public $depends = [
        BaseAsset::class,
    ];
}