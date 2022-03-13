<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\LabAppointments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lab-appointments-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">


        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(['H' => 'H', 'L' => 'L', '' => '',], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'paymode')->dropDownList(['C' => 'C', 'CC' => 'CC', 'K' => 'K'], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'sample_collection_time')->textInput() ?>

            <?= $form->field($model, 'prescription_file')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'created_at')->textInput() ?>

            <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(\app\models\Users::find()->where(['is_deleted' => 0])->orderBy(['user_id' => SORT_DESC])->all(), "user_id", "user_id"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'lab_id')->dropDownList(ArrayHelper::map(\app\models\Labs::find()->where(['is_deleted' => 0])->orderBy(['name_en' => SORT_DESC])->all(), "lab_id", "name_en"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?= $form->field($model, 'is_paid')->textInput() ?>

            <?= $form->field($model, 'lab_amount')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'sample_collection_address')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'is_deleted')->textInput() ?>

            <?= $form->field($model, 'updated_at')->textInput() ?>

            <?= $form->field($model, 'kid_id')->dropDownList(ArrayHelper::map(\app\models\Kids::find()->where(['is_deleted' => 0])->orderBy(['name_en' => SORT_DESC])->all(), "kid_id", "name_en"), ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
