<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\actions;

use davidxu\adminlte4\helpers\ActionHelper;
use Yii;
use yii\base\ExitException;

class AjaxEditAction extends BaseAction
{
    /**
     * Create or edit model
     * @throws ExitException
     */
    public function run()
    {
        $controller = $this->controller;
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        ActionHelper::activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? ActionHelper::message(Yii::t('adminlte4', 'Saved successfully'),
                    $controller->redirect(Yii::$app->request->referrer))
                : ActionHelper::message(ActionHelper::getError($model), $controller->redirect(['index']), 'error');
        }

        return $controller->renderAjax($controller->action->id, [
            'model' => $model,
        ]);
    }
}
