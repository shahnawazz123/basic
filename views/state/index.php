<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\StateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'States';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$permissionStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
$allowActivate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'activate', Yii::$app->user->identity->admin_id, 'A');
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
                <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>
                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create state', ['create'], ['class' => 'btn btn-success']) : "" ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'country_id',
                            'value' => function($model) {
                                return !empty($model->country) ? $model->country->nicename : "";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'country_id', app\helpers\AppHelper ::getCountryList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Country']),
                        ],
                        'name_en',
                        'name_ar',
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function($model) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->state_id,
                                            'onclick' => 'common.status("state/activate",this,' . $model->state_id . ')',
                                            'disabled' => ($allowActivate)?false:true,
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->state_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        // 'is_deleted',
                        ['class' => 'yii\grid\ActionColumn', 'template' => $permissionStr],
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