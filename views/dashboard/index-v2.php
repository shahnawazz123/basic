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

$doctor_url = \yii\helpers\BaseUrl::home() . "doctor-appointment/index?";
$lab_url = \yii\helpers\BaseUrl::home() . "doctor-appointment/index?";
$url = \yii\helpers\BaseUrl::home() . "order/index?";
?>
<?php
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style>
    /*.hpanel .panel-body {
  color: #fff;
  background: rgb(96,186,209);
  border: 1px solid #e4e5e7;
  border-radius: 2px;
  padding: 20px;
  position: relative;
}
.font-extra-bold a{
    color: #fff !important;
}
.content{background: #fff !important;}*/
</style>
<div class="animate-panel">

    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Welcome to Eyadat administrator panel.
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>DOCTOR APPOINTMENT STATISTICS - BY DATE</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="doctor_bar_chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>LAB APPOINTMENT STATISTICS - BY DATE</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bar_chart1"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
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
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>DOCTOR APPOINTMENT BY STATUS</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bar_chart3"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>LAB APPOINTMENT BY STATUS</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div id="bar_chart4"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
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
        <div class="col-md-12">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Doctor Appointment Statistics - By Date</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($todayAppointments, $doctor_url . "DoctorAppointmentSearch[today]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisWeekAppointments, $doctor_url . "DoctorAppointmentSearch[week]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisMonthAppointments, $doctor_url . "DoctorAppointmentSearch[month]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisYearAppointments, $doctor_url . "DoctorAppointmentSearch[year]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Lifetime</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($totalAppointments, $doctor_url . "DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" hidden>
        <div class="col-md-12">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Lab Appointment Statistics - By Date</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($todayLabAppointments, $lab_url . "LabAppointmentSearch[today]=1&LabAppointmentSearch[atype]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisWeekLabAppointments, $lab_url . "LabAppointmentSearch[week]=1&LabAppointmentSearch[atype]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisMonthLabAppointments, $lab_url . "LabAppointmentSearch[month]=1&LabAppointmentSearch[atype]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisYearLabAppointments, $lab_url . "LabAppointmentSearch[year]=1&LabAppointmentSearch[atype]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Lifetime</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($totalLabAppointments, $lab_url . "LabAppointmentSearch[atype]=C"); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($todayOrders, $url . "OrdersSearch[today]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisWeekOrders, $url . "OrdersSearch[week]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisMonthOrders, $url . "OrdersSearch[month]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisYearOrders, $url . "OrdersSearch[year]=1&OrdersSearch[exclude_cancel_order]=1"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
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

    <div class="row" hidden>
        <div class="col-md-12" style="">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Appointments">Orders By Status</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Pending</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=1'], true) ?>"><?php echo AppHelper::totalOrderStatus(1); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Accepted</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=2'], true) ?>"><?php echo AppHelper::totalOrderStatus(2); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">In Progress</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=3'], true) ?>"><?php echo AppHelper::totalOrderStatus(3); ?></a>
                                </h2>
                                <br>
                            </div>

                            <!-- <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">Ready for Delivery</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=7'], true) ?>"><?php echo AppHelper::totalOrderStatus(7); ?></a>
                                </h2>
                                <br>
                            </div> -->

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Out for Delivery</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=4'], true) ?>"><?php echo AppHelper::totalOrderStatus(4); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Delivery Order</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=5'], true) ?>"><?php echo AppHelper::totalOrderStatus(5); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Cancelled Order</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/order/index?OrdersSearch[status_id]=6'], true) ?>"><?php echo AppHelper::totalOrderStatus(6); ?></a>
                                </h2>
                                <br>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- -->

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Clinic / Hospital Earning In KWD</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_today_earn = ($todayClinicEarn['total_amount'] != "") ? number_format($todayClinicEarn['total_amount'] - $todayClinicEarn['total_commission'], 3) : '0.000';
                                    echo Html::a($total_today_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_earn = ($weekClinicEarn['total_amount'] != "") ? number_format($weekClinicEarn['total_amount'] - $weekClinicEarn['total_commission'], 3) : '0.000';
                                    echo Html::a($total_week_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_earn = ($monthClinicEarn['total_amount'] != "") ? number_format($monthClinicEarn['total_amount'] - $monthClinicEarn['total_commission'], 3) : '0.000';
                                    echo Html::a($total_month_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_earn = ($yearClinicEarn['total_amount'] != "") ? number_format($yearClinicEarn['total_amount'] - $yearClinicEarn['total_commission'], 3) : '0.000';
                                    echo Html::a($total_year_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Admin Earning In KWD (Clinic / Hospital)</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php

                                    $total_today_earn = ($todayClinicEarn['total_commission'] != "") ? number_format($todayClinicEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_today_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_earn = ($weekClinicEarn['total_commission'] != "") ? number_format($weekClinicEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_week_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_earn = ($monthClinicEarn['total_commission'] != "") ?  number_format($monthClinicEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_month_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_earn = ($yearClinicEarn['total_commission'] != "") ? number_format($yearClinicEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_year_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Lab Earning In KWD</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_today_lab_earn = ($todayLabEarn['total_amount'] != "") ? number_format($todayLabEarn['total_amount'] - $todayLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_today_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_lab_earn = ($weekLabEarn['total_amount'] != "") ? number_format($weekLabEarn['total_amount'] - $weekLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_week_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_lab_earn = ($monthLabEarn['total_amount'] != "") ? number_format($monthLabEarn['total_amount'] - $monthLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_month_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_lab_earn = ($yearLabEarn['total_amount'] != "") ? number_format($yearLabEarn['total_amount'] - $yearLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_year_lab_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Admin Earning In KWD (Lab)</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php

                                    $total_today_lab_earn = ($todayLabEarn['total_commission'] != "") ? number_format($todayLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_today_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_lab_earn = ($weekLabEarn['total_commission'] != "") ? number_format($weekLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_week_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_lab_earn = ($monthLabEarn['total_commission'] != "") ?  number_format($monthLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_month_lab_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_lab_earn = ($yearLabEarn['total_commission'] != "") ? number_format($yearLabEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_year_lab_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Pharmacy Earning In KWD</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_today_pharmacy_earn = ($todayPharmacyEarn['total_amount'] != "") ? number_format($todayPharmacyEarn['total_amount'], 3) : '0.00';
                                    echo Html::a($total_today_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_pharmacy_earn = ($weekPharmacyEarn['total_amount'] != "") ? number_format($weekPharmacyEarn['total_amount'], 3) : '0.00';
                                    echo Html::a($total_week_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_pharmacy_earn = ($monthPharmacyEarn['total_amount'] != "") ? number_format($monthPharmacyEarn['total_amount'], 3) : '0.00';
                                    echo Html::a($total_month_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_pharmacy_earn = ($yearPharmacyEarn['total_amount'] != "") ? number_format($yearPharmacyEarn['total_amount'], 3) : '0.00';
                                    echo Html::a($total_year_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Admin Earning In KWD (Pharmacy)</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">Today</small>
                                <h2 class="font-extra-bold">
                                    <?php

                                    $total_today_pharmacy_earn = ($todayPharmacyEarn['total_commission'] != "") ? number_format($todayPharmacyEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_today_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_week_pharmacy_earn = ($weekPharmacyEarn['total_commission'] != "") ? number_format($weekPharmacyEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_week_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_month_pharmacy_earn = ($monthPharmacyEarn['total_commission'] != "") ?  number_format($monthPharmacyEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_month_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-Pharmacyel" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php
                                    $total_year_pharmacy_earn = ($yearPharmacyEarn['total_commission'] != "") ? number_format($yearPharmacyEarn['total_commission'], 3) : '0.00';
                                    echo Html::a($total_year_pharmacy_earn); ?>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Clinics">Clinics</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Clinics" style="">
                                <small class="stat-label" title="Total Clinics">Total Clinics</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/clinic/index'], true) ?>"><?php echo $clinicCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Clinics" style="">
                                <small class="stat-label" title="Total Active Clinics">Active Clinics</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/clinic/index?ClinicsSearch[is_active]=1'], true) ?>"><?php echo $clinicActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive Clinics" style="">
                                <small class="stat-label" title="Total InActive Gyms"> Inactive Clinics</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/clinic/index?ClinicsSearch[is_active]=0'], true) ?>"><?php echo $clinicInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Clinics">Hospital</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Clinics" style="">
                                <small class="stat-label" title="Total Clinics">Total Hospital</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/hospital/index'], true) ?>"><?php echo $hospitalCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Hospital" style="">
                                <small class="stat-label" title="Total Active Hospital">Active Hospital</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/hospital/index?ClinicsSearch[is_active]=1'], true) ?>"><?php echo $hospitalActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive" style="">
                                <small class="stat-label" title="Total InActive Gyms"> Inactive Hospital</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/hospital/index?ClinicsSearch[is_active]=0'], true) ?>"><?php echo $hospitalInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Doctors">Doctors</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Doctors" style="">
                                <small class="stat-label" title="Total Doctors">Total Doctors</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor/index'], true) ?>"><?php echo $doctorCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Doctors" style="">
                                <small class="stat-label" title="Total Active Doctors">Active Doctors</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor/index?DoctorsSearch[is_active]=1'], true) ?>"><?php echo $doctorActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive Doctors" style="">
                                <small class="stat-label" title="Total InActive Doctors"> Inactive Doctors</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor/index?DoctorsSearch[is_active]=0'], true) ?>"><?php echo $doctorInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Labs">Labs</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Labs" style="">
                                <small class="stat-label" title="Total Labs">Total Labs</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/lab/index'], true) ?>"><?php echo $labCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Labs" style="">
                                <small class="stat-label" title="Total Active Labs">Active Labs</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/lab/index?LabsSearch[is_active]=1'], true) ?>"><?php echo $labActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive Labs" style="">
                                <small class="stat-label" title="Total InActive Labs"> Inactive Labs</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/lab/index?LabsSearch[is_active]=0'], true) ?>"><?php echo $labInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- doctor-->

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Tests">Tests</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Tests" style="">
                                <small class="stat-label" title="Total Tests">Total Tests</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/tests/index'], true) ?>"><?php echo $testCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Tests" style="">
                                <small class="stat-label" title="Total Active Tests">Active Tests</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/tests/index?TestsSearch[is_active]=1'], true) ?>"><?php echo $testActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive Tests" style="">
                                <small class="stat-label" title="Total InActive Tests"> Inactive Tests</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/tests/index?TestsSearch[is_active]=0'], true) ?>"><?php echo $testInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Pharmacy">Pharmacy</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Pharmacy" style="">
                                <small class="stat-label" title="Total Pharmacy">Total Pharmacy</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/pharmacies/index'], true) ?>"><?php echo $pharmaciesCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active Pharmacy" style="">
                                <small class="stat-label" title="Total Active Pharmacy">Active Pharmacy</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/pharmacies/index?PharmaciesSearch[is_active]=1'], true) ?>"><?php echo $pharmaciesActive; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive Pharmacy" style="">
                                <small class="stat-label" title="Total InActive Pharmacy"> Inactive Pharmacy</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/pharmacies/index?PharmaciesSearch[is_active]=0'], true) ?>"><?php echo $pharmaciesInActive; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- doctor-->

    <div class="row">
        <div class="col-md-6">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Pharmacy">USERS</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total User" style="">
                                <small class="stat-label" title="Total User">Total User</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/user/index'], true) ?>"><?php echo $userCount; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active User" style="">
                                <small class="stat-label" title="Total Android">Total Android</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/user/index?PharmaciesSearch[is_active]=1'], true) ?>"><?php echo $userAndroid; ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-4 col-sm-12 col-xs-12 text-center row-float-right" title="Total Inactive User" style="">
                                <small class="stat-label" title="Total iOs"> Total iOs</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/user/index?PharmaciesSearch[is_active]=0'], true) ?>"><?php echo $userIos; ?></a>
                                </h2>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Pharmacy-->



    <div class="row list">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body list">
                    <div class="stats-title pull-left">
                        <h4>Top 10 selling products</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-cash fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sales Price</th>
                                    <th>Commission</th>
                                    <!-- <th>Cost Price</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($topSellingProducts as $product) {
                                    if (!empty($product['name'])) {
                                ?>
                                        <tr>
                                            <td><?php echo substr($product['name'], 0, 20) . " (" . $product['sku'] . ")"; ?></td>
                                            <td>KWD <?php echo number_format($product['total_amount'], 3); ?></td>
                                            <td>
                                                KWD <?php echo number_format($product['commission_amount'], 3); ?></td>
                                            <!-- <td>
                                            KWD <?php //echo number_format(($product['total_cost_amount']), 3); 
                                                ?></td> -->
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
<?php
$this->registerJs("
Highcharts.chart('doctor_bar_chart', {
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
            text: 'DOCTOR APPOINTMENT STATISTICS - BY DATE'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Doctor Appointments',
        data: [
            ['Today', " . $todayAppointments . "],
            ['This Week', " . $thisWeekAppointments . "],
            ['This Month', " . $thisMonthAppointments . "],
            ['This Year', " . $thisYearAppointments . "],
            ['Lifetime', " . $totalAppointments . "],
        ]
    }]
});

Highcharts.chart('bar_chart1', {
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
            text: 'LAB APPOINTMENT STATISTICS - BY DATE'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Lab Appointments',
        data: [
            ['Today', " . $todayLabAppointments . "],
            ['This Week', " . $thisWeekLabAppointments . "],
            ['This Month', " . $thisMonthLabAppointments . "],
            ['This Year', " . $thisYearLabAppointments . "],
            ['Lifetime', " . $totalLabAppointments . "],
        ]
    }]
});

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
            ['Today', " . $todayOrders . "],
            ['This Week', " . $thisWeekOrders . "],
            ['This Month', " . $thisMonthOrders . "],
            ['This Year', " . $thisYearOrders . "],
            ['Lifetime', " . $totalOrders . "],
        ]
    }]
});

Highcharts.chart('bar_chart3', {
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
            text: 'DOCTOR APPOINTMENT BY STATUS'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Doctor Appointments',
        data: [
            ['Scheduled', " . AppHelper::totalDoctorAppointment('U') . "],
            ['Completed', " . AppHelper::totalDoctorAppointment('C') . "],
            ['No Show', " . AppHelper::totalDoctorAppointment('N') . "],
            ['Failed', " . AppHelper::totalDoctorAppointment('F') . "],
        ]
    }]
});

Highcharts.chart('bar_chart4', {
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
            text: 'LAB APPOINTMENT BY STATUS'
        }
    },
    series: [{
        colorByPoint: true,
        name: 'Lab Appointments',
        data: [
            ['Scheduled', " . AppHelper::totalLabAppointment('U') . "],
            ['Completed', " . AppHelper::totalLabAppointment('C') . "],
            ['No Show', " . AppHelper::totalLabAppointment('N') . "],
            ['Failed', " . AppHelper::totalLabAppointment('F') . "],
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
            ['Pending', " . AppHelper::totalOrderStatus(1) . "],
            ['Accepted', " . AppHelper::totalOrderStatus(2) . "],
            ['In Progress', " . AppHelper::totalOrderStatus(3) . "],
            ['Ready for Delivery', " . AppHelper::totalOrderStatus(8) . "],
            ['Out for Delivery', " . AppHelper::totalOrderStatus(4) . "],
            ['Delivered', " . AppHelper::totalOrderStatus(5) . "],
            ['Cancelled', " . AppHelper::totalOrderStatus(6) . "],
        ]
    }]
}); 
", \yii\web\View::POS_END);
?>