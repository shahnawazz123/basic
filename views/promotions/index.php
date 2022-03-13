<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;
use app\helpers\BannerHelper;
use yii\helpers\ArrayHelper;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $searchModel app\models\PromotionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Promotions';
$this->params['breadcrumbs'][] = $this->title;

$btnStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

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
                <?php
                $form = ActiveForm::begin([
                    'action' => [''],
                    'method' => 'get',
                ]);
                ?>
                <div class="row" hidden>
                    <div class="col-md-8">
                        <?php
                        echo $form->field($searchModel, 'date_range', [
                            'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],
                            'options' => ['class' => 'drp-container form-group col-md-6'],
                        ])->widget(DateRangePicker::classname(), [
                            'useWithAddon' => true,
                            'pluginOptions' => [
                                'locale' => [
                                    'format' => 'Y-MM-DD',
                                    'separator' => ' to ',
                                ],
                                'opens' => 'left'
                            ],
                            'pluginEvents' => [
                                "cancel.daterangepicker" => "function() {
                                               $('#promotions-date_range').val('');
                                               $('#w0').submit();
                                            }",
                                'apply.daterangepicker' => 'function(ev, picker) {
                                                var val = picker.startDate.format(picker.locale.format) + picker.locale.separator +
                                                picker.endDate.format(picker.locale.format);
                                                $(\'#promotions-date_range\').val(val);
                                                $(\'#w0\').submit();
                                            }',
                            ]
                        ]);
                        ?>
                        <br>
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary ']) ?>
                        <?= Html::resetButton('Reset', ['class' => 'btn btn-default ', 'id' => 'resetbtn']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create Promotions', ['create'], ['class' => 'btn btn-success']) : ''; ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); 
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'title_en',
                        'title_ar',
                        'code',
                        'start_date',
                        'end_date',
                        //'promo_type',
                        'promo_count',

                        [
                            'attribute' => 'Promo Used',
                            'value' => function ($model) {
                                if ($model->promo_for == 'D') {
                                    return $model->getPromotionDoctors()->count();
                                } else if ($model->promo_for == 'L') {
                                    return $model->getPromotionLabs()->count();
                                } else if ($model->promo_for == 'C') {
                                    return $model->getPromotionClinics()->count();
                                } else if ($model->promo_for == 'F') {
                                    return $model->getPromotionPharmacy()->count();
                                }
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'Promo Remaining',
                            'value' => function ($model) {
                                if ($model->promo_for == 'D') {
                                    return ($model->promo_count - $model->getPromotionDoctors()->count() );
                                } else if ($model->promo_for == 'L') {
                                    return ($model->promo_count - $model->getPromotionLabs()->count() );
                                } else if ($model->promo_for == 'C') {
                                    return ($model->promo_count - $model->getPromotionClinics()->count() );
                                } else if ($model->promo_for == 'F') {
                                    return ($model->promo_count - $model->getPromotionPharmacy()->count() );
                                }
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'promo_for',
                            'value' => function ($model) {
                                if ($model->promo_for == 'D') {
                                    return 'Doctor';
                                } else if ($model->promo_for == 'L') {
                                    return 'Lab';
                                } else if ($model->promo_for == 'C') {
                                    return 'Clinic';
                                } else if ($model->promo_for == 'F') {
                                    return 'Pharmacy';
                                }
                            },
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'promo_for', BannerHelper::$bannerTypes, ['class' => 'form-control select2', 'prompt' => 'Filter By Promo']),
                        ],
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<div class="onoffswitch">'
                                    . Html::checkbox('onoffswitch', $model->is_active, [
                                        'class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->promotion_id,
                                        'onclick' => 'common.changeStatus("promotions/publish",this,' . $model->promotion_id . ')'
                                    ])
                                    . '<label class="onoffswitch-label" for="myonoffswitch' . $model->promotion_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],
                        //'minimum_order',
                        //'shipping_included',
                        //'registration_start_date',
                        //'registration_end_date',
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
$this->registerJs("
    $('input[name=\"PromotionsSearch[start_date]\"]').attr('type','date');
    $('input[name=\"PromotionsSearch[end_date]\"]').attr('type','date');
", \yii\web\View::POS_END, 'time-picker');
?>