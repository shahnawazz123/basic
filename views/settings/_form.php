<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\Settings */
/* @var $form yii\widgets\ActiveForm */

use dosamigos\fileupload\FileUpload;

?>
 
<div class="settings-form">
    <p class="pull-right">
        <a href="<?= \yii\helpers\BaseUrl::home(); ?>settings/clear-cache" class="btn btn-danger">Clear Cache</a>
    </p>
    <div class="clearfix"></div>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'contact_email')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'support_email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-6">
            <?= $form->field($model, 'support_phone')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'buffer_quantity')->textInput(['class'=>'form-control isNumber']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'translator_price')->textInput(['class'=>'form-control isNumber']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <label>
                Physical Consultation (300 X 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[physical_consultation_image]',
                'url' => [
                    'upload/common?attribute=settings[physical_consultation_image]&settings[width'
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
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-settings-physical_consultation_image input[type=hidden]").val(data.result.files.name);
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
                    if ($model->physical_consultation_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->physical_consultation_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'physical_consultation_image')->hiddenInput()->label(false); ?>
        </div>

        <div class="col-md-6">
            <label>
                Online Consultation (300 X 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[online_consultation_image]',
                'url' => [
                    'upload/common?attribute=settings[online_consultation_image]'
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
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview_ar").html(img);
                                            $(".field-settings-online_consultation_image input[type=hidden]").val(data.result.files.name);
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
                    if ($model->online_consultation_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->online_consultation_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'online_consultation_image')->hiddenInput()->label(false); ?>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-6">
            <label>
                Lab Test Image (300 x 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[lab_test_image]',
                'url' => [
                    'upload/common?attribute=settings[lab_test_image]'
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
                                        $('#progress-lab_test_image').show();
                                        $('#progress-lab_test_image .progress-bar').css(
                                            'width',
                                            progress + '%'
                                    );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview-lab_test_image").html(img);
                                            $(".field-settings-lab_test_image input[type=hidden]").val(data.result.files.name);
                                            $("#progress-lab_test_image .progress-bar").attr("style","width: 0%;");
                                            $("#progress-lab_test_image").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress-lab_test_image .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress-lab_test_image .progress-bar").attr("style","width: 0%;");
                                           $("#progress-lab_test_image").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview-lab_test_image").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview-lab_test_image span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>


            <div id="progress-lab_test_image" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview-lab_test_image">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->lab_test_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->lab_test_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'lab_test_image')->hiddenInput()->label(false); ?>
        </div>

        <div class="col-md-6">
            <label>
                Pharmacies Image (300 x 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[pharmacies_image]',
                'url' => [
                    'upload/common?attribute=settings[pharmacies_image]'
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
                                        $('#progress-pharmacies_image').show();
                                        $('#progress-pharmacies_image .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview-pharmacies_image").html(img);
                                            $(".field-settings-pharmacies_image input[type=hidden]").val(data.result.files.name);
                                            $("#progress-pharmacies_image .progress-bar").attr("style","width: 0%;");
                                            $("#progress-pharmacies_image").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress-pharmacies_image .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress-pharmacies_image .progress-bar").attr("style","width: 0%;");
                                           $("#progress-pharmacies_image").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview-pharmacies_image").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview-pharmacies_image span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>

            <div id="progress-pharmacies_image" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview-pharmacies_image">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->pharmacies_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->pharmacies_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'pharmacies_image')->hiddenInput()->label(false); ?>
        </div>
        <div class="clearfix"></div>

        

        <div class="col-md-6">
            <label>
                Beauty Clinic Image (300 x 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[beauty_clinic_image]',
                'url' => [
                    'upload/common?attribute=settings[beauty_clinic_image]'
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
                                        $('#progress-beauty_clinic_image').show();
                                        $('#progress-beauty_clinic_image .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview-beauty_clinic_image").html(img);
                                            $(".field-settings-beauty_clinic_image input[type=hidden]").val(data.result.files.name);
                                            $("#progress-beauty_clinic_image .progress-bar").attr("style","width: 0%;");
                                            $("#progress-beauty_clinic_image").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress-beauty_clinic_image .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress-beauty_clinic_image .progress-bar").attr("style","width: 0%;");
                                           $("#progress-beauty_clinic_image").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview-beauty_clinic_image").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview-beauty_clinic_image span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>

            <div id="progress-beauty_clinic_image" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview-beauty_clinic_image">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->beauty_clinic_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->beauty_clinic_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'beauty_clinic_image')->hiddenInput()->label(false); ?>
        </div>



        <div class="col-md-6">
            <label>
                Hospital Image (300 x 300)
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'settings[hospital_image]',
                'url' => [
                    'upload/common?attribute=settings[hospital_image]'
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
                                        $('#progress-hospital_image').show();
                                        $('#progress-hospital_image .progress-bar').css(
                                            'width',
                                            progress + '%'
                                    );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img id="settingsImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview-hospital_image").html(img);
                                            $(".field-settings-hospital_image input[type=hidden]").val(data.result.files.name);
                                            $("#progress-hospital_image .progress-bar").attr("style","width: 0%;");
                                            $("#progress-hospital_image").hide();
                                            //var ratio = 1920/680;
                                            //var targetWidth = 96  * ratio;
                                            //var targetHeight = 34 * ratio;
                                            
                                            $("#progress-hospital_image .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress-hospital_image .progress-bar").attr("style","width: 0%;");
                                           $("#progress-hospital_image").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#logo_preview-hospital_image").html(errorHtm);
                                           setTimeout(function(){
                                               $("#logo_preview-hospital_image span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>


            <div id="progress-hospital_image" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview-hospital_image">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->hospital_image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->hospital_image ?>" alt="img" style="max-width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'hospital_image')->hiddenInput()->label(false); ?>
        </div>
        <div class="clearfix"></div>

    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
