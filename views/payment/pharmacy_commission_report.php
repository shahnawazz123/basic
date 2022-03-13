<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\field\FieldRange;
use kartik\detail\DetailView;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use app\helpers\AppHelper;

\app\assets\SelectAsset::register($this);
$this->title = 'Pharmacy Commission Report';
$this->params['breadcrumbs'][] = $this->title;
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
$admin_commission = 0;
$allowExport = true;?>
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
                <div class="table table-responsive">
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
                                    'label' => 'Bill to Name',
                                    'attribute' => 'user_id',
                                    'value' => function($model) {
                                        return $model->user_name;
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'user_id', app\helpers\AppHelper::getAllUser(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                                ],
                                
                                [
                                    'label' => 'Total Amount',
                                    'attribute' => 'total_order_amount',
                                    'value' => function($model) {
                                        return isset($model->total_order_amount) ? $model->currency_code . " " . $model->total_order_amount : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    'headerOptions' => ['style' => 'width:200px'],
                                    'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                ],
                                [
                                    'label' => 'Commision %',
                                    'attribute' => 'order_admin_commission',
                                    'value' => function($model) {
                                        return isset($model->order_admin_commission) ? $model->currency_code . " " . $model->order_admin_commission : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    //'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                ],
                                [
                                    'label' => 'Admin Commision',
                                    'attribute' => 'order_admin_commission',
                                    'value' => function($model) {
                                        $totalAmt = isset($model->total_order_amount) ?  $model->total_order_amount : 0;
                                        return isset($model->order_admin_commission) ?  $model->currency_code . " " .($totalAmt * $model->order_admin_commission) / 100 : "KWD 0.000";
                                    },
                                    'filter' => false,
                                    //'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
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