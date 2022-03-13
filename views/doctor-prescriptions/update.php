<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorPrescriptions */

$this->title = 'Update Doctor Prescriptions: ' . $model->doctor_appointment_prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->doctor_appointment_prescription_id, 'url' => ['view', 'id' => $model->doctor_appointment_prescription_id]];
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
