<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorPrescriptions */

$this->title = 'Create Doctor Prescriptions';
$this->params['breadcrumbs'][] = ['label' => 'Doctor Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <?= $this->render('_form', [
                'model' => $model,
                'model1' => $model1,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                ]) ?>

            </div>
        </div>
    </div>
</div>
