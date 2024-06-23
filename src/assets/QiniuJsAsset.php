<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;

class QiniuJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/qiniu-js/dist/';
    public $css = [
    ];
    public $js = [
        'qiniu.min.js',
    ];

    public $depends = [
        BaseAsset::class,
    ];
}
