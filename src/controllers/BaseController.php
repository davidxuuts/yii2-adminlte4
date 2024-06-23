<?php

namespace davidxu\adminlte4\controllers;

use davidxu\adminlte4\actions\EditAction;
use davidxu\adminlte4\actions\AjaxEditAction;
use davidxu\adminlte4\actions\SortOrderAction;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\base\Model;

class BaseController extends Controller
{
    /**
     * @var string|ActiveRecordInterface|null
     */
    public ActiveRecordInterface|string|null $modelClass = null;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'destroy' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        return ArrayHelper::merge([
            'edit' => [
                'class' => EditAction::class,
                'modelClass' => $this->modelClass,
            ],
            'ajax-edit' => [
                'class' => AjaxEditAction::class,
                'modelClass' => $this->modelClass,
            ],
            'sort-order' => [
                'class' => SortOrderAction::class,
                'modelClass' => $this->modelClass,
            ],
        ], $actions);
    }

    /**
     * @param int|string|null $id
     * @param string|ActiveRecordInterface|null $modelClass
     * @return ActiveRecordInterface|ActiveRecord|Model
     * @throws BadRequestHttpException
     */
    protected function findModel(int|string|null $id,
                                 string|ActiveRecordInterface $modelClass = null
    ): ActiveRecordInterface|ActiveRecord|Model
    {
        /* @var $modelClass ActiveRecordInterface|Model|ActiveRecord */
        if (!$modelClass) {
            throw new BadRequestHttpException('No modelClass found.');
        }
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        } elseif ($modelClass::findOne($id) === null) {
            $model = new $modelClass;
        }

        if (!isset($model)) {
            $model = new $modelClass;
        }
        $model->loadDefaultValues();
        return $model;
    }
}
