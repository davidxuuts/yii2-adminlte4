<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\widgets;

use davidxu\adminlte4\assets\BaseUploadAsset;
use davidxu\adminlte4\enums\UploadTypeEnum;
use davidxu\adminlte4\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Qiniu\Auth;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\i18n\PhpMessageSource;
use yii\bootstrap5\InputWidget as BS5InputWidget;
use Yii;
use yii\web\View;

/**
 * Input widget
 * @property array $clientOptions
 * @property array $metaData
 * @property string $url
 * @property string $uploadBasePath
 * @property string $drive
 * @property bool $storeInDB
 * @property int|string $chunkSize
 * @property string $qiniuBucket
 * @property string $qiniuAccessKey
 * @property string $qiniuSecretKey
 * @property string $qiniuCallbackUrl
 * @property array $qiniuCallbackBody
 */
class InputWidget extends BS5InputWidget
{

    public ?string $lang;
    public $clientOptions = [];
    public array $metaData = [];
    public ?array $headers = null;
    public ?string $url = '@web/upload/local';
    public ?string $getHashUrl = '/upload/get-hash';
    public string $uploadBasePath = 'uploads/';
    public string $drive = UploadTypeEnum::DRIVE_LOCAL;
    public bool $storeInDB = true;
    public int|float $chunkSize = 4 * 1024 * 1024;
    public bool $secondUpload = false;
    public int|string $maxFiles = 1;
    public string|array|null $acceptedFiles = null;
    public ?array $existFiles = null;
    public string $selectorUrl = '/upload/selector';

    // Qiniu
    public ?string $qiniuBucket = null;
    public ?string $qiniuAccessKey = null;
    public ?string $qiniuSecretKey = null;
    public ?string $qiniuCallbackUrl = null;
    public array $qiniuCallbackBody = [
        'drive' => UploadTypeEnum::DRIVE_QINIU,
        'specific_type' => '$(mimeType)',
        'file_type' => '$(x:file_type)',
        'path' => '$(key)',
        'hash' => '$(etag)',
        'size' => '$(fsize)',
        'name' => '$(fname)',
        'extension' => '$(ext)',
        'member_id' => '$(x:member_id)',
        'width' => '$(imageInfo.width)',
        'height' => '$(imageInfo.height)',
        'duration' => '$(avinfo.format.duration)',
        'store_in_db' => '$(x:store_in_db)',
        'upload_ip' => '$(x:upload_ip)',
    ];

    protected View|string|null $_view = null;
    protected ?string $_secondUpload = null;
    protected ?string $_encodedExistFiles = null;
    protected string $_storeInDB = 'false';
    protected ?string $_encodedMetaData = null;
//    protected ?string $_qiniuToken = null;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->lang = $this->lang ?? Yii::$app->language;
        $this->_view = $this->getView();
        BaseUploadAsset::register($this->_view);
        $this->registerBaseTranslations();

