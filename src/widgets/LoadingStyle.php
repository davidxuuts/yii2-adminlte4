<?php

namespace davidxu\adminlte4\widgets;

use yii\helpers\Html;

class LoadingStyle extends Widget
{
    public string|int $iconSize = '';

    public function init(): void
    {
        parent::init();

        $this->initOptions();
    }

    public function run()
    {
        $i = Html::tag('i', '', ['class' => "fas $this->iconSize fa-sync-alt fa-spin"]);
        return Html::tag('div', $i, $this->options);
    }

    protected function initOptions(): void
    {
        $this->options = array_merge([
            'class' => 'overlay'
        ], $this->options);

        $this->iconSize = $this->iconSize ?? 'fa-2x';
    }
}