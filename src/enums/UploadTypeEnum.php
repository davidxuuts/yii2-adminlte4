<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\enums;
use Yii;

/**
 * UploadType Enum
 *
 * Class UploadTypeEnum
 * @package davidxu\adminlte4\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class UploadTypeEnum extends BaseEnum
{
    public const DRIVE_LOCAL = 'local';
    public const DRIVE_QINIU = 'qiniu';
    public const DRIVE_OSS = 'oss';
    public const DRIVE_OSS_DIRECT_PASSING = 'oss-direct-passing';
    public const DRIVE_COS = 'cos';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DRIVE_LOCAL => Yii::t('adminlte4', 'Local'),
            self::DRIVE_QINIU => Yii::t('adminlte4', 'Qiniu'),
            self::DRIVE_OSS => Yii::t('adminlte4', 'OSS'),
            self::DRIVE_OSS_DIRECT_PASSING => Yii::t('adminlte4', 'OSS direct passing'),
            self::DRIVE_COS => Yii::t('adminlte4', 'COS'),
        ];
    }
}
