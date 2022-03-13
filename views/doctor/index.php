<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;
use yii\helpers\ArrayHelper;

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\DoctorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Doctors';
$this->params['breadcrumbs'][] = $this->title;
$btnStr = '';
$allowPush = true;
$allowCreate = (Yii::$app->session['_eyadatAuth'] == 2) || PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = (Yii::$app->session['_eyadatAuth'] == 2) || PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = (Yii::$app->session['_eyadatAuth'] == 2) || PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = (Yii::$app->session['_eyadatAuth'] == 2) || PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}

if ($allowPush) {
    $btnStr .= '{push} ';
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
            <div class="panel-body table-responsive">
                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create Doctors', ['create'], ['class' => 'btn btn-success']) : ''; ?>
                </p>
                <div class="clearfix"></div>
                <div class="table-responsive">
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
                            [
                                'attribute' => 'image',
                                'value' => function ($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image;
                                },
                                'format' => ['image', ['width' => '96']],
                                'filter' => false,
                            ],
                            'name_en',
                            'name_ar',
                            'email:email',
                            //'password',
                            'years_experience',
                            [
                                'attribute' => 'speciality',
                                'value' => function ($model) {
                                    $list = '';
                                    if (isset($model->doctorCategories)) {
                                        foreach ($model->doctorCategories as $row) {
                                            $list .= '-' . $row->category->name_en . '<br>';
                                            //array_push($c_list,$cats->category->name_en);
                                        }
                                    }
                                    return $list;
                                },
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'category_id', AppHelper::getRecursiveCategory('D'), ['class' => 'form-control select2', 'prompt' => 'Filter by Speciality']),
                            ],
                            [
                                'attribute' => 'insurance_id',
                                'value' => function ($model) {
                                    $list = '';
                                    if (isset($model->doctorInsurances)) {
                                        foreach ($model->doctorInsurances as $row) {
                                            $list .= '-' . $row->insurance->name_en . '<br>';
                                            //array_push($c_list,$cats->category->name_en);
                                        }
                                    }
                                    return $list;
                                },
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'insurance_id', AppHelper::getInsuranceList(), ['class' => 'form-control select2', 'prompt' => 'Filter by Insurance']),
                            ],
                            [
                                'attribute' => 'gender',
                                'value' => function ($model) {
                                    return (isset($model->gender)) ? $model->gender : '';
                                },
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'gender', [
                                    "M" => "Men",
                                    "W" => "Women",
                                    "U" => "Unisex",

                                ], ['class' => 'form-control select2', 'prompt' => 'FilterGender']),

                            ],
                            [
                                'attribute' => 'clinic_id',
                                'value' => function ($model) {
                                    return (isset($model->clinic)) ? $model->clinic->name_en : '';
                                },
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'clinic_id', AppHelper::getClinicsList(), ['class' => 'form-control select2', 'prompt' => 'Clinic/Hospital']),
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                            ],
                            [
                                'label' => 'Status',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_active, [
                                            'class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->doctor_id,
                                            'onclick' => 'common.changeStatus("doctor/publish",this,' . $model->doctor_id . ')'
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->doctor_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                            ],
                            [
                                'label' => 'Featured',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_featured, [
                                            'class' => "onoffswitch-checkbox", 'id' => "featuredButton" . $model->doctor_id,
                                            'onclick' => 'common.changeStatus("doctor/featured",this,' . $model->doctor_id . ')'
                                        ])
                                        . '<label class="onoffswitch-label" for="featuredButton' . $model->doctor_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_featured', [1 => 'Featured', 0 => 'Non-Featured'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                            ],

                            //'qualification:ntext',
                            //'image',
                            //'gender',
                            //'type',
                            //'consultation_time_online:datetime',
                            //'consultation_time_offline:datetime',
                            //'doctor_id',
                            //'consultation_price_regular',
                            //'consultation_price_final',
                            //'is_active',
                            //'is_deleted',
                            //'created_at',
                            //'updated_at',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $btnStr,
                                'buttons' => [
                                    'push' => function ($url, $model) {
                                        if ($model->is_active == 1) {
                                            return Html::a('<i class="glyphicon glyphicon-export"></i> ', "javascript:;", [
                                                'title' => Yii::t('yii', 'Send push'),
                                                'onclick' => 'common.openPushPopup("' . $model->doctor_id . '","' . $model->name_en . '")',
                                            ]);
                                        } else {
                                            return '';
                                        }
                                    },
                                ],
                            ],
                        ],
                    ]); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pushModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Push notification</h4>
            </div>
            <div class="modal-body">
                <div id="pushResult"></div>
                <input id="pushItem" type="hidden" name="push_item_id" value="" />
                <input id="pushTitle" name="txtMessage" class="form-control" placeholder="Title" />
                <span class="clearfix">&nbsp;</span>
                <textarea id="pushMsg" name="txtMessage" class="form-control" placeholder="Message"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="common.sendTargetedPush('doctor/send-push')" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>