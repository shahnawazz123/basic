<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\AppHelper;
/* @var $this yii\web\View */
/* @var $model app\models\State */
/* @var $form yii\widgets\ActiveForm */
\app\assets\SelectAsset::register($this);
?>

<div class="state-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'country_id')->dropDownList(AppHelper::getCountryList(),[
                'prompt' => 'Please select',
                'class' => 'form-control select2',
            ]) ?>            
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>

            <div style="margin-top: 45px;">
                <?= $form->field($model, 'is_active')->checkbox() ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>
