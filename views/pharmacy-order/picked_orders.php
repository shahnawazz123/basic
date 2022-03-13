<?php

use yii\helpers\BaseUrl;
use kartik\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\field\FieldRange;
use yii\widgets\Pjax;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile(BaseUrl::home() . 'js/order.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'Ready for delivery Suborders';
$this->params['breadcrumbs'][] = $this->title;
$btnStr = '';
$allowExport = $allowView = true;
if ($allowView) {
    $btnStr .= '{view} ';
}
Pjax::begin([
    'id' => 'order-list-pjax',
    'timeout' => 60000,
]);
define('time', microtime());
$this->registerJsFile(BaseUrl::home() . 'js/order.js?t='.time, ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style>
    .kv-field-range input[type=text]{
        /*        width: 70px;*/
    }
    .form-control[readonly]
    {background-color: #fff;}
</style>
<?php
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                
                
                <div class="clearfix">&nbsp;</div>
                <div class="table-responsive">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'formatter' => [
                            'class' => 'yii\i18n\Formatter',
                            'nullDisplay' => ''
                        ],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'label' => 'Order number',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a($model->order_number, BaseUrl::home() . 'pharmacy-order/view?id=' . $model->pharmacy_order_id);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'order_number', ['class' => 'form-control'])
                            ],
                            [
                                'label' => 'Pharmacy',
                                'value' => function($model) 
                                {
                                    return $model->pharmacy->name_en;
                                },
                            ],
                            [
                                'label' => 'Quantity',
                                'value' => function($model) {
                                    $qty = $model->quantity;
                                    return $qty;
                                },
                            ],
                            [
                                'label' => 'Total Bill',
                                'attribute' => 'total_amount',
                                'value' => function($model) {
                                    return "KWD " . number_format($model->total_bill, 3);
                                },
                                'filter'=>false,
                                /*'filter' => FieldRange::widget([
                                    'model' => $searchModel,
                                    'label' => '',
                                    'attribute1' => 'from_amt',
                                    'attribute2' => 'to_amt',
                                    'separator' => 'to',
                                    'template' => '{widget}'
                                ]),*/
                                'visible'=>true,
                            ],
                            [
                                'label' => 'Purchased On',
                                'value' => function($model) 
                                {
                                    $newTimeZone = new \DateTimeZone(Yii::$app->params['timezone']);
                                    $dateTime = new \DateTime($model->purchase_date, new \DateTimeZone('UTC'));
                                    $dateTime->setTimezone($newTimeZone);

                                    return $dateTime->format('d-m-Y');
                                },
                                'filter' => '<div class="input-group drp-container" style="width: 230px;">' . DateRangePicker::widget([
                                        'model' => $searchModel,
                                        'attribute' => 'date_range',
                                        'presetDropdown' => false,
                                        'convertFormat' => true,
                                        'useWithAddon' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-y',
                                                'separator' => ' to ',
                                            ],
                                            'opens' => 'left',
                                            'ranges' => [
                                                "Today" => ["moment().startOf('day')", "moment()"],
                                                "Yesterday" => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                                                "Last 7 Days" => ["moment().startOf('day').subtract(6, 'days')", "moment()"],
                                                "Last 30 Days" => ["moment().startOf('day').subtract(29, 'days')", "moment()"],
                                                "Last Week" => ["moment().startOf('day').subtract(7, 'days')", "moment()"],
                                                "Last Month" => ["moment().startOf('day').subtract(31, 'days')", "moment()"],
                                            ],
                                        ],
                                    ]) . '</div>',
                            ],
                            [
                                'label' => 'Pharmacy Status',
                                'filter' => false,
                                'value' => function($model) {
                                    $pharmacyOrderStatus = \app\models\PharmacyOrderStatus::find()
                                            ->join('LEFT JOIN', 'pharmacy_orders', 'pharmacy_order_status.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                                            ->where(['pharmacy_orders.order_id' => $model->order_id])
                                            ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                            ->one();
                                    if (!empty($pharmacyOrderStatus)) {
                                        return $pharmacyOrderStatus->pharmacyStatus->name_en;
                                    } else {
                                        return "";
                                    }
                                },
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'filter' => false,
                            ],
                            [
                                'label' => 'Driver',
                                'value' => function($model) 
                                {
                                    $driver_id = $model->driverId;
                                    $driverModel = \app\models\Drivers::findOne($driver_id);
                                    return (!empty($driverModel)) ? $driverModel->name_en : '';

                                },
                            ],
                            
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $btnStr
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>
