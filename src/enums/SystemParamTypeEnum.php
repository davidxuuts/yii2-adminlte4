<?php

namespace davidxu\adminlte4\enums;

use Yii;

class SystemParamTypeEnum extends BaseEnum
{
    public const CONFIG_TEXT = 'text';
    public const CONFIG_PASSWORD = 'password';
    public const CONFIG_SECRETKEY_TEXT = 'secretKeyText';
    public const CONFIG_TEXTAREA = 'textarea';
    public const CONFIG_DROPDOWN_LIST = 'dropDownList';
    public const CONFIG_RADIO = 'radioList';
    public const CONFIG_CHECKBOX = 'checkboxList';
    public const CONFIG_EDITOR = 'editor';
    public const CONFIG_IMAGE = 'image';
    public const CONFIG_IMAGES = 'images';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::CONFIG_TEXT => Yii::t('adminlte4', 'Text'),
            self::CONFIG_PASSWORD => Yii::t('adminlte4', 'Password'),
            self::CONFIG_SECRETKEY_TEXT => Yii::t('adminlte4', 'SecretKeyText'),
            self::CONFIG_TEXTAREA => Yii::t('adminlte4', 'Textarea'),
            self::CONFIG_DROPDOWN_LIST => Yii::t('adminlte4', 'DropDownList'),
            self::CONFIG_RADIO => Yii::t('adminlte4', 'RadioList'),
            self::CONFIG_CHECKBOX => Yii::t('adminlte4', 'CheckboxList'),
            self::CONFIG_EDITOR => Yii::t('adminlte4', 'Rich text'),
            self::CONFIG_IMAGE => Yii::t('adminlte4', 'Image'),
            self::CONFIG_IMAGES => Yii::t('adminlte4', 'Images'),
        ];
    }

}
