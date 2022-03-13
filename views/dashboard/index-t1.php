<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\models\Translator;
use yii\data\ActiveDataProvider;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->title = 'Translator';
$this->params['breadcrumbs'][] = $this->title;
// $url = \yii\helpers\BaseUrl::home() . "doctor-appointment/index?";
?>
<div class="animate-panel">
    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Welcome to Eyadat Translator panel.
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="hpanel stats">
                <div class="panel-body h-200">
                    <div class="stats-title pull-left">
                        <h4>Appointment Status</h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="pe-7s-graph1 fa-4x"></i>
                    </div>
                    <div class="clearfix"></div>
                    <div class="m-t-xs">

                        <div class="row">


                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Upcoming</small>
                                <h2 class="font-extra-bold">
                                    
                                    <?php echo $upcoming; ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Completed</small>
                                <h2 class="font-extra-bold">

                                    <?php echo $completed; ?>

                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">No Show</small>
                                <h2 class="font-extra-bold">
                                    <?php echo $notshow; ?>
                                </h2>
                                <br>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12 text-center row-float-right" title="Total " style="">
                                <small class="stat-label" title="Total ">Failed</small>
                                <h2 class="font-extra-bold">
                                    <?php echo $failed; ?>
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