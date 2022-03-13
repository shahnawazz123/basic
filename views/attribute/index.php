<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AttributesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attributes';
$this->params['breadcrumbs'][] = $this->title;
$permissionStr = '';

$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');

if ($allowView) {
    $permissionStr .= '{view}';
}
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>
                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create attribute', ['create'], ['class' => 'btn btn-success']) : "" ?>
                </p>
                <?=
                GridView::widget([
                    'id' => Yii::$app->controller->id,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'name_en',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Editable::widget([
                                    'name' => "name_en",
                                    'value' => $model->name_en,
                                    'attribute' => 'name_en',
                                    'header' => 'Name in English',
                                    'type' => 'primary',
                                    'asPopover' => false,
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'formOptions' => ['action' => ['attribute/editable-field?field=name_en']],
                                    'afterInput' => Html::hiddenInput('id', $model->attribute_id),
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'name_ar',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Editable::widget([
                                    'name' => "name_ar",
                                    'value' => $model->name_ar,
                                    'attribute' => 'name_ar',
                                    'header' => 'Name in Arabic',
                                    'type' => 'primary',
                                    'asPopover' => false,
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'formOptions' => ['action' => ['attribute/editable-field?field=name_ar']],
                                    'afterInput' => Html::hiddenInput('id', $model->attribute_id),
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'code',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Editable::widget([
                                    'name' => "code",
                                    'value' => $model->code,
                                    'attribute' => 'code',
                                    'header' => 'Code',
                                    'type' => 'primary',
                                    'asPopover' => false,
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'formOptions' => ['action' => ['attribute/editable-field?field=code']],
                                    'afterInput' => Html::hiddenInput('id', $model->attribute_id),
                                ]);
                            },
                        ],
                        ['class' => 'yii\grid\ActionColumn', 'template' => $permissionStr],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
