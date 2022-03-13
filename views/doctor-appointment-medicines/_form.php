<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorAppointmentMedicines */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doctor-appointment-medicines-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'doctor_appointment_prescription_id')->textInput() ?>

<?= $form->field($model, 'qty')->textInput() ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'product_id')->textInput() ?>

<?= $form->field($model, 'instruction')->textarea(['rows' => 6]) ?>

        </div>
        
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
