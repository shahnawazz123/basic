<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AreaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Areas';
$this->params['breadcrumbs'][] = $this->title;
\app\assets\SelectAsset::register($this);
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$permissionStr = '';
$allowActivate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'activate', Yii::$app->user->identity->admin_id, 'A');
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
                <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

                <p class="pull pull-right">
                    <?= ($allowCreate)?Html::a('Create area', ['create'], ['class' => 'btn btn-success']):"" ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'state_id',
                            'value' => function($model) {
                                return $model->state->name_en;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'state_id', app\helpers\AppHelper ::getStates(), ['class' => 'form-control select2', 'prompt' => 'Filter By State']),
                        ],
                        'name_en',
                        'name_ar',
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function($model) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->area_id,
                                            'onclick' => 'common.status("area/activate",this,' . $model->area_id . ')',
                                            'disabled' => ($allowActivate)?false:true,
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->area_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        // 'is_deleted',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $permissionStr,
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>