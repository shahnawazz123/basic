<?php

use yii\helpers\Html;
use yii\grid\GridView;

use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\BannerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Banners';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$btnStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
$allowActivate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'publish', Yii::$app->user->identity->admin_id, 'A');

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
                    <?= Html::a('Create Banner', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); 
                ?>

                <?php if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $gridViewClassName = \himiklab\sortablegrid\SortableGridView::className();
                } else {
                    $gridViewClassName = GridView::className();
                }

                echo $gridViewClassName::widget([
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
                            'attribute' => 'image_en',
                            'value' => function ($model) {
                                if ($model->image_en != "") {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image_en;
                                } else {
                                    return '';
                                }
                            },
                            'format' => ['image', ['width' => '128']],
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'image_ar',
                            'value' => function ($model) {
                                if ($model->image_ar != "") {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image_ar;
                                } else {
                                    return '';
                                }
                            },
                            'format' => ['image', ['width' => '128']],
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'position',
                            'value' => function ($model) {
                                return (!empty($model->position)) ? $model->position : '-';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'position', ['Top' => 'Top', 'Center' => 'Center', 'Bottom' => 'Bottom'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],

                        [
                            'attribute' => 'link_type',
                            'value' => function ($model) {
                                return (!empty($model->link_type)) ? app\helpers\BannerHelper::$bannerTypes[$model->link_type] : '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'link_type', app\helpers\BannerHelper::$bannerTypes, ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        [
                            'attribute' => 'link_name',
                            'value' => function ($model) {
                                if ($model->link_type == "C") {
                                    $clinic = app\models\Clinics::find()
                                        ->where(['clinic_id' => $model->link_id])
                                        ->one();
                                    if (!empty($clinic)) {
                                        return $clinic->name_en;
                                    }
                                } else if ($model->link_type == "D") {
                                    $doctor = \app\models\Doctors::find()
                                        ->where(['doctor_id' => $model->link_id])
                                        ->one();
                                    if (!empty($doctor)) {
                                        return $doctor->name_en;
                                    }
                                } else if ($model->link_type == "H") {
                                    $hospital = \app\models\Clinics::find()
                                        ->where(['clinic_id' => $model->link_id])
                                        ->one();
                                    if (!empty($hospital)) {
                                        return $hospital->name_en;
                                    }
                                } else if ($model->link_type == "L") {
                                    $lab = \app\models\Labs::find()
                                        ->where(['lab_id' => $model->link_id])
                                        ->one();
                                    if (!empty($lab)) {
                                        return $lab->name_en;
                                    }
                                } else if ($model->link_type == "F") {
                                    $pharmacy = \app\models\Pharmacies::find()
                                        ->where(['pharmacy_id' => $model->link_id])
                                        ->one();
                                    if (!empty($pharmacy)) {
                                        return $pharmacy->name_en;
                                    }
                                } else {
                                    return $model->url;
                                }
                            },
                        ],
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function ($model, $url) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                    . Html::checkbox('onoffswitch', $model->is_active, [
                                        'class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->banner_id,
                                        'onclick' => 'common.changeStatus("banner/publish",this,' . $model->banner_id . ')',
                                        'disabled' => ($allowActivate) ? false : true,
                                    ])
                                    . '<label class="onoffswitch-label" for="myonoffswitch' . $model->banner_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        // 'is_deleted',
                        // 'url:ntext',
                        ['class' => 'yii\grid\ActionColumn', 'template' => $btnStr,],
                    ],
                ]); ?>

            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>