<?php

namespace davidxu\adminlte4\enums;

use Yii;

class BooleanEnum extends BaseEnum
{
    const BOOLEAN_TRUE = 1;
    const BOOLEAN_FALSE = 0;

    public static function getMap(): array
    {
        return [
            static::BOOLEAN_TRUE => Yii::t('yii', 'Yes'),
            static::BOOLEAN_FALSE => Yii::t('yii', 'No'),
        ];
    }
}
