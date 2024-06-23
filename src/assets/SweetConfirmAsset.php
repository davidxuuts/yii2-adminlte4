<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;

/**
 * Class SweetConfirmAsset
 * @package davidxu\adminlte4\assets
 */
class SweetConfirmAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/davidxu/yii2-adminlte4/src/adminlte4';

    /**
     * @var array
     */
    public $css = [];

    /**
     * @var array
     */
    public $js = [
        'js/sweetconfirm.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        SweetAlert2Asset::class,
    ];
}
