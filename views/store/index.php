<?php

use yii\helpers\Html;
use yii\grid\GridView;
use himiklab\sortablegrid\SortableGridView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$permissionStr = '';
$this->title = 'Stores';
$this->params['breadcrumbs'][] = $this->title;
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
if ($allowView) {
    $permissionStr .= '{view}';
}
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowCreate)?Html::a('Create Stores', ['create'], ['class' => 'btn btn-success']):"" ?>
                </p>
                <br clear="all"/>
                <!-- <div class="text-right">
                    <a tabindex="0" class="btn btn-md btn-warning" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" title="" data-content="Drag & Drop to change the order"><i class="fa fa-info-circle"></i></a>
                </div> -->
                <br clear="all"/>
               <?=
                SortableGridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'formatter' => [
                        'class' => 'yii\i18n\Formatter',
                        'nullDisplay' => ''
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'name_en',
                        'name_ar',
                        [
                            'attribute' => 'flag',
                            'value' => function($model) {
                                if ($model->flag != "") {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->flag;
                                } else {
                                    return null;
                                }
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],
                        'code',
//                        [
//                            'attribute' => 'currency_id',
//                            'value' => function($model){
//                                return $model->currency->code;
//                            }
//                        ],

                        ['class' => 'yii\grid\ActionColumn', 'template' => $permissionStr],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
