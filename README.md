davidxu/yii2-adminlte4
======================
adminlte4 for yii2 using [Bootstrap 5](https://getbootstrap.com/)

![home](doc/screenshot-adminlte4.png 'Home')

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require "davidxu/yii2-adminlte4"
```

or add

```
"davidxu/yii2-adminlte4": "^1.0"
```

to the requirement section of your `composer.json` file.


Usage
-----

Once the extension is installed, you can config the path mappings of the view component:

```php
'components' => [
    'view' => [
         'theme' => [
             'pathMap' => [
                '@app/views' => '@vendor/davidxu/yii2-adminlte4/src/views'
             ],
         ],
    ],
],
```

Or copy files from @vendor/davidxu/yii2-adminlte4/src/views to @app/views, then edit.

simply use:

```php
<?= \davidxu\adminlte4\widgets\Alert::widget([
    'type' => 'success',
    'body' => '<h3>Congratulations!</h3>'
]) ?>
```
more for [widgets](https://github.com/davidxuuts/yii2-adminlte4/tree/master/src/widgets)

Create Controller
----
To create a new controller, you can extension it from davidxu\adminlte4\controllers\BaseController

a base controller
```php
class BaseController extends \davidxu\adminlte4\controllers\BaseController
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
    }
}
```

a normal controller
```php

use backend\models\Article;
use davidxu\adminlte4\enums\StatusEnum;
use davidxu\adminlte4\helpers\ActionHelper;
use davidxu\adminlte4\widgets\SweetAlert2;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;
use yii\web\BadRequestHttpException;
use yii\base\ExitException;
use Exception;

class ArticleController extends \davidxu\adminlte4\controllers\BaseController
{
    public ActiveRecordInterface|string|null $modelClass = Article::class;
    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['ajax-edit'], $actions['edit']);
        return $actions;
    }
    
    /**
     * Lists all Article models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->modelClass::find(),
            'sort' => [
                'defaultOrder' => [
                    'order' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * @throws BadRequestHttpException|ExitException|Exception
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id', 0);
        $model = $this->findModel($id, $this->modelClass);
        ActionHelper::activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return ActionHelper::message(Yii::t('app', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer));
            }
            return ActionHelper::message(ActionHelper::getError($model),
                $this->redirect(Yii::$app->request->referrer), SweetAlert2::TYPE_ERROR);
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    
    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionEdit(): mixed
    {
        $id = Yii::$app->request->get('id', 0);
        $model = $this->findModel($id, $this->modelClass);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return ActionHelper::message(Yii::t('app', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer));
            }
            return ActionHelper::message(ActionHelper::getError($model),
                $this->redirect(Yii::$app->request->referrer), SweetAlert2::TYPE_ERROR);
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
```

Create view
----
To create a view file, follow the following:

```php
// article index
<?php

use backend\models\Article;
use common\enums\CategoryTypeEnum;
use davidxu\adminlte4\enums\ModalSizeEnum;
use davidxu\adminlte4\enums\StatusEnum;
use yii\data\ActiveDataProvider;
use davidxu\adminlte4\yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use davidxu\adminlte4\helpers\HtmlHelper;

/**
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('app', 'Content management');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-content-index card card-outline card-secondary">
    <?php Pjax::begin(); ?>
    <div class="card-header">
        <h4 class="card-title"><?= HtmlHelper::encode($this->title); ?> </h4>
    </div>
    <div class="card-body pt-3 pl-0 pr-0">
        <div class="container-fluid">
            <?php try {
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-sm table-bordered table-hover'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'title',
                        [
                            'attribute' => 'status',
                            'format' => 'RAW',
                            'value' => function ($model) {
                                /** @var Article $model */
                                return HtmlHelper::displayStatus($model->status);
                            }
                        ],
                        [
                            'format' => 'RAW',
                            'attribute' => 'order',
                            'headerOptions' => ['class' => 'col-1'],
                            'value' => static function ($model) {
                                /** @var Article $model */
                                return HtmlHelper::sort($model->order);
                            }
                        ],
                        'updated_at:date',
                        [
                            'class' => ActionColumn::class,
                            'header' => Yii::t('app', 'Operate'),
                            'template' => '{ajax-edit} {delete}',
                        ],
                    ],
                ]);
            } catch (Exception|Throwable $e) {
                if (YII_ENV_DEV) {
                    echo 'Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
                    echo $e->getTraceAsString() . "\n";
                }
            } ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
```

```php
// article ajax-edit
<?php

use backend\models\Category;
use yii\base\InvalidConfigException;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Category
 * @var $form ActiveForm
 */

try {
    $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'options' => [
            'class' => 'form-horizontal',
        ],
        'validationUrl' => Url::to(['ajax-edit', 'id' => $model->primaryKey]),
        'fieldConfig' => [
            'options' => ['class' => 'form-group row mb-2'],
            'template' => "<div class='col-sm-2 text-end'>{label}</div>"
                . "<div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
    ]);
    ?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app', 'Edit category') ?></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="container">
            <?= $form->field($model, 'title')->textInput(['maxlength' => 30]) ?>
            <?= $form->field($model, 'keyword')->textarea(['rows' => 2]) ?>
        </div>
    </div>
    <?php
} catch (Exception|InvalidConfigException $e) {
    if (YII_ENV_DEV) {
        echo 'Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
        echo $e->getTraceAsString() . "\n";
    }
}
?>
    <div class="modal-footer">
        <?= Html::button(Yii::t('app', 'Close'), [
            'class' => 'btn btn-secondary',
            'data-bs-dismiss' => 'modal'
        ]) ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end();

```


