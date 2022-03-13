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
$this->title = 'Payments';
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
                    
                <?php 
                    $total_cash = 0;
                    $total_paid = 0;
                    $total_knetcard = 0;

                ?>
                <div class="table table-responsive">
                    <?php $gridColumns = [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute'=>'order_id',
                                'label'=>'Ref #',
                                'value'=> function($model){
                                    return $model->order->order_id;
                                },
                            ],
                            [
                                'attribute'=>'order_number',
                                'label'=>'Order Number',
                                'value'=> function($model){
                                    return $model->order->order_number;
                                },
                            ],
                            [
                                'label' => 'User Name',
                                'value' => function($model) {
                                    return (!empty($model->order->user)) ? $model->order->user->first_name.' '.$model->order->user->last_name : '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'user_id', app\helpers\AppHelper::getAllUser(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
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
                                'filter' => Html::activeDropDownList($searchModel, 'paymode', ['C' => 'Cash on delivery', 'CC' => 'Credit Card', 'K' => 'KNET'], ['class' => 'form-control select2', 'prompt' => 'Filter By Payment Mode']),
                            ],
                            [
                                'label' => 'Pharmacy',
                                'value' => function($model) use ($admin_commission){
                                    $pharmacyOrders = app\models\PharmacyOrders::find()
                                            ->join('RIGHT JOIN','order_items','pharmacy_orders.pharmacy_order_id=order_items.pharmacy_order_id')
                                            ->where(['pharmacy_orders.order_id' => $model->order->order_id])
                                            ->all();
                                    $str = '';
                                    foreach ($pharmacyOrders as $pharmacyOrder) {
                                        $str .= !empty($pharmacyOrder->pharmacy) ? $pharmacyOrder->pharmacy->name_en : 'not set';
                                        $admin_commission = !empty($pharmacyOrder->pharmacy) ? $pharmacyOrder->pharmacy_commission : 0;
                                    }
                                    return $str;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pharmacy_id', app\helpers\ProductHelper ::getPharmacyList(), ['class' => 'form-control select2', 'prompt' => 'Filter']),
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'format' => 'raw',
                            ],
                            [
                                'label' => 'Amount',
                                'attribute' => 'gross_amount',
                                'value' => function($model) use($total_cash,$total_paid,$total_knetcard)
                                {
                                    return isset($model->gross_amount) ? $model->currency_code . " " . $model->gross_amount : "0.000 KWD";
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
                            ],
                            [
                                'label' => 'Eyadat Commission',
                                'attribute' => 'total_amount',
                                'value' => function($model) use($admin_commission){
                                   $pharmacyOrders = app\models\PharmacyOrders::find()
                                            ->join('RIGHT JOIN','order_items','pharmacy_orders.pharmacy_order_id=order_items.pharmacy_order_id')
                                            ->where(['pharmacy_orders.order_id' => $model->order->order_id])
                                            ->all();
                                    $admin_commission = '';
                                    foreach ($pharmacyOrders as $pharmacyOrder) {
                                        $admin_commission = !empty($pharmacyOrder->pharmacy) ? $pharmacyOrder->pharmacy_commission : 0;
                                    }
                                    return 'KWD '.number_format($admin_commission,3);
                                },
                                'filter'=>false,
                                'headerOptions' => ['style' => 'width:200px'],
                            ],
                            [
                                'label' => 'Paid On',
                                'value' => function($model) {
                                    if (isValidDate($model->payment_date)) 
                                    {
                                        $newTimeZone1 = new \DateTimeZone(Yii::$app->params['timezone']);
                                        $dateTime1 = new \DateTime($model->payment_date, new \DateTimeZone('UTC'));
                                        $dateTime1->setTimezone($newTimeZone1);
                                        return $dateTime1->format('M d, Y h:i A');
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
                                    ],
                                    'pluginEvents' => [
                                        "cancel.daterangepicker" => "function() {
                                           $('#paymentsearch-invoice_date_range').val('');
                                           $('.grid-view').yiiGridView('applyFilter');
                                        }",
                                        'apply.daterangepicker' => 'function(ev, picker) {
                                            var val = picker.startDate.format(picker.locale.format) + picker.locale.separator +
                                            picker.endDate.format(picker.locale.format);
                                            $(\'#paymentsearch-invoice_date_range\').val(val);
                                            $(\'.grid-view\').yiiGridView(\'applyFilter\');
                                        }',
                                    ],
                                ]) . '</div>',
                                'headerOptions' => ['style' => 'width:230px'],
                            ],
                            [
                                'attribute' => 'result',
                                'value' => function($model) {
                                    return $model->result;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'result', \app\helpers\AppHelper ::getPaymentStatus(), ['class' => 'form-control select2', 'prompt' => 'Filter By Result']),
                            ],
                            'transaction_id',
                            'ref',
                            'TrackID',
                           
                    ];?>

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

                    <br><br>
                    <div class="col-md-7"></div>
                    <div class="col-md-5" >
                      
                        <?php
                            $sql1    = $dataProvider->query->createCommand()->rawSql;
                            $dbCommand1 = Yii::$app->db->createCommand($sql1);
                            $query = $dbCommand1->queryAll();
                            
                            if(!empty($query))
                            {
                                foreach($query as $row)
                                {
                                    if ($row['payment_mode'] == 'C') 
                                    {
                                        $total_cash += $row['gross_amount'] ;
                                    } elseif ($row['payment_mode'] == 'CC' || $row['payment_mode'] == 'K') {
                                        $total_knetcard += $row['gross_amount'] ;
                                    } 

                                    $total_paid += $row['gross_amount'] ;

                                }
                            }


                        //if (!empty($dataProvider)) {
                            echo DetailView::widget([
                                'model'     => $searchModel,
                                'buttons1'  => '',
                                'panel' => [
                                    'heading' => 'Payment Summary',
                                    'type' => DetailView::TYPE_INFO,
                                ],
                                'attributes' => [
                                    [
                                        'label' => 'Cash',
                                        'value' => number_format($total_cash,3).' KWD',
                                        'format' => 'raw',
                                        'labelColOptions' => ['hidden' => false]
                                    ],
                                    [
                                        'label' => 'Knet/Card',
                                        'value' => number_format($total_knetcard,3).' KWD',
                                        'format' => 'raw',
                                        'labelColOptions' => ['hidden' => false]
                                    ],
                                    [
                                        'label' => 'Total Amount',
                                        'value' => number_format($total_paid,3).' KWD',
                                        'format' => 'raw',
                                        'labelColOptions' => ['hidden' => false]
                                    ],
                                ],
                            ]);
                        //}
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>