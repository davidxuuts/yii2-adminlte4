<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace davidxu\adminlte4\yii\grid;

use Yii;
use yii\helpers\Html;
use davidxu\adminlte4\enums\ModalSizeEnum;

class ActionColumn extends \yii\grid\ActionColumn
{
    public string $modalToggle = 'modal';
    public string $modalTarget = '#modal';
    public string $modalAriaLabel = '';
    public string $modalSize = '';

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons(): void
    {
        $this->initDefaultButton('view', 'eye-fill');
        $this->initDefaultButton('update', 'pencil-fill');
        $this->initDefaultButton('edit', 'pencil-fill');
        $this->initDefaultButton('delete', 'trash3-fill', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);

        $this->initDefaultButton('ajax-edit', 'pencil-square', [
            'data-bs-toggle' => $this->modalToggle ?? 'modal',
            'data-bs-target' => $this->modalTarget ?? '#modal',
            'title' => $this->modalAriaLabel ?? Yii::t('adminlte4', 'Edit'),
            'aria-label' => $this->modalAriaLabel ?? Yii::t('adminlte4', 'Edit'),
            'data-bs-modal-class' => !empty($this->modalSize) ? $this->modalSize : ModalSizeEnum::SIZE_LARGE,
        ]);
    }
    
    /**
     * Initializes the default button rendering callback for single button.
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = []): void
    {
        if (!isset($this->buttons[$name]) && str_contains($this->template, '{' . $name . '}')) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                $title = match ($name) {
                    'view' => Yii::t('yii', 'View'),
                    'update' => Yii::t('yii', 'Update'),
                    'edit' => Yii::t('adminlte4', 'Edit'),
                    'delete' => Yii::t('yii', 'Delete'),
                    default => ucfirst($name),
                };
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('i', '', ['class' => "bi bi-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}