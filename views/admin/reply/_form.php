<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
?>

<div class="rule-form">
    <?php $form = ActiveForm::begin([
        'action' => $this->context->action->id == 'create' && !$model->getIsNewRecord() ? ['update', 'id' => $model->id] : '',
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'labelOptions' => [
                'class' => 'control-label col-sm-2'
            ],
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n</div>\n<div class=\"col-sm-4\">\n{hint}\n{error}\n</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>
    <?php $statuses = $model::$statuses; unset($statuses[$model::STATUS_DELETED]) ?>
    <?= $form->field($model, 'status')->dropDownList($statuses) ?>
    <?= $form->field($model, 'priority')->textInput() ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button id="addKeyword" class="btn btn-success" type="button"><span class="glyphicon glyphicon-plus"></span> <b>添加关键字</b></button>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= ListView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $keywords
                ]),
                'itemView' => '_ruleKeyword',
                'viewParams' => [
                    'form' => $form
                ],
                'emptyText' => false,
                'summary' => false
            ]) ?>
            <?php if (!$model->getIsNewRecord()): ?>
                <?= ListView::widget([
                    'dataProvider' => new ActiveDataProvider([
                        'query' => $model->getKeywords(),
                        'sort' => [
                            'defaultOrder' => [
                                'created_at' => SORT_DESC
                            ]
                        ]
                    ]),
                    'itemView' => '_ruleKeyword',
                    'viewParams' => [
                        'form' => $form
                    ],
                    'emptyText' => false,
                    'summary' => false
                ]) ?>
            <?php endif ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <?= Html::submitButton($model->getIsNewRecord() ? '提交设置' : '提交修改', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script id="keywordTemplate" type="text/html">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-10">
            <?= $this->render('_ruleKeyword', [
                'model' => $ruleKewordModel,
                'form' => $form
            ]) ?>
        </div>
    </div>
</script>
<?php
$keywordsNum = count($keywords);
$script = <<<EOF
    var i = {$keywordsNum};
    $('#addKeyword').click(function() {
        $(this)
            .closest('.form-group')
            .after(template('keywordTemplate')().replace(/(name="[^"\[]+)(\[)([^"]+")/g, '\\$1[new][' + i++ + '][\\$3'));

    });
EOF;
$this->registerJs($script);