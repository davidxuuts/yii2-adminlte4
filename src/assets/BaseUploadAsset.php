<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;

/**
 * Class BaseUploadAsset
 * @package davidxu\adminlte4\assets;
 */
class BaseUploadAsset extends AssetBundle
{
    public $sourcePath = '@davidxu/adminlte4/';
    /**
     * @var array
     */
    public $js = [
        'js/common.js',
        'js/jquery.i18n.js',
    ];
    public $css = [];

    public $depends = [
        BaseAsset::class,
        QiniuJsAsset::class,
        QETagAsset::class,
    ];
}
