<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class SweetAlert2DarkThemeAsset
 * @package davidxu\adminlte4\assets;
 */
class SweetAlert2DarkThemeAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/sweetalert2--theme-dark';

    /**
     * @var array
     */
    public $js = [];
    public $css = [
        'dark.scss',
    ];

    /**
     * @var array
     */
    public $depends = [
        SweetAlert2Asset::class,
    ];
}
