<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LabAppointments */

$this->title = 'Update Lab Appointments: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lab Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->lab_appointment_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <?= $this->render('_form', [
                'model' => $model,
                ]) ?>

            </div>
        </div>
    </div>
</div>
