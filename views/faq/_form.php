<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
app\assets\CmsEditorAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Faq */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .field-faq-answer_ar .note-editing-area{
        direction: rtl;
    }
</style>
<div class="faq-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'question_en')->textarea() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'question_ar')->textarea(['dir' => 'rtl']) ?>
        </div>
        <div class="clearfix">
        <div class="col-md-6">
            <?= $form->field($model, 'answer_en')->textarea(['rows'=>6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'answer_ar')->textarea(['rows'=>6,'dir' => 'rtl']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>