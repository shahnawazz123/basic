<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DoctorPrescriptionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Doctor Prescriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Doctor Prescriptions', ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                                                        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                                    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                                'doctor_appointment_id',
            'total_usage',
            'referred_pharmacy_id',
            'is_deleted',
            'is_active',
            //'created_at',

                    ['class' => 'yii\grid\ActionColumn'],
                    ],
                    ]); ?>
                
                
            </div>
        </div>
    </div>
</div>
