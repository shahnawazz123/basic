<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\components\EditableColumn;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\app\assets\SelectAsset::register($this); 
$this->title = 'Countries';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(BaseUrl::home() . 'js/country.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$permissionStr = '';
$allowActivateCod = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'activate', Yii::$app->user->identity->admin_id, 'A');
$allowActivate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'publish', Yii::$app->user->identity->admin_id, 'A');
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
                    <?= ($allowCreate)?Html::a('Create country', ['create'], ['class' => 'btn btn-success']):"" ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'flag',
                            'value' => function($model) {
                                if ($model->flag != "") {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->flag;
                                } else {
                                    return "";
                                }
                            },
                            'format' => ['image', ['width' => '64']],
                            'filter' => false,
                        ],
                        'name_en',
                        'name_ar',
                        /*[
                            'value' => function($model) {
                                return $model->shipping_cost;
                            },
                            'attribute' => 'shipping_cost',
                            'format' => 'raw',
                            
                        ],*/
                        
                       /* [
                            'value' => function($model) {
                                return $model->express_shipping_cost;
                            },
                            
                            'attribute' => 'express_shipping_cost',
                            'format' => 'raw',
                            
                        ],*/
                        
                        /*[
                            'value' => function($model) {
                                return $model->cod_cost;
                            },
                            'attribute' => 'cod_cost',
                            'format' => 'raw',
                            
                        ],*/
                       
                        /*[
                            'value' => function($model) {
                                return $model->vat;
                            },
                            
                            'attribute' => 'vat',
                            'format' => 'raw',
                        
                        ],*/
                        /*[
                            'label' => 'COD Status',
                            'attribute' => 'is_cod_enable',
                            'format' => 'raw',
                            'value' => function ($model, $url) use ($allowActivateCod) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_cod_enable, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->country_id,
                                            'onclick' => 'country.changeStatus("country/activate",this,' . $model->country_id . ')',
                                            'disabled' => ($allowActivateCod)?false:true,
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->country_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_cod_enable', [1 => 'Yes', 0 => 'No'], ['class' => 'form-control select2', 'prompt' => 'Filter']),
                        ],*/
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function ($model, $url) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "active-switch" . $model->country_id,
                                            'onclick' => 'country.activeStatus("country/publish",this,' . $model->country_id . ')',
                                            'disabled' => ($allowActivate)?false:true,
                                        ])
                                        . '<label class="onoffswitch-label" for="active-switch' . $model->country_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        // 'is_deleted',
                        ['class' => 'yii\grid\ActionColumn','template' => $permissionStr],
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