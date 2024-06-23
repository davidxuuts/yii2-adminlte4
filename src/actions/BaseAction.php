<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\actions;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class BaseAction extends Action
{
    /**
     * @var string|ActiveRecordInterface|null class name of the model which will be handled by this action.
     * The model class must implement [[ActiveRecordInterface]].
     * This property must be set.
     */
    public string|ActiveRecordInterface|null $modelClass = null;
    /**
     * @var callable|null a PHP callable that will be called to return the model corresponding
     * to the specified primary key value. If not set, [[findModel()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($id, $action) {
     *     // $id is the primary key value. If composite primary key, the key values
     *     // will be separated by comma.
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return the model found, or throw an exception if not found.
     */
    public $findModel;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!($this->modelClass)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param int|string|null $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return Model|ActiveRecordInterface|ActiveRecord the model found
     */
    public function findModel(int|string|null $id): Model|ActiveRecordInterface|ActiveRecord
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        /* @var $modelClass ActiveRecordInterface|Model|ActiveRecord */
        $modelClass = $this->modelClass;
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
