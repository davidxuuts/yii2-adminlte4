<?php

namespace davidxu\adminlte4\enums;

use Yii;

class MemberStatusEnum extends BaseEnum
{
    const MEMBER_STATUS_ENABLED = 1;
    const MEMBER_STATUS_DISABLED = 0;
    const MEMBER_STATUS_DELETED = -1;

    public static function getMap(): array
    {
        return [
            static::MEMBER_STATUS_ENABLED => Yii::t('adminlte4', 'Enabled'),
            static::MEMBER_STATUS_DISABLED => Yii::t('adminlte4', 'Disabled'),
            static::MEMBER_STATUS_DELETED => Yii::t('adminlte4', 'Deleted'),
        ];
    }
}
