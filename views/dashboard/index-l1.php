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

$url = \yii\helpers\BaseUrl::home() . "lab-appointment/index?";
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="animate-panel">

    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Welcome to Eyadat Lab panel.
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                <small class="stat-label" title="Total ">Upcoming</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/lab-appointment/index?LabAppointmentSearch[atype]=U'], true) ?>"><?php echo AppHelper::totalLabAppointment('U'); ?></a>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right"
                                 title="Total Active " style="">
                                <small class="stat-label" title=""> Completed </small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/lab-appointment/index?LabAppointmentSearch[atype]=C'], true) ?>"><?php echo AppHelper::totalLabAppointment('C'); ?></a>
                                </h2>
                                <br>
                            </div>

                            

                            <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total"
                                 style="">
                                <small class="stat-label" title="Total ">Expired</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/lab-appointment/index?LabAppointmentSearch[atype]=F'], true) ?>"><?php echo AppHelper::totalLabAppointment('F'); ?></a>
                                </h2>
                                <br>
                            </div>
                            <!--<div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right"
                                 title="Total Inactive ">
                                <small class="stat-label"> Total</small>
                                <h2 class="font-extra-bold"><a
                                        href="<?php echo \yii\helpers\Url::to(['/lab-appointment/index'], true) ?>"><?php echo AppHelper::totalLabAppointment(); ?></a>
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

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">Today</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($todayLabAppointments, $url . "LabAppointmentSearch[today]=1&LabAppointmentSearch[atype]=C"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Week</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisWeekLabAppointments, $url . "LabAppointmentSearch[week]=1&LabAppointmentSearch[atype]=C"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Month</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisMonthLabAppointments, $url . "LabAppointmentSearch[month]=1&LabAppointmentSearch[atype]=C"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">This Year</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($thisYearLabAppointments, $url . "LabAppointmentSearch[year]=1&LabAppointmentSearch[atype]=C"); ?>
                                    </h2>
                                    <br>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12 text-center row-float-right" title="Total "
                                 style="">
                                    <small class="stat-label" title="Total ">Lifetime</small>
                                    <h2 class="font-extra-bold">
                                        <?php echo Html::a($totalLabAppointments, $url . "LabAppointmentSearch[atype]=C"); ?>
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
            ['Today', ".$todayLabAppointments."],
            ['This Week', ".$thisWeekLabAppointments."],
            ['This Month', ".$thisMonthLabAppointments."],
            ['This Year', ".$thisYearLabAppointments."],
            ['Lifetime', ".$totalLabAppointments."],
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
            ['Scheduled', ".AppHelper::totalLabAppointment('U')."],
            ['Completed', ".AppHelper::totalLabAppointment('C')."],
            ['No Show', ".AppHelper::totalLabAppointment('N')."],
            ['Failed', ".AppHelper::totalLabAppointment('F')."],
        ]
    }]
});

", \yii\web\View::POS_END);
?>