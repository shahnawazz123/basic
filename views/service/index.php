<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\ServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Services';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$btnStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
//$allowPublish = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'publish', Yii::$app->user->identity->admin_id, 'A');

$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}

if ($allowCreate) {
    $btnStr .= '{create} ';
}

if ($allowView) {
    $btnStr .= '{view} ';
}
if ($allowUpdate) {
    $btnStr .= '{update} ';
}
if ($allowDelete) {
    $btnStr .= '{delete} ';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create Services', ['create'], ['class' => 'btn btn-success']):''; ?>
                </p>

                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                <?php if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $gridViewClassName = \himiklab\sortablegrid\SortableGridView::className();
                } else {
                    $gridViewClassName = GridView::className();
                }

                echo $gridViewClassName::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                                'name_en',
                                'name_ar',
                                [
                                    'attribute' => 'image_en',
                                    'value' => function($image) {
                                        return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_en;
                                    },
                                    'format' => ['image', ['width' => '96']],
                                    'filter' => false,
                                ],
                                [
                                    'attribute' => 'image_ar',
                                    'value' => function($image) {
                                        return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_ar;
                                    },
                                    'format' => ['image', ['width' => '96']],
                                    'filter' => false,
                                ],
                                 [
                                    'label' => 'Status',
                                    'attribute' => 'is_active',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return '<div class="onoffswitch">'
                                                . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->service_id,
                                                    'onclick' => 'common.changeStatus("service/publish",this,' . $model->service_id . ')'
                                                ])
                                                . '<label class="onoffswitch-label" for="myonoffswitch' . $model->service_id . '"></label></div>';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                                ],
                                //'is_deleted',

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => $btnStr,
                                
                                ],
                            ],
                    ]); ?>
                
                
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>