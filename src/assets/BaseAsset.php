<?php

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;

class BaseAsset extends AssetBundle
{
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        SweetConfirmAsset::class,
    ];
}