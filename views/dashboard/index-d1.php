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

$url = \yii\helpers\BaseUrl::home() . "doctor-appointment/index?";
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="animate-panel">

    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Welcome to Eyadat Doctor panel.
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
    </div>

    <div class="row" hidden>
        <div class="col-md-12" style="">
            <div class="hpanel stats">
                <div class="panel-body h-200">

                    <div class="stats-title float-right col-md-6">
                        <h4 title="Appointments">Appointments</h4>
                    </div>
                    <div class="stats-icon opposite-float-right col-md-6">
                        <i class="pull-right fa fa-list fa-4x"></i>
                    </div>

                    <div class="clearfix"></div>
                    <div class="m-t-xs">
                        <div class="row">
                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today Upcoming Video Appointments</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index?DoctorAppointmentSearch[type]=U&DoctorAppointmentSearch[consultation_type]=V'], true) ?>"><?php echo AppHelper::totalDoctorAppointment('U', 'V'); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Today Upcoming In Person Appointments</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index?DoctorAppointmentSearch[type]=U&DoctorAppointmentSearch[consultation_type]=I'], true) ?>"><?php echo AppHelper::totalDoctorAppointment('U', 'I'); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Total Upcoming</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index?DoctorAppointmentSearch[type]=U'], true) ?>"><?php echo AppHelper::totalDoctorAppointment('U'); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total Active " style="">
                                <small class="stat-label" title=""> Completed </small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index?DoctorAppointmentSearch[type]=C'], true) ?>"><?php echo AppHelper::totalDoctorAppointment('C'); ?></a>
                                </h2>
                                <br>
                            </div>



                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total" style="">
                                <small class="stat-label" title="Total ">Expired</small>
                                <h2 class="font-extra-bold"><a href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index?DoctorAppointmentSearch[type]=F'], true) ?>"><?php echo AppHelper::totalDoctorAppointment('F'); ?></a>
                                </h2>
                                <br>
                            </div>

                            <!-- <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right"
                                 title="Total Inactive ">
                                <small class="stat-label"> Total</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/doctor-appointment/index'], true) ?>"><?php echo AppHelper::totalDoctorAppointment(); ?></a>
                                </h2>
                                <br>
                            </div> -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div><!-- Clinic-->

    <div class="row" hidden>
        <div class="col-md-12">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Appointment Statistics - By Date</h4>
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
                                    <?php echo Html::a($todayAppointments, $url . "DoctorAppointmentSearch[today]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Week</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisWeekAppointments, $url . "DoctorAppointmentSearch[week]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Month</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisMonthAppointments, $url . "DoctorAppointmentSearch[month]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">This Year</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($thisYearAppointments, $url . "DoctorAppointmentSearch[year]=1&DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Lifetime</small>
                                <h2 class="font-extra-bold">
                                    <?php echo Html::a($totalAppointments, $url . "DoctorAppointmentSearch[type]=C"); ?>
                                </h2>
                                <br>
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

", \yii\web\View::POS_END);
?>