<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\models;

use davidxu\adminlte4\enums\AttachmentTypeEnum;
use davidxu\adminlte4\enums\StatusEnum;
use davidxu\adminlte4\enums\UploadTypeEnum;
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
            'id' => Yii::t('adminlte4', 'ID'),
            'member_id' => Yii::t('adminlte4', 'Uploader'),
            'drive' => Yii::t('adminlte4', 'Driver'),
            'file_type' => Yii::t('adminlte4', 'Upload type'),
            'specific_type' => Yii::t('adminlte4', 'Specific type'),
            'path' => Yii::t('adminlte4', 'File path'),
            'poster' => Yii::t('adminlte4', 'Video poster'),
            'hash' => Yii::t('adminlte4', 'File hash'),
            'name' => Yii::t('adminlte4', 'Original name'),
            'extension' => Yii::t('adminlte4', 'Extension'),
            'size' => Yii::t('adminlte4', 'File size'),
            'year' => Yii::t('adminlte4', 'Year'),
            'month' => Yii::t('adminlte4', 'Month'),
            'day' => Yii::t('adminlte4', 'Day'),
            'width' => Yii::t('adminlte4', 'Width'),
            'height' => Yii::t('adminlte4', 'Height'),
            'duration' => Yii::t('adminlte4', 'Duration'),
            'upload_ip' => Yii::t('adminlte4', 'Upload IP'),
            'status' => Yii::t('adminlte4', 'Status'),
            'created_at' => Yii::t('adminlte4', 'Created at'),
            'updated_at' => Yii::t('adminlte4', 'Updated at'),
        ];
    }
}
