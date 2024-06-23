<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\actions;

use Yii;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use davidxu\adminlte4\helpers\ActionHelper;

class SortOrderAction extends BaseAction
{

    public function run(): array
    {
        /* @var $modelClass BaseActiveRecord */
        $modelClass = $this->modelClass;
        $id = Yii::$app->request->get('id');
        if (!($model = $modelClass::findOne($id))) {
            return ActionHelper::jsonMessage(404, Yii::t('adminlte4', 'Data not found'));
        }

        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), ['order', 'status']);
        if (!$model->save()) {
            return ActionHelper::jsonMessage(422, ActionHelper::getError($model));
        }
        return ActionHelper::jsonMessage(200, Yii::t('adminlte4', 'Saved successfully'), $model->attributes);
    }
}
