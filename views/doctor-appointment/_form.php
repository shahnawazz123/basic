<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorAppointments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doctor-appointments-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">


        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'consultation_fees')->textInput() ?>

            <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(\app\models\Users::find()->where(['is_deleted' => 0])->orderBy(['user_id' => SORT_DESC])->all(), "user_id", "user_id"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'prescription_file')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'created_at')->textInput() ?>

            <?= $form->field($model, 'kid_id')->dropDownList(ArrayHelper::map(\app\models\Kids::find()->where(['is_deleted' => 0])->orderBy(['name_en' => SORT_DESC])->all(), "kid_id", "name_en"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'consultation_type')->dropDownList(['V' => 'Video Consultation', 'I' => 'In-person Consultation',], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'appointment_datetime')->textInput() ?>

            <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map(\app\models\Doctors::find()->where(['is_deleted' => 0])->orderBy(['name_en' => SORT_DESC])->all(), "doctor_id", "name_en"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'is_deleted')->textInput() ?>

            <?= $form->field($model, 'updated_at')->textInput() ?>

        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
