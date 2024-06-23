<?php

namespace davidxu\adminlte4\widgets;

use Throwable;
use yii\helpers\Html;

/**
 * Class SmallBox
 * @package davidxu\adminlte4\widgets
 */
class SmallBox extends Widget
{
    public string $title = '';

    public string $text = '';

    public string $icon = '';

    public string $theme = '';

    public false|string $linkText = false;

    public ?string $linkUrl = null;

    public array $linkOptions = [];

    public function init(): void
    {
        parent::init();

        $this->initOptions();

        echo Html::beginTag('div', $this->options) . "\n";
        echo $this->renderInner() . "\n";
        echo $this->renderIcon() . "\n";
        echo $this->renderFooter() . "\n";
    }

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        echo $this->renderLoadingStyle(['iconSize' => 'fa-3x']);
        echo "\n" . Html::endTag('div');
    }

    protected function renderInner(): string
    {
        $h = Html::tag('h3', $this->title);
        $p = Html::tag('p', $this->text);
        return Html::tag('div', $h . $p, ['class' => 'inner']);
    }

    protected function renderIcon()
    {
        $i = Html::tag('i', '', ['class' => $this->icon]);
        return Html::tag('div', $i, ['class' => 'icon']);
    }

    protected function renderFooter()
    {
        $i = Html::tag('i', '', ['class' => 'fas fa-arrow-circle-right']);

        if ($this->linkText !== false) {
            empty($this->linkText) && $this->linkText = 'More info';
        }
        return Html::a($this->linkText.' '.$i, $this->linkUrl, $this->linkOptions);
    }

    /**
     * Initializes the widget options,
     * This method sets the default values for various options.
     */
    protected function initOptions(): void
    {
        $this->options = array_merge([
            'class' => 'small-box'
        ], $this->options);
        $this->theme || $this->theme = 'info';
        Html::addCssClass($this->options, 'bg-'.$this->theme);

        $this->linkOptions = array_merge([
            'class' => 'small-box-footer'
        ], $this->linkOptions);
    }
}