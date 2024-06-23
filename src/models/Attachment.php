<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\models;

use davidxu\base\enums\AttachmentTypeEnum;
use davidxu\adminlte4\enums\StatusEnum;
use davidxu\base\enums\UploadTypeEnum;
use Yii;

/**
 * This is the model class for table "{{%common_attachment}}".
 *
 * @property int $id ID
 * @property int|null $member_id Uploader
 * @property string|null $drive Driver
 * @property string|null $file_type File type
 * @property string|null $specific_type Specific type
 * @property string|null $path File path
 * @property string|null $poster Video poster
 * @property string|null $hash File hash
 * @property string|null $name Original name
 * @property string|null $extension Extension
 * @property int|null $size File size
 * @property int|null $year Year
 * @property int|null $month Month
 * @property int|null $day Day
 * @property int|null $width Width
 * @property int|null $height Height
 * @property string|null $duration Duration
 * @property string|null $upload_ip Upload IP
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property int $material_type Compare to WeChat material type
 *
 */
class Attachment extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%common_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['member_id', 'size', 'year', 'month', 'day', 'width', 'height', 'status'], 'integer'],
            [['drive', 'extension', 'duration', 'upload_ip'], 'string', 'max' => 50],
            [['width', 'height'], 'default', 'value' => 0],
            [['drive'], 'in', 'range' => UploadTypeEnum::getKeys()],
            ['drive', 'default', 'value' => UploadTypeEnum::getKeys()],
            ['status', 'in', 'range' => StatusEnum::getKeys()],
            ['status', 'default', 'value' => StatusEnum::STATUS_ENABLED],
            [['file_type'], 'string', 'max' => 10],
            [['file_type'], 'in', 'range' => AttachmentTypeEnum::getKeys()],
            [['specific_type', 'hash'], 'string', 'max' => 100],
            [['path', 'poster'], 'string', 'max' => 1024],
            [['name'], 'string', 'max' => 200],
//            [['hash'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('base', 'ID'),
            'member_id' => Yii::t('base', 'Uploader'),
            'drive' => Yii::t('base', 'Driver'),
            'file_type' => Yii::t('base', 'Upload type'),
            'specific_type' => Yii::t('base', 'Specific type'),
            'path' => Yii::t('base', 'File path'),
            'poster' => Yii::t('base', 'Video poster'),
            'hash' => Yii::t('base', 'File hash'),
            'name' => Yii::t('base', 'Original name'),
            'extension' => Yii::t('base', 'Extension'),
            'size' => Yii::t('base', 'File size'),
            'year' => Yii::t('base', 'Year'),
            'month' => Yii::t('base', 'Month'),
            'day' => Yii::t('base', 'Day'),
            'width' => Yii::t('base', 'Width'),
            'height' => Yii::t('base', 'Height'),
            'duration' => Yii::t('base', 'Duration'),
            'upload_ip' => Yii::t('base', 'Upload IP'),
            'status' => Yii::t('base', 'Status'),
            'created_at' => Yii::t('base', 'Created at'),
            'updated_at' => Yii::t('base', 'Updated at'),
        ];
    }
}
