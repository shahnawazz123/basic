<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AttributeSetsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$permissionStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowCreateAtt = PermissionHelper::checkUserHasAccess("attribute", 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');

if ($allowView) {
    $permissionStr .= '{view}';
}
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
$this->title = 'Attribute sets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create attribute set', ['create'], ['class' => 'btn btn-success']) : "" ?>
                    <?= ($allowCreateAtt) ? Html::a('Create attribute', ['attribute/create'], ['class' => 'btn btn-success']) : "" ?>
                </p>
                <?=
                GridView::widget([
                    'id' => Yii::$app->controller->id,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'format' => 'raw',
                            'attribute' => 'name_en',
                            'value' => function ($model) {
                                //return $model->name_en;
                                return Editable::widget([
                                    'name' => "name_en",
                                    'value' => $model->name_en,
                                    'attribute' => 'name_en',
                                    'header' => 'Name in English',
                                    'type' => 'primary',
                                    'asPopover' => false,
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'formOptions' => ['action' => ['attribute-set/editable-field?field=name_en']],
                                    'afterInput' => Html::hiddenInput('id', $model->attribute_set_id),
                                ]);
                            },
                        ],
                        [
                            'format' => 'raw',
                            'attribute' => 'name_ar',
                            'value' => function ($model) {
                                //return $model->name_ar;
                                return Editable::widget([
                                    'name' => "name_ar",
                                    'value' => $model->name_ar,
                                    'attribute' => 'name_ar',
                                    'header' => 'Name in Arabic',
                                    'type' => 'primary',
                                    'asPopover' => false,
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'formOptions' => ['action' => ['attribute-set/editable-field?field=name_ar']],
                                    'afterInput' => Html::hiddenInput('id', $model->attribute_set_id),
                                ]);
                            },
                        ],
                        'attribute_set_code',
                        ['class' => 'yii\grid\ActionColumn', 'template' => $permissionStr],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
