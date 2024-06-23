<?php

namespace davidxu\adminlte4\widgets;

use yii\helpers\Html;

/**
 * Class Ribbon
 * @package davidxu\adminlte4\widgets
 *
 * ```php
 * echo Ribbon::widget([
 *      'text' => 'Ribbon',
 *      'theme' => 'info',
 *      'size' => 'lg',
 *      'textSize' => 'lg',
 * ])
 * ```
 */
class Ribbon extends Widget
{
    public string $text = '';

    /**
     * primary, secondary, info, success
     * @var string
     */
    public string $theme = '';

    /**
     * ribbon size
     * lg - large
     * xl - extra large
     * @var string
     */
    public string $size = 'lg';

    /**
     * text size
     * @var string
     */
    public string $textSize = '';

    public array $textOptions = [];

    public function init(): void
    {
        parent::init();

        $this->initOptions();

        echo Html::beginTag('div', $this->options) . "\n";
        echo $this->renderText() . "\n";
    }

    public function run(): void
    {
        echo "\n" . Html::endTag('div');
    }

    protected function renderText(): string
    {
        return Html::tag('div', $this->text, $this->textOptions);
    }

    protected function initOptions(): void
    {
        $this->options = array_merge([
            'class' => 'ribbon-wrapper'
        ], $this->options);
        $this->size && Html::addCssClass($this->options, 'ribbon-'.$this->size);

        $this->textOptions = array_merge([
            'class' => 'ribbon'
        ], $this->textOptions);
        $this->theme || $this->theme = 'primary';
        Html::addCssClass($this->textOptions, 'bg-'.$this->theme);
        $this->textSize && Html::addCssClass($this->textOptions, 'text-'.$this->textSize);
    }
}