<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\field\FieldRange;
use kartik\detail\DetailView;
use kartik\daterange\DateRangePicker;
use app\helpers\PermissionHelper;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\app\assets\SelectAsset::register($this);
$this->title = 'Pharmacy Sale Report';
$this->params['breadcrumbs'][] = $this->title;
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
$admin_commission = 0;
$allowExport = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'export', Yii::$app->user->identity->admin_id, 'A');
?>
<style>
    .kv-field-range input[type=text]{
        width: 70px;
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                  
                <div class="">
                    <?php 
                        $gridColumns = [
                            ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'label' => 'Invoice #',
                                    'value' => function($model) {
                                        return $model->transaction_id;
                                    },
                                    'filter' => Html::activeTextInput($searchModel, 'transaction_id', ['class' => 'form-control'])
                                ],
                                [
                                    'label' => 'Invoice Date',
                                    'value' => function($model) {
                                        if (isValidDate($model->payment_date)) {
                                            return date("d-m-Y H:i A", strtotime($model->payment_date));
                                        }
                                    },
                                    'filter' => '<div class="input-group drp-container" style="width: 230px;">' . DateRangePicker::widget([
                                        'model' => $searchModel,
                                        'attribute' => 'invoice_date_range',
                                        'presetDropdown' => true,
                                        'convertFormat' => true,
                                        'useWithAddon' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-y',
                                                'separator' => ' to ',
                                            ],
                                            'opens' => 'left'
                                        ]
                                    ]) . '</div>',
                                ],
                                [
                                    'label' => 'Order #',
                                    'value' => function($model) {
                                        return $model->order->order_number;
                                    },
                                    'filter' => Html::activeTextInput($searchModel, 'order_number', ['class' => 'form-control'])
                                ],
                                [
                                    'label' => 'Order Date',
                                    'value' => function($model) {
                                        return date("d-m-Y H:i A", strtotime($model->order->create_date));
                                    },
                                    'filter' => '<div class="input-group drp-container" style="width: 230px;">' . DateRangePicker::widget([
                                        'model' => $searchModel,
                                        'attribute' => 'order_date_range',
                                        'presetDropdown' => true,
                                        'convertFormat' => true,
                                        'useWithAddon' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-y',
                                                'separator' => ' to ',
                                            ],
                                            'opens' => 'left'
                                        ]
                                    ]) . '</div>',
                                ],
                                [
                                    'attribute' => 'paymode',
                                    'value' => function($model) {
                                        $return = "";
                                        if ($model->paymode == 'C') {
                                            $return = 'Cash on delivery';
                                        } elseif ($model->paymode == 'CC') {
                                            $return = 'Credit Card';
                                        } elseif ($model->paymode == 'K') {
                                            $return = 'KNET';
                                        }
                                        return $return;
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'paymode', ['C' => 'Cash on delivery','CC' => 'Credit Card','K' => 'KNET'], ['class' => 'form-control select2', 'prompt' => 'Filter By Payment Mode']),
                                ],
                                [
                                    'label' => 'Bill to Name',
                                    'attribute' => 'user_id',
                                    'value' => function($model) {
                                        return $model->user_name;
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'user_id', app\helpers\AppHelper::getAllUser(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                                ],
                                [
                                    'label' => 'Status',
                                    'attribute' => 'result',
                                    'value' => function($model) {
                                        return $model->result;
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'result', app\helpers\AppHelper::getPaymentStatus(), ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                                //'filter' => false
                                ],/*
                                [
                                    'label' => 'Amount',
                                    'attribute' => 'net_amount',
                                    'value' => function($model) {
                                        return isset($model->gross_amount) ? $model->currency_code . " " . $model->gross_amount : "KWD 0.000";
                                    },
                                    'filter' => FieldRange::widget([
                                        'model' => $searchModel,
                                        'label' => '',
                                        'attribute1' => 'from_amt',
                                        'attribute2' => 'to_amt',
                                        'separator' => 'to',
                                        'template' => '{widget}'
                                            //'type' => FieldRange::INPUT_SPIN,
                                    ]),
                                    'headerOptions' => ['style' => 'width:200px'],
                                ],*/
                                [
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                    'attribute' => 'delivery_charge',
                                    'value' => function($model) {
                                        return "KWD " . $model->delivery_charge;
                                    }
                                ],
                                [
                                    'label' => 'Admin Commision',
                                    'attribute' => 'order_admin_commission',
                                    'value' => function($model) {
                                        return isset($model->order_admin_commission) ? $model->currency_code . " " . $model->order_admin_commission : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    //'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                ],
                                [
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                    'attribute' => 'cod_charge',
                                    'value' => function($model) {
                                        return "KWD " . $model->cod_charge;
                                    }
                                ],
                                [
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                    'attribute' => 'vat_charges',
                                    'value' => function($model) {
                                        return "KWD " . $model->vat_charges;
                                    }
                                ],
                                [
                                    'label' => 'Total Item Price',
                                    'attribute' => 'total_order_amount',
                                    'value' => function($model) {
                                        return isset($model->total_order_amount) ? $model->currency_code . " " . $model->total_order_amount : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    'headerOptions' => ['style' => 'width:200px'],
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                ],
                                [
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                    'attribute' => 'discount_price',
                                    'value' => function($model) {
                                        return "KWD " . $model->discount_price;
                                    }
                                ],

                                /*[
                                    'label' => 'Net Amount',
                                    'value' => function($model) {
                                        return !empty($model->net_amount) ? $model->currency_code . " " . $model->net_amount : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    'headerOptions' => ['style' => 'width:200px'],
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                ],*/
                                [
                                    'label' => 'Status',
                                    'value' => function($model) {
                                        return isset($model->result) ? $model->result : "";
                                    },

                                ],
                                [
                                    'label' => 'Order Status',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        $orderStatus = app\models\OrderStatus::find()
                                            ->where(['order_id' => $model->type_id])
                                            ->orderBy(['order_status_id' => SORT_DESC])
                                            ->one();

                                        return (!empty($orderStatus)) ? $orderStatus->status->name_en : "";
                                    },

                                ],
                        ];
                        ?>
                        <?php echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,
                        'exportConfig' => [
                            ExportMenu::FORMAT_TEXT => false,
                            ExportMenu::FORMAT_PDF => false,
                            ExportMenu::FORMAT_CSV => false,
                            ExportMenu::FORMAT_HTML => false
                        ]
                    ]); ?>

                    <?= \kartik\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => $gridColumns,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>