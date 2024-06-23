<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\enums;
use Yii;

/**
 * AttachmentType Enum
 *
 * Class AttachmentTypeEnum
 * @package davidxu\adminlte4\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class AttachmentTypeEnum extends BaseEnum
{
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_NEWS   = 'news';
    public const TYPE_THUMBNAIL = 'thumb';
    public const TYPE_OTHER = 'other';

    public const TYPE_IMAGES = 'images';
    public const TYPE_VIDEOS = 'videos';
    public const TYPE_AUDIOS = 'audios';
    public const TYPE_OTHERS = 'others';
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TYPE_IMAGE => Yii::t('base', 'Images'),
            self::TYPE_VIDEO => Yii::t('base', 'Videos'),
            self::TYPE_AUDIO => Yii::t('base', 'Audios'),
            self::TYPE_NEWS => Yii::t('base', 'Hybrids'),
            self::TYPE_THUMBNAIL => Yii::t('base', 'Thumbnails'),
            self::TYPE_OTHER => Yii::t('base', 'Others'),
        ];
    }
}
