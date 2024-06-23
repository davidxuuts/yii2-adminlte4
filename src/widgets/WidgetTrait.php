<?php

namespace davidxu\adminlte4\widgets;

use Throwable;

trait WidgetTrait
{
    public bool $loadingStyle = false;

    /**
     * @throws Throwable
     */
    protected function renderLoadingStyle($config = []): string
    {
        $config = array_merge([
            'id' => $this->options['id'] . '-overlay'
        ], $config);
        return $this->loadingStyle ? LoadingStyle::widget($config) : '';
    }
}