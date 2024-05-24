<?php
namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;
use davidxu\adminlte4\assets\BaseAsset;
use davidxu\adminlte4\assets\PluginAsset;

class AdminLteAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';

    public $css = [
        'css/adminlte.min.css'
    ];

    public $js = [
        'js/adminlte.min.js'
    ];

    public $depends = [
        BaseAsset::class,
        PluginAsset::class,
    ];
}