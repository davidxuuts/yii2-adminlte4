<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\enums;

use yii\helpers\ArrayHelper;

/**
 * Class BaseEnum
 * @package davidxu\adminlte4\enums
 */
abstract class BaseEnum
{
    abstract public static function getMap(): array;

    /**
     * @param $key
     * @return string
     */
    public static function getValue($key): string
    {
        return static::getMap()[$key] ?? '';
    }

    /**
     * @param array $keys
     * @return array
     */
    public static function getValues(array $keys) : array
    {
        return ArrayHelper::filter(static::getMap(), $keys);
    }

    /**
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getMap());
    }
}
