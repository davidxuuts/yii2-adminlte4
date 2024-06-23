<?php

namespace davidxu\adminlte4\enums;

use Yii;

class StatusEnum extends BaseEnum
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const STATUS_DELETED = -1;

    public static function getMap(): array
    {
        return [
            static::STATUS_ENABLED => Yii::t('adminlte4', 'Enabled'),
            static::STATUS_DISABLED => Yii::t('adminlte4', 'Disabled'),
            static::STATUS_DELETED => Yii::t('adminlte4', 'Deleted'),
        ];
    }
}
