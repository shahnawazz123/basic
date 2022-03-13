<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\Manufacturers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manufacturers-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">


        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>

        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $form->field($model, 'image_name')->hiddenInput()->label(false); ?>
            <label>
                Image 500x500
            </label>
            <br/>
            <?php
            echo FileUpload::widget([
                'name' => 'Manufacturers[image]',
                'url' => [
                    'upload/common?attribute=Manufacturers[image]'
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
                                            
                                            var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-manufacturers-image_name input[type=hidden]").val(data.result.files.name);
                                            $("#progress .progress-bar").attr("style","width: 0%;");
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
                    if ($model->image_name != "") {
                        ?>
                        <br/><img src="<?php echo \app\helpers\AppHelper::getUploadUrl() . $model->image_name ?>" alt="img" style="width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    
     <br/>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
