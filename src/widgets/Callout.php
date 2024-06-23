<?php
namespace davidxu\adminlte4\widgets;

use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\bootstrap5\Widget;
use yii\helpers\Html;

/**
 * Class Callout
 * @package davidxu\adminlte4\widgets
 * @example
 * <?= Callout::widget(['type'=>'info', 'head'=>'head string', 'body'=>'body string']) ?>
 * Also possible
 * <?php Callout::begin(['type'=>'info', 'head'=>'head string']) ?>
 *      body content
 * <?php Callout::end() ?>
 */
class Callout extends Widget
{
    /**
     * @var array supported type
     */
    public array $supportedType = ['danger', 'info', 'warning', 'success'];

    public ?string $type = null;

    /**
     * @var ?string
     */
    public ?string $head = null;

    /**
     * @var ?string
     */
    public ?string $body = null;

    /**
     * @inheritdoc
     */
    public $options = [];

    /**
     * @var string $template
     */
    public string $template = <<<html
<div {options}>
    <h5>{head}</h5>
    <p>{body}</p>
</div>
html;

    /**
     * @throws ErrorException
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (is_null($this->type)) {
            $this->type = 'info';
        }
        if (!in_array($this->type, $this->supportedType)) {
            throw new ErrorException('unsupported type: '.$this->type);
        }

        Html::addCssClass($this->options, 'callout');
        Html::addCssClass($this->options, 'callout-'.$this->type);

        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();

        return strtr($this->template, [
            '{options}' => Html::renderTagAttributes($this->options),
            '{head}' => $this->head,
            '{body}' => $this->body.$content
        ]);
    }
}