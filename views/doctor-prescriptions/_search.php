<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorPrescriptionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doctor-prescriptions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'doctor_appointment_prescription_id') ?>

    <?= $form->field($model, 'doctor_appointment_id') ?>

    <?= $form->field($model, 'total_usage') ?>

    <?= $form->field($model, 'referred_pharmacy_id') ?>

    <?= $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
