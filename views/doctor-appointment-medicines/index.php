<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DoctorAppointmentMedicinesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Doctor Appointment Medicines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                

                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                                    <?= GridView::widget([
                                        'dataProvider' => $dataProvider,
                                        //'filterModel' => $searchModel,
                                        'columns' => [
                                                ['class' => 'yii\grid\SerialColumn'],

                                                //'doctor_appointment_prescription_id',
                                                [
                                                    'label'=>'Product',
                                                    'value'=>function($model)
                                                    {
                                                        return $model->product->name_en;
                                                    },
                                                ],
                                                'qty',
                                                'instruction:ntext',

                                                /*['class' => 'yii\grid\ActionColumn'],*/
                                                ],
                                                ]); ?>
                
                
            </div>
        </div>
    </div>
</div>
