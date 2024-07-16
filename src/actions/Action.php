<?php

namespace davidxu\adminlte4\actions;

use davidxu\adminlte4\enums\AttachmentTypeEnum;
use davidxu\adminlte4\enums\UploadTypeEnum;
use davidxu\adminlte4\models\Attachment;
use FFMpeg\FFMpeg;
use Yii;
use Qiniu\Etag;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\web\Response;
use yii\i18n\PhpMessageSource;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\TimeCode;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\base\Exception;


class Action extends \yii\base\Action
{
    /** @var string */
    public string $url = '';

    /** @var ?string */
    public ?string $fileDir = '';

    /** @var bool */
    public bool $allowAnony = false;

    /** @var ActiveRecord|Attachment|string  */
    public ActiveRecord|Attachment|string $attachmentModel = Attachment::class;

    /**
     * @return array[]|void
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        if (Yii::$app->user->isGuest && !$this->allowAnony) {
            $result = [
                'success' => false,
                'data' => Yii::t('adminlte4', 'Anonymous user is not allowed, please login first'),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    /**
     * @param array|object $params
     * @return array|true[]
     */
    protected function getHash(array|object $params): array
    {
        /** @var Attachment|ActiveRecordInterface|ActiveRecord $model */
        $model = $this->attachmentModel::findOne(['hash' => $params['hash']]);
        if ($model) {
            if (!($model->poster)) {
                $model->poster = '/images/default-video.jpg';
            }
            $result = [
                'success' => true,
                'data' => $model,
            ];
        } else {
            $result = [
                'success' => false,
                'data' => [],
            ];
        }
        return $result;
    }

