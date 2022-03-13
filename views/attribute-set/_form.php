<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\AppHelper;
use yii\helpers\BaseUrl;
use dosamigos\fileupload\FileUpload;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\AttributeSets */
/* @var $form yii\widgets\ActiveForm */
if (!$model->isNewRecord) {
    $attributeId = [];
    foreach ($model->attributeSetGroups as $attr) {
        array_push($attributeId, $attr->attribute_id);
    }
    $model->attributes_id = $attributeId;
}

if ($model->isNewRecord || (!$model->isNewRecord && $model->has_size_guide == 0)) {
    ?>
    <style>
        .hide-size-guides { display: none;}
    </style>
    <?php
}
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="attribute-sets-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
            <?=
            $form->field($model, 'attributes_id')->dropDownList(AppHelper::getAttributeList(), [
                'multiple' => 'multiple',
                //'prompt' => 'Please Select',
                'class' => 'select3 form-control',
            ])
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true, 'dir' => 'rtl']) ?>
            <?= $form->field($model, 'has_size_guide')->checkbox(['value' => 1, 'style' => 'margin-top: 23px;']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 hide-size-guides">
            <label>
                Size Guide Image in English
            </label>
            <br/>

            <?php
            echo FileUpload::widget([
                'name' => 'AttributeSets[size_guide_image_en]',
                'url' => [
                    'upload/common?attribute=AttributeSets[size_guide_image_en]'
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
                                    $(".field-attributesets-size_guide_image_en input[type=hidden]").val(data.result.files.name);$("#progress .progress-bar").attr("style","width: 0%;");
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
                    if ($model->size_guide_image_en != "") {
                        ?>
                        <br/><img src="<?php echo \app\helpers\AppHelper::getUploadUrl() . $model->size_guide_image_en ?>" alt="img" style="width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>
            <?php echo $form->field($model, 'size_guide_image_en')->hiddenInput()->label(false); ?>
        </div>
        <div class="col-md-6 hide-size-guides">

            <label>
                Size Guide Image in Arabic
            </label>
            <br/>

            <?php
            echo \dosamigos\fileupload\FileUpload::widget([
                'name' => 'AttributeSets[size_guide_image_ar]',
                'url' => [
                    'upload/common?attribute=AttributeSets[size_guide_image_ar]'
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
                                $('#progress2').show();
                                $('#progress2 .progress-bar').css(
                                    'width',
                                    progress + '%'
                                );
                             }",
                    'fileuploaddone' => 'function (e, data) {
                                if(data.result.files.error==""){

                                    var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                    $("#logo_preview2").html(img);
                                    $(".field-attributesets-size_guide_image_ar input[type=hidden]").val(data.result.files.name);$("#progress2 .progress-bar").attr("style","width: 0%;");
                                    $("#progress2").hide();
                                }
                                else{
                                   $("#progress2 .progress-bar").attr("style","width: 0%;");
                                   $("#progress2").hide();
                                   var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                   $("#logo_preview2").html(errorHtm);
                                   setTimeout(function(){
                                       $("#logo_preview2 span").remove();
                                   },3000)
                                }
                            }',
                ],
            ]);
            ?>

            <div id="progress2" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="logo_preview2">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->size_guide_image_ar != "") {
                        ?>
                        <br/><img src="<?php echo \app\helpers\AppHelper::getUploadUrl() . $model->size_guide_image_ar ?>" alt="img" style="width:256px;"/>
                        <?php
                    }
                }
                ?>
            </div>
            <?php echo $form->field($model, 'size_guide_image_ar')->hiddenInput()->label(false); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("$('.select3').select2({
        placeholder: \"Please Select\",
        maximumSelectionSize: 2
});", \yii\web\View::POS_END, 'select-picker');

$this->registerJs("
    $('#attributesets-has_size_guide').on('change', function() {
        if($(this).is(':checked')) {
            $('.hide-size-guides').show();
            common.addImageFileValidation('w0','attributesets-size_guide_image_file_en','AttributeSets[size_guide_image_file_en]','.field-attributesets-size_guide_image_file_en','Size Guide Image in English can\'t be blank');
            common.addImageFileValidation('w0','attributesets-size_guide_image_file_ar','AttributeSets[size_guide_image_file_ar]','.field-attributesets-size_guide_image_file_ar','Size Guide Image in Arabic can\'t be blank');
        }
        else {
            $('.hide-size-guides').hide();
            common.removeValidation('w0','attributesets-size_guide_image_file_en','.field-attributesets-size_guide_image_file_en');
            common.removeValidation('w0','attributesets-size_guide_image_file_ar','.field-attributesets-size_guide_image_file_ar');
        }
    });
", \yii\web\View::POS_END);
