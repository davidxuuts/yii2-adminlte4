<?php

namespace davidxu\adminlte4\enums;

use Yii;

class ModalSizeEnum extends BaseEnum
{
    const SIZE_SMALL = 'modal-sm';
    const SIZE_LARGE = 'modal-lg';
    const SIZE_EXTRA_LARGE = 'modal-xl';

    public static function getMap(): array
    {
        return [
            static::SIZE_SMALL => Yii::t('adminlte4', 'Small'),
            static::SIZE_LARGE => Yii::t('adminlte4', 'Large'),
            static::SIZE_EXTRA_LARGE => Yii::t('adminlte4', 'Larger'),
        ];
    }
}
