<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JqueryMigrateAsset extends AssetBundle
{
    public $sourcePath = '@npm/jquery-migrate/dist';
    public $js = [
        'jquery-migrate' . (YII_ENV_PROD ? '.min' : '') . '.js',
    ];
    public $css = [
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
