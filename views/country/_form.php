<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
/* @var $this yii\web\View */
/* @var $model app\models\Country */
/* @var $form yii\widgets\ActiveForm */ 
?>

<div class="country-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6"> 
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'nicename')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'iso')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phonecode')->textInput(['maxlength' => true]) ?>

            <!-- <?= $form->field($model, 'shipping_cost')->textInput(['maxlength' => true]) ?> -->
            
            <?php // echo $form->field($model, 'standard_shipping_cost_actual')->textInput() ?>
            
            <?php //echo $form->field($model, 'delivery_interval')->textInput(['maxlength' => true]) ?>

            <?php //echo $form->field($model, 'standard_delivery_items')->textInput() ?>
            
            <?php //echo $form->field($model, 'express_delivery_items')->textInput() ?>

            <!-- <?= $form->field($model, 'vat')->textInput(['maxlength' => true]) ?> -->



            <!-- <?= $form->field($model, 'cod_cost')->textInput(['maxlength' => true]) ?> -->
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'iso3')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'numcode')->textInput(['maxlength' => true]) ?>

            <?php //echo $form->field($model, 'express_shipping_cost')->textInput(['maxlength' => true]) ?>
            
            <?php //echo $form->field($model, 'express_shipping_cost_actual')->textInput() ?>
            
            <?php //echo $form->field($model, 'express_delivery_interval')->textInput(['maxlength' => true]) ?>
            
            <?php //echo $form->field($model, 'free_delivery_limit')->textInput(['maxlength' => true]) ?>

            

            <?php //echo $form->field($model, 'standard_delivery_charge')->textInput() ?>

            <?php //echo $form->field($model, 'express_delivery_charge')->textInput() ?>
            
            
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">

            <label>
                Flag (500 X 500)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'Country[flag]',
                'url' => [
                    'upload/common?attribute=Country[flag]'
                ],
                'options' => [
                    'accept' => 'image/*',
                ],
                'clientOptions' => [
                    'dataType' => 'json',
                    'maxFileSize' => 2000000,
                ],
                'clientEvents' => [
                    'fileuploadprogressall' => "function (e, data) {
                                        var progress = parseInt(data.loaded / data.total * 100, 10);
                                        $('#progress').show();
                                        $('#progress .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:128px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-country-flag input[type=hidden]").val(data.result.files.name);$("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                        }
                                        else{
                                           $("#progress .progress-bar").attr("style","width: 0%;");
                                           $("#progress").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>

            <div id="progress" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->flag != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->flag ?>" alt="img" style="width:128px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'flag')->hiddenInput()->label(false); ?>
            
        </div>
    </div>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
