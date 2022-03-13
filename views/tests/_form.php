<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $model app\models\Tests */
/* @var $form yii\widgets\ActiveForm */

\app\assets\SelectAsset::register($this);


$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>

<div class="tests-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'is_home_service')->checkbox() ?> 
        </div>
        <div class="clearfix"></div>
        <div class="col-lg-6">
            <?php 
                if (!$model->isNewRecord) {
                    $model->category_id = AppHelper::getSelectedCategoriesIds($model->test_id,'T');
                    //print_r($model->category_id);
                }
            ?>
            <?=
            $form->field($model, 'category_id')->dropDownList(AppHelper::getRecursiveCategory('T'), [
                
                'class' => 'form-control select2',
                'multiple'=>'multiple'
            ])
            ?>
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