<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
\app\assets\SelectAsset::register($this);

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


/* @var $this yii\web\View */
/* @var $searchModel app\models\InsurancesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Insurances';
$this->params['breadcrumbs'][] = $this->title;
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
                    <?= ($allowCreate) ?Html::a('Create Insurances', ['create'], ['class' => 'btn btn-success']) : ''; ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'image',
                                'value' => function($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image;
                                },
                                'format' => ['image', ['width' => '96']],
                                'filter' => false,
                            ],
                            'name_en',
                            'name_ar',
                            //'phone',
                            //'address',
                            [
                                'label' => 'Status',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return '<div class="onoffswitch">'
                                            . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->insurance_id,
                                                'onclick' => 'common.changeStatus("insurance/publish",this,' . $model->insurance_id . ')'
                                            ])
                                            . '<label class="onoffswitch-label" for="myonoffswitch' . $model->insurance_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                            ],
                            //'is_active',
                            //'is_deleted',
                            //'created_at',
                            //'updated_at',

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