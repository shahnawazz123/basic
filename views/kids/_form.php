<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Kids */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kids-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        <div class="col-md-6">
            <?= $form->field($model, 'user_id')->dropDownList(AppHelper::getAllUser(), ['prompt' => 'Please Select','class' => 'form-control select2']) ?>
        </div> 
        <div class="clearfix"></div>   
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'civil_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dob')->textInput(['maxlength' => true]) ?>
        </div>
        
        
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
