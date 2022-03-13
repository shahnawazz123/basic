<?php

use yii\helpers\Html;
use yii\helpers\BaseUrl;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;
/* @var $this yii\web\View */
/* @var $model app\models\Symptoms */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>


<div class="symptoms-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true,'dir' => 'rtl']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <label>
                Icon (300 X 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'Symptoms[image]',
                'url' => [
                    'upload/common?attribute=Symptoms[image]'
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
                                            $(".field-symptoms-image input[type=hidden]").val(data.result.files.name);
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
                    if ($model->image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
