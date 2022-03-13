<?php
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use app\helpers\AppHelper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = 'Dashboard';

$url = \yii\helpers\BaseUrl::home() . "order/index?";
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="animate-panel">

    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Welcome to Eyadat Store panel.
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>ORDER STATISTICS - BY DATE</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bar_chart2"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>ORDER BY STATUS</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bar_chart5"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" hidden>
        <div class="col-md-12" style="">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Appointments">Orders</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">All Orders</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/pharmacy-order/orders'], true) ?>"><?php echo AppHelper::totalPharmacyOrderStatus(); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">Accepted</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/pharmacy-order/orders?OrdersSearch[status_id]=1'], true) ?>"><?php echo AppHelper::totalPharmacyOrderStatus(2); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">Ready for Pickup</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/pharmacy-order/orders?OrdersSearch[status_id]=2'], true) ?>"><?php echo AppHelper::totalPharmacyOrderStatus(2); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">Delivered by Driver</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/pharmacy-order/orders?OrdersSearch[status_id]=4'], true) ?>"><?php echo AppHelper::totalPharmacyOrderStatus(2); ?></a>
                                </h2>
                                <br>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- -->

    <div class="row" hidden>
            <div class="col-md-12">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>Order Statistics - By Date</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-graph1 fa-4x"></i>
                        </div>
                        <div class="clearfix"></div>
                        <div class="m-t-xs">

                            <div class="row">

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">Today</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($todayOrders, $url . "OrdersSearch[today]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Week</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisWeekOrders, $url . "OrdersSearch[week]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Month</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisMonthOrders, $url . "OrdersSearch[month]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Year</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisYearOrders, $url . "OrdersSearch[year]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">Lifetime</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($totalOrders, $url . "OrdersSearch[exclude_cancel_order]=1"); ?>
                                    </h2>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    <?php
    $weekStart = date("Y-m-d", strtotime('monday this week'));
    $weekStart = date("Y-m-d", strtotime($weekStart . "-1 days"));
    $weekStartDate = date("d-M-y", strtotime($weekStart));
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Sales Statistics</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-cash fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12 text-center">
                                <small class="stat-label">Today Pharmacy Earnings (KWD)</small>
                                <h1 class="font-extra-bold">

                                    <?php echo Html::a(number_format(($todaySalesStatistics['total_order_amount'] - $todaySalesStatistics['commission']), 3), $url . 'OrdersSearch[date_range]=' . date('d-M-y') . '+to+' . date('d-M-y') . ''); ?></h1>
                            </div>
                            
                            <div class="col-md-3 col-sm-12 col-xs-12 text-center">
                                <small class="stat-label">Weekly Pharmacy Earnings (KWD)</small>
                                <h1 class="font-extra-bold"><?php echo Html::a(number_format(($weeklySalesStatistics['total_order_amount'] - $todaySalesStatistics['commission']), 3), $url . 'OrdersSearch[date_range]=' . $weekStartDate . '+to+' . date('d-M-y') . ''); ?></h1>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center">
                                <small class="stat-label">Total Pharmacy Earnings (KWD)</small>
                                <h1 class="font-extra-bold">
                                    <?php echo Html::a(number_format(($salesStatistics['total'] - $salesStatistics['commission']), 3), $url); ?>
                                </h1>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php 
$this->registerJs("
Highcharts.chart('bar_chart2', {
    chart: {
        type: 'column'
    },
    title: {
        text: ''
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        min: 0,
        title: {
            text: 'ORDER STATISTICS - BY DATE'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Order',
        data: [
            ['Today', ".$todayOrders."],
            ['This Week', ".$thisWeekOrders."],
            ['This Month', ".$thisMonthOrders."],
            ['This Year', ".$thisYearOrders."],
            ['Lifetime', ".$totalOrders."],
        ]
    }]
});

Highcharts.chart('bar_chart5', {
    chart: {
        type: 'column'
    },
    title: {
        text: ''
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        min: 0,
        title: {
            text: 'ORDER BY STATUS'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Orders',
        data: [
            ['Accepted', ".AppHelper::totalPharmacyOrderStatus(1)."],
            ['Ready for Pickup', ".AppHelper::totalPharmacyOrderStatus(2)."],
            ['Delivered by Driver', ".AppHelper::totalPharmacyOrderStatus(4)."]
        ]
    }]
}); 
", \yii\web\View::POS_END);
?>