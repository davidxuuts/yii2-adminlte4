<?php

namespace davidxu\adminlte4\helpers;

use davidxu\adminlte4\widgets\SweetAlert2;
use Yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\bootstrap5\ActiveForm;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class ActionHelper
{
    /**
     * Message redirect
     *
     * @param mixed $msg Message
     * @param mixed $redirectUrl Redirect URL
     * @param string $type Message type [success/error/info/warning]
     * @return mixed
     */
    public static function message(mixed $msg, mixed $redirectUrl, string $type = SweetAlert2::TYPE_SUCCESS): mixed
    {
        if (!in_array($type, [SweetAlert2::TYPE_SUCCESS, SweetAlert2::TYPE_ERROR, SweetAlert2::TYPE_INFO, SweetAlert2::TYPE_WARNING])) {
            $type = SweetAlert2::TYPE_SUCCESS;
        }
        Yii::$app->session->setFlash($type, $msg);
        return $redirectUrl;
    }

    /**
     * @param Model|ActiveRecordInterface $model
     * @return string
     */
    public static function getError(Model|ActiveRecordInterface $model): string
    {
        return self::analysisErrors($model->getFirstErrors());
    }

    /**
     * Analysis Errors
     *
     * @param array|string $errors
     * @return bool|string
     */
    public static function analysisErrors(array|string $errors): bool|string
    {
        if (!is_array($errors) || empty($errors)) {
            return false;
        }
        $firstErrors = array_values($errors)[0];
        return $firstErrors ?? Yii::t('adminlte4', 'Error message not fount');
    }

    /**
     * @param Model|ActiveRecord|ActiveRecordInterface $model
     * @return void
     * @throws ExitException
     */
    public static function activeFormValidate(Model|ActiveRecord|ActiveRecordInterface $model): void
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = ActiveForm::validate($model);
                Yii::$app->end();
            }
        }
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array
     */
    public static function jsonMessage(int $code = 404, string $message = '', array $data = []): array
    {
        if (!$message) {
            $message = Yii::t('adminlte4', 'Data not fount');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'code' => (string)$code,
            'message' => trim($message),
            'data' => $data ? ArrayHelper::toArray($data) : [],
        ];
    }
}