    /**
     * @param UploadedFile|null $file
     * @param object|array $params
     * @param string $url
     * @param string $dir
     * @return array|bool|bool[]
     * @throws Exception
     */
    protected function localInfo(?UploadedFile $file, object|array $params, string $url, string $dir): array|bool
    {
        return $this->processChunk($file, $params, $url, $dir);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function qiniuInfo(array $params): array
    {
        $extension = explode('.', $params['extension']);
        $params['extension'] = $extension[count($extension) - 1];
        if ($params['file_type'] === 'null' && $params['specific_type']) {
            $specificType = explode('/', $params['specific_type']);
            $params['file_type'] = $specificType[0];
        }
        if ($params['width'] === 'null') {
            $params['width'] = 0;
        }
        if ($params['height'] === 'null') {
            $params['height'] = 0;
        }
        if ($params['duration'] === 'null' || $params['duration'] === '') {
            $params['duration'] = null;
        }
        if ($params['upload_ip'] === 'null') {
            $params['upload_ip'] = $this->getClientIP();
        }

        if ($params['store_in_db'] === true || $params['store_in_db'] === 'true') {
            $model = $this->attachmentModel::findOne(['hash' => $params['hash']]);
            if ($model) {
                return [
                    'success' => true,
                    'data' => $model,
                ];
            }
            /** @var ActiveRecord|Attachment|string $model */
            $model = new $this->attachmentModel;
            $model->attributes = $params;
//            $extension = explode('.', $model->extension);
//            $model->extension = $extension[count($extension) - 1];
//            if ($model->file_type === 'null' && $model->specific_type) {
//                $specificType = explode('/', $model->specific_type);
//                $model->file_type = $specificType[count($specificType) - 1];
//            }
//            if ($model->width === 'null') {
//                $model->width = 0;
//            }
//            if ($model->height === 'null') {
//                $model->height = 0;
//            }
//            if ($model->duration === 'null' || $model->duration === '') {
//                $model->duration = null;
//            }
            if (!str_starts_with($model->path, '/')) {
                $model->path = '/' . $model->path;
            }
            $model->year = date('Y');
            $model->month = date('m');
            $model->day = date('d');
//            if ($model->upload_ip === 'null') {
//                $model->upload_ip = Yii::$app->request->userIP;
//            }
            if ($model->save()) {
                $model->refresh();
                $result = [
                    'success' => true,
                    'data' => $model,
                ];
            } else {
                $msg = YII_ENV_PROD
                    ? Yii::t('adminlte4', 'Data writing error')
                    : array_values($model->getFirstErrors())[0];
                $result = [
                    'success' => false,
                    'data' => $msg,
                ];
            }
        } else {
            $result = [
                'success' => true,
                'data' => $params,
            ];
        }
        return $result;
    }

    /**
     * @param UploadedFile|null $file
     * @param array|object $params
     * @param string $url
     * @param string $dir
     * @return array
     * @throws Exception
     */
    private function processChunk(?UploadedFile $file, array|object $params, string $url, string $dir): array
    {
        $chunksStorePath = Yii::getAlias('@runtime/chunks');
        if (!is_dir($chunksStorePath)) {
            @mkdir($chunksStorePath, 0755, true);
        }

        if (isset($params['eof']) && (bool)($params['eof'])) {
            if (str_ends_with($url, '/')) {
                $url = rtrim($url);
            }
            if (str_ends_with($dir, '/')) {
                $dir = rtrim($dir);
            }

            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            if ($params['key']) {
                if (substr($params['key'], 1) === '/') {
                    $key = ltrim($params['key'], 1);
                } else {
                    $key = $params['key'];
                }
                $urlPath = $url . DIRECTORY_SEPARATOR . $key;
                $savePath = $dir . DIRECTORY_SEPARATOR . $key;
            } else {
                $relativePath = DIRECTORY_SEPARATOR
                    . $params['file_type'] . DIRECTORY_SEPARATOR
                    . date('Ymd') . DIRECTORY_SEPARATOR
                    . Yii::$app->security->generateRandomString() . $params['extension'];
                $urlPath = $url . $relativePath;
                $savePath = $dir . $relativePath;
            }
            $saveDirArr = explode($savePath, DIRECTORY_SEPARATOR);
            $saveDirArr2 = [];
            for ($i = 0; $i < count($saveDirArr) -1; $i++) {
                $saveDirArr2[] = $saveDirArr[$i];
            }
            $saveDir = implode(DIRECTORY_SEPARATOR, $saveDirArr2);
            if (!is_dir($saveDir)) {
                @mkdir($saveDir, 0755, true);
            }

            Yii::info($saveDir);

//            if (!is_dir($savePath)) {
//                @mkdir($savePath, 0755, true);
//            }

            if ($this->mergeChunkFile($chunksStorePath, $params, $savePath)) {
                return $this->getLocalInfo($savePath, $urlPath, $params);
            } else {
                return [
                    'success' => false,
                    'eof' => (bool)$params['eof'],
                    'eof_txt' => $params['eof'],
                    'completed' => false,
                    'data' => Yii::t('adminlte4', 'Data writing error'),
                ];
            }
        } else {
            if (!($file->saveAs($chunksStorePath . DIRECTORY_SEPARATOR
                . $params['chunk_key'] . '_' . $params['chunk_index']))
            ) {
                return [
                    'success' => false,
                    'completed' => false,
                    'data' => Yii::t('adminlte4', 'Data writing error'),
                ];
            } else {
                $data = $params;
                unset($data['chunk_index'], $data['file_field'], $data['_csrf-backend']);
                return [
                    'success' => true,
                    'completed' => true,
                    'data' => $data,
//                    'data' => [
//                        'key' => $params['key'],
//                        'extension' => $params['extension'],
//                        'file_type' => $params['file_type'],
//                        'chunk_key' => $params['chunk_key'],
//                        'total_chunks' => $params['total_chunks'],
//                    ],
                ];
            }
        }
    }

    /**
     * @param string $storePath
     * @param array|object $params
     * @param string $savePath
     * @return bool
     */
    private function mergeChunkFile(string $storePath, array|object $params, string $savePath): bool
    {
        $chunks = [];
        for ($i = 0; $i < $params['total_chunks']; $i++) {
            $chunkFile = $storePath . DIRECTORY_SEPARATOR . $params['chunk_key'] . '_' . $i;
            if(file_exists($chunkFile) && filesize($chunkFile) > 0) {
                $chunks[$i] = $chunkFile;
//                $chunks[$i] = $params['chunk_key'] . '_' . $i;
            } else {
                break;
            }
        }

        $fp = fopen($savePath, 'wb+');
        if (flock($fp, LOCK_EX)) {
            foreach ($chunks as $chunk) {
                $handle = fopen($chunk,"rb");
                if (fwrite($fp, fread($handle, filesize($chunk)))) {
                    fclose($handle);
                    unset($handle);
                    unlink($chunk);
                }
            }
        }
        if (flock($fp, LOCK_UN) && fclose($fp)) {
            unset($fp);
            return true;
        }
        return false;
    }

    /**
     * @param string $savePath
     * @param string $urlPath
     * @param array|object $params
     * @return array
     */
    private function getLocalInfo(string $savePath, string $urlPath, array|object $params): array
    {
        $width = $height = 0;
        $duration = $poster = null;
        if (isset($params['file_type'])) {
            if ($params['file_type'] === AttachmentTypeEnum::TYPE_IMAGE) {
                [$width, $height] =  getimagesize($savePath);
            }
            if (in_array($params['file_type'], [AttachmentTypeEnum::TYPE_VIDEO, AttachmentTypeEnum::TYPE_AUDIO])) {
                [$duration, $poster, $hasPoster] = $this->getAvDuration($savePath, $params['extension']);
                $poster = $hasPoster ? str_replace($params['extension'], '', $savePath) . '.jpg' : $poster;
            }
        }

        $info = [
            'member_id' => Yii::$app->user->isGuest ? 0 : Yii::$app->user->id,
            'drive' => UploadTypeEnum::DRIVE_LOCAL,
            'specific_type' => $params['mime_type'],
            'file_type' => $params['file_type'],
            'path' => $urlPath,
            'poster' => $poster,
            'name' => $params['name'],
            'extension' => ltrim(trim($params['extension']), 1),
            'size' => $params['size'],
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'hash' => Etag::sum($savePath) ? Etag::sum($savePath)[0] : null,
            'upload_ip' => Yii::$app->request->userIP,
        ];

        if (isset($params['store_in_db']) && (true === $params['store_in_db'] || $params['store_in_db'] === 'true')) {
            return $this->saveLocalToDB($info);
        } else {
            if ($cache = Yii::$app->cache) {
                $cache->set($info['path'], $info, null, new TagDependency(['tags' => $info['path'] . $info['hash']]));
            }
            return [
                'success' => true,
                'completed' => true,
                'data' => $info,
            ];
        }
    }

    /**
     * @param string $file
     * @param string $extension
     * @return array
     */
    private function getAvDuration(string $file, string $extension): array
    {
        try {
            $ffProbe = isset(Yii::$app->params['ffmpeg']['ffprobe.binaries'])
            && isset(Yii::$app->params['ffmpeg']['ffmpeg.binaries'])
                ? FFProbe::create(Yii::$app->params['ffmpeg'])
                : FFProbe::create();
            $ffmpeg = isset(Yii::$app->params['ffmpeg']['ffprobe.binaries'])
            && isset(Yii::$app->params['ffmpeg']['ffmpeg.binaries'])
                ? FFMpeg::create(Yii::$app->params['ffmpeg'])
                : FFMpeg::create();
            $duration = $ffProbe->format($file)->get('duration');

            $offset = rand(0, intval($duration));
            $video = $ffmpeg->open($file);
            $frame = $video->frame(TimeCode::fromSeconds($offset));
            $poster = str_replace($extension, '', $file) . '.jpg';
            $frame->save($poster);
            Image::thumbnail($poster, 400, 0)->save($poster);
            return [
                $duration,
                $poster,
                true
            ];
        } catch (\Exception) {
            return [
                null,
                '/images/icon-video.png',
                false
            ];
        }
    }

    /**
     * @param array|object $info
     * @return array
     */
    private function saveLocalToDB(array|object $info): array
    {
        /** @var ActiveRecord|ActiveRecordInterface|object|Attachment|array $model */
        if (isset($info['hash'])) {
            $model = $this->attachmentModel::findOne(['hash' => $info['hash']]);
            if ($model) {
                if (!($model->poster) || $model->poster === '') {
                    $model->poster = '/images/icon-video.png';
                }
                $model->path = $this->url . $model->path;
                return [
                    'success' => true,
                    'completed' => true,
                    'data' => $model,
                ];
            } else {
                return $this->saveNewRecord($info);
            }
        } else {
            return $this->saveNewRecord($info);
        }
    }

    /**
     * @param array|object $info
     * @return array
     */
    private function saveNewRecord(array|object $info): array
    {
        $model = new $this->attachmentModel;
        $model->attributes = $info;
        if ($model->save()) {
            $model->refresh();
            return [
                'success' => true,
                'completed' => true,
                'data' => $model,
            ];
        } else {
            $msg = YII_ENV_PROD
                ? Yii::t('adminlte4', 'Data writing error')
                : array_values($model->getFirstErrors())[0];
            return [
                'success' => false,
                'completed' => true,
                'data' => $msg,
            ];
        }
    }

    /**
     * @return array|string|null
     */
    private function getClientIP(): array|string|null
    {
        $header = Yii::$app->request->getHeaders();
        if ($header->get('x-real-ip')) {
            $ip = $header->get('x-real-ip');
        } else if ($header->get('HTTP_CLIENT_IP')) {
            $ip = $header->get('HTTP_CLIENT_IP');
        } else if ($header->get('HTTP_X_FORWARDED_FOR')) {
            $ip = $header->get('HTTP_X_FORWARDED_FOR');
        } else if ($header->get('REMOTE_ADDR')) {
            $ip = $header->get('REMOTE_ADDR');
        } else {
            $ip = Yii::$app->request->getUserIP();
        }
        return $ip;
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $i18n = Yii::$app->i18n;
//        $i18n->translations['dropzone*'] = [
//            'class' => PhpMessageSource::class,
//            'sourceLanguage' => 'en-US',
//            'basePath' => Yii::getAlias('@davidxu/dropzone/messages'),
//            'fileMap' => [
//                'dropzone' => 'dropzone.php',
//            ],
//        ];
        $i18n->translations['adminlte4*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => Yii::getAlias('@davidxu/adminlte4/messages'),
            'fileMap' => [
                '*' => 'adminlte4.php',
            ],
        ];
    }
}
