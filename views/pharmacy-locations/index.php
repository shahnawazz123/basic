<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;
use yii\helpers\ArrayHelper;
\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\PharmacyLocationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pharmacy Locations';
$this->params['breadcrumbs'][] = $this->title;
$btnStr = '';
$allowCreate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
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
                    <?=($allowCreate) ?  Html::a('Create Pharmacy Locations', ['create'], ['class' => 'btn btn-success']):''; ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute'=>'pharmacy_id',
                                    'label'=>'Pharmacy',
                                    'value'=>function($model)
                                    {
                                        return (isset($model->pharmacy)) ? $model->pharmacy->name_en : '';
                                    },
                                ],
                                'name_en',
                                'name_ar',
                                [
                                    'attribute' => 'governorate_id',
                                    'value'=>function($model)
                                    {
                                        return $model->governorate->name_en;
                                    }
                                ],
                                [
                                    'attribute' => 'area_id',
                                    'value'=>function($model)
                                    {
                                        return $model->area->name_en;
                                    }
                                ],
                                'block',
                                //'street',
                                //'building',
                                //'name_en',
                                //'name_ar',
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
