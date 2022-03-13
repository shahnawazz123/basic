<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $model app\models\Stores */
/* @var $form yii\widgets\ActiveForm */
app\assets\DataTableAsset::register($this);
\app\assets\SelectAsset::register($this);
?>

<div class="stores-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

            
            <?php echo $form->field($model, 'flag')->hiddenInput()->label(false); ?>

            <?=
            $form->field($model, 'currency_id')->dropDownList(\app\helpers\AppHelper::getCurrencyList(), [
                'prompt' => 'Please select',
                'class' => 'select2 form-control'
            ])
            ?>

            <label>
                Flag File
            </label>
            <br/>
            <?php
            echo FileUpload::widget([
                'name' => 'Stores[flag]',
                'url' => [
                    'upload/common?attribute=Stores[flag]'
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
                                            $("#image_preview").html(img);
                                            $(".field-stores-flag input[type=hidden]").val(data.result.files.name);$("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                        }
                                        else{
                                           $("#progress .progress-bar").attr("style","width: 0%;");
                                           $("#progress").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#image_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#image_preview span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>
            <div id="progress" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="image_preview">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->flag != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->flag ?>" alt="img" style="width:256px;"/>
                        <?php
                    }
                }
                ?><br><br>
            </div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <div style="margin-top: 46px;">
                <?= $form->field($model, 'is_default')->checkbox(['value' => 1]) ?>
            </div>
        </div>
    </div>

    <div class="form-group"><br><br>
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>