<?php
namespace davidxu\adminlte4\widgets;

use Throwable;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\bootstrap5\Widget;

/**
 * Class Alert
 * @package davidxu\adminlte4\widgets
 */
class Alert extends Widget
{
    public array $alertTypes = [
        'danger' => [
            'class' => 'alert-danger',
            'icon' => 'fa-ban'
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => 'fa-info'
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => 'fa-exclamation-triangle'
        ],
        'success' => [
            'class' => 'alert-success',
            'icon' => 'fa-check'
        ],
        'light' => [
            'class' => 'alert-light',
        ],
        'dark' => [
            'class' => 'alert-dark'
        ]
    ];

    public ?string $type = 'success';

    public ?string $title = 'Alert!';

    public ?string $icon = null;

    /**
     * @var ?string the body content in the alert component.
     */
    public ?string $body = null;

    /**
     * @var bool whether or not the body has the head
     */
    public bool $simple = false;

    /**
     * @var array|false the options for rendering the close button tag.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to 'X'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     */
    public array|false $closeButton = [];

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
        if (!isset($this->alertTypes[$this->type])) {
            throw new ErrorException('unsupported type: '.$this->type);
        }
    }

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        $head = '';
        if (!$this->simple) {
            $icon = $this->icon ?? $this->alertTypes[$this->type]['icon'] ?? null;
            $iconHtml = $icon ? '<i class="icon bi '.$icon.'"></i>' : '';
            $head = '<h5>'.$iconHtml.' '.$this->title.'</h5>';
        }

        echo \yii\bootstrap5\Alert::widget([
            'body' => $head.$this->body,
            'closeButton' => $this->closeButton,
            'options' => [
                'id' => $this->getId().'-'.$this->type,
                'class' => $this->alertTypes[$this->type]['class']
            ]
        ]);
    }
}