        //TODO validation need
        if ($this->selectorUrl) {
            $this->selectorUrl = Yii::getAlias('@web' . $this->selectorUrl);
        }
        if ($this->name === null && !$this->hasModel()) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }

        if (empty($this->name) && (!empty($this->model) && !empty($this->attribute))) {
            $this->name = Html::getInputName($this->model, $this->attribute);
        }
        $this->maxFiles = $this->maxFiles <= 0 ? 1 : $this->maxFiles;
        if (isset($this->acceptedFiles) && is_array($this->acceptedFiles)) {
            $this->acceptedFiles = implode(',', $this->acceptedFiles);
        }
        // second upload function
        $this->_secondUpload = $this->secondUpload ? 'true' : 'false';
        $this->_storeInDB = $this->storeInDB ? 'true' : 'false';

        $keysInclude = ['name', 'size', 'path'];
        if ((!empty($this->existFiles)) && is_array($this->existFiles)) {
            if (count($this->existFiles) === count($this->existFiles, COUNT_RECURSIVE)) {
                $this->existFiles = [$this->existFiles];
            }
            $include = true;
            foreach ($this->existFiles as $existFile) {
                $include = $include && array_intersect($keysInclude, array_keys($existFile)) === $keysInclude;
            }
            if (!$include) {
                throw new InvalidConfigException("'name', 'size' and 'path' must be specified.");
            }
        }
        $this->_encodedExistFiles = Json::encode($this->existFiles ?? []);
        if ($this->secondUpload) {
            if (empty($this->getHashUrl)) {
                throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                    'attribute' => 'getHashUrl',
                ]));
            }
        }

        // Local
        if ($this->drive === UploadTypeEnum::DRIVE_LOCAL) {
            if (empty($this->url)) {
                throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                    'attribute' => 'url',
                ]));
            }
        }

        // Qiniu
        if ($this->drive === UploadTypeEnum::DRIVE_QINIU) {
            if (empty($this->qiniuCallbackUrl)) {
                if (!isset(Yii::$app->params['qiniu.callbackUrl']) || Yii::$app->params['qiniu.callbackUrl'] === '') {
                    throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                        'attribute' => 'qiniuCallbackUrl',
                    ]));
                }
                $this->qiniuCallbackUrl = Yii::$app->params['qiniu.callbackUrl'];
            }

            if (empty($this->qiniuBucket)) {
                if (!isset(Yii::$app->params['qiniu.bucket']) || Yii::$app->params['qiniu.bucket'] === '') {
                    throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                        'attribute' => 'qiniu.bucket',
                    ]));
                }
                $this->qiniuBucket = Yii::$app->params['qiniu.bucket'];
            }

            if (empty($this->qiniuAccessKey)) {
                if (!isset(Yii::$app->params['qiniu.accessKey']) || Yii::$app->params['qiniu.accessKey'] === '') {
                    throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                        'attribute' => 'qiniu.accessKey',
                    ]));
                }
                $this->qiniuAccessKey = Yii::$app->params['qiniu.accessKey'];
            }

            if (empty($this->qiniuSecretKey)) {
                if (!isset(Yii::$app->params['qiniu.secretKey']) || Yii::$app->params['qiniu.secretKey'] === '') {
                    throw new InvalidConfigException(Yii::t('adminlte4', 'Invalid configuration: {attribute}', [
                        'attribute' => 'qiniu.secretKey',
                    ]));
                }
                $this->qiniuSecretKey = Yii::$app->params['qiniu.secretKey'];
            }
        }

        if ($this->drive === UploadTypeEnum::DRIVE_LOCAL) {
            $this->metaData['file_field'] = $this->name;
            $this->metaData['store_in_db'] = $this->storeInDB;
            if (Yii::$app->request->enableCsrfValidation) {
                $this->metaData[Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();
            }
        }
        if ($this->drive === UploadTypeEnum::DRIVE_QINIU) {
            $this->metaData = ArrayHelper::merge([
                'x:store_in_db' => $this->storeInDB,
                'x:member_id' => Yii::$app->user->isGuest ? 0 : Yii::$app->user->id,
                'x:upload_ip' => Yii::$app->request->remoteIP,
            ], $this->metaData);
        }

        $systemMaxFileSize = StringHelper::getSizeInByte(get_cfg_var('upload_max_filesize'));
        $this->chunkSize = StringHelper::getSizeInByte($this->chunkSize);
        if ($this->chunkSize > $systemMaxFileSize) {
            $this->chunkSize = $systemMaxFileSize;
        }
        $this->_encodedMetaData = Json::encode($this->metaData);
    }

    protected function getQiniuToken(): ?string
    {
        if ($this->drive === UploadTypeEnum::DRIVE_QINIU) {
            $auth = new Auth($this->qiniuAccessKey, $this->qiniuSecretKey);
            $policy = [
                'callbackUrl' => $this->qiniuCallbackUrl,
                'callbackBody' => Json::encode($this->qiniuCallbackBody),
                'callbackBodyType' => 'application/json',
            ];
            return $auth->uploadToken($this->qiniuBucket, null, 3600, $policy);
        }
        return null;
    }

    protected function isLocalDrive(): string
    {
        return $this->drive === UploadTypeEnum::DRIVE_LOCAL ? 'true' : 'false';
    }

    protected function isQiniuDrive(): string
    {
        return $this->drive === UploadTypeEnum::DRIVE_QINIU ? 'true' : 'false';
    }

    protected function isCosDrive(): string
    {
        return $this->drive === UploadTypeEnum::DRIVE_COS ? 'true' : 'false';
    }

    protected function isOssDrive(): string
    {
        return $this->drive === UploadTypeEnum::DRIVE_OSS ? 'true' : 'false';
    }

    protected function registerBaseTranslations(): void
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['adminlte4*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => Yii::getAlias('@davidxu/adminlte4/messages'),
            'fileMap' => [
                'dropzone' => 'adminlte4.php',
            ],
        ];
    }
}
