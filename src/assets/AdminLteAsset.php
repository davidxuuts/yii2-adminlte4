<?php
namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;

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
        'davidxu\adminlte4\assets\BaseAsset',
        'davidxu\adminlte4\assets\PluginAsset'
    ];
}