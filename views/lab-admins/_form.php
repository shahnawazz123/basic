<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\LabAdmins */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lab-admins-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'lab_id')->dropDownList(AppHelper::getLabsList(), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true, 'dir' => 'rtl']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true, 'value' => null]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>