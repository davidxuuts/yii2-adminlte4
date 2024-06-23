<?php

namespace davidxu\adminlte4\helpers;

use davidxu\adminlte4\enums\StatusEnum;
use yii\bootstrap5\BaseHtml;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class HtmlHelper extends BaseHtml
{
    /**
     * @param int|string $value
     * @param array|null $options
     * @return string
     */
    public static function sort(int|string $value, ?array $options = []): string
    {
        $options = ArrayHelper::merge([
            'data-message' => Yii::t('adminlte4', 'Only number allowed for display order'),
            'class' => 'form-control form-control-sm sort-input',
            'data-current-url' => Url::current(),
            'onblur' => 'editOrder(this)',
        ], $options);

        return self::input('number', 'order', $value, $options);
    }

    public static function displayStatus(int $status = StatusEnum::STATUS_ENABLED, $options = [])
    {
        $listBut = [
            StatusEnum::STATUS_DISABLED => self::tag('p', StatusEnum::getValue($status), array_merge(
                [
                    'class' => 'text-secondary',
                ], $options
            )),
            StatusEnum::STATUS_ENABLED => self::tag('p', StatusEnum::getValue($status), array_merge(
                [
                    'class' => 'text-success',
                ], $options
            )),
            StatusEnum::STATUS_DELETED => self::tag('p', StatusEnum::getValue($status), array_merge(
                [
                    'class' => 'text-danger',
                ], $options
            )),
        ];

        return $listBut[$status] ?? '';
    }
}
