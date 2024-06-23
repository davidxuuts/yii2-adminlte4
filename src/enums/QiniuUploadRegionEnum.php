<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\enums;

/**
 * QiniuUploadRegion Enum
 *
 * Class QiniuUploadRegionEnum
 * @package davidxu\base\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class QiniuUploadRegionEnum extends BaseEnum
{

    const EC_ZHEJIANG = 'z0';
    const EC_ZHEJIANG_2 = 'cn-east-2';
    const NC_HEBEI = 'z1';
    const SC_GUANGDONG = 'z2';
    const NA_LOS_ANGELES = 'na0';
    const AP_SINGAPORE = 'as0';
    const AP_SEOUL = 'ap-northeast-1';

    /**
     * @inheritDoc
     */
    public static function getMap(): array
    {
        return [
            self::EC_ZHEJIANG => 'https://up.qiniup.com',
            self::EC_ZHEJIANG_2 => 'https://up-cn-east-2.qiniup.com',
            self::NC_HEBEI => 'https://up-z1.qiniup.com',
            self::SC_GUANGDONG => 'https://up-z2.qiniup.com',
            self::NA_LOS_ANGELES => 'https://up-na0.qiniup.com',
            self::AP_SINGAPORE => 'https://up-as0.qiniup.com',
            self::AP_SEOUL => 'https://up-ap-northeast-1.qiniup.com',
        ];
    }
}
