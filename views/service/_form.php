<?php

use yii\helpers\Html;
use yii\helpers\BaseUrl;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;


$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


/* @var $this yii\web\View */
/* @var $model app\models\Services */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="services-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <label>
                Image in English (500*500)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'Services[image_en]',
                'url' => [
                    'upload/common?attribute=Services[image_en]'
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
                                            
                                            var img = \'<br/><img id="bannerImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-services-image_en input[type=hidden]").val(data.result.files.name);
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress .progress-bar").attr("style","width: 0%;");
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
                    if ($model->image_en != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image_en ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'image_en')->hiddenInput()->label(false); ?>
        </div>

        <div class="col-md-6">
            <label>
                Image in Arabic (500*500)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'Services[image_ar]',
                'url' => [
                    'upload/common?attribute=Services[image_ar]'
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
                                        $('#progress_ar').show();
                                        $('#progress_ar .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="bannerImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview_ar").html(img);
                                            $(".field-services-image_ar input[type=hidden]").val(data.result.files.name);
                                            $("#progress_ar .progress-bar").attr("style","width: 0%;");
                                            $("#progress_ar").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress_ar .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress_ar .progress-bar").attr("style","width: 0%;");
                                           $("#progress_ar").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview_ar").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview_ar span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>

            <div id="progress_ar" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview_ar">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->image_ar != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image_ar ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'image_ar')->hiddenInput()->label(false); ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
