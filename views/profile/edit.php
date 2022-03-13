<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use dosamigos\fileupload\FileUpload;

\app\assets\SelectAsset::register($this);

$this->title = Yii::t('app', 'Edit profile');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(BaseUrl::home() . 'js/profile.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<style>
    .form-horizontal .control-label{
        text-align: left;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                            'id' => 'profile-edit',
                            'options' => ['class' => 'form-horizontal'],
                            'fieldConfig' => [
                                'template' => "{label}\n<div class=\"col-lg-3\">
                            {input}</div>\n<div class=\"col-lg-5\">
                            {error}</div>",
                                'labelOptions' => ['class' => 'col-lg-2 control-label'],
                            ],
                ]);
                ?>

                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    echo $form->field($model, 'name', [
                        'inputOptions' => [
                            'placeholder' => 'Name',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name');

                    echo
                    $form->field($model, 'phone', [
                        'inputOptions' => [
                            'placeholder' => 'Phone',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Phone');
                } elseif (\Yii::$app->session['_eyadatAuth'] == 2) {
                    echo $form->field($model, 'name_en', [
                        'inputOptions' => [
                            'placeholder' => 'Name in English',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in English');

                    echo $form->field($model, 'name_ar', [
                        'inputOptions' => [
                            'placeholder' => 'Name in Arabic',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in Arabic');
                } elseif (\Yii::$app->session['_eyadatAuth'] == 4) {
                    echo $form->field($model, 'name_en', [
                        'inputOptions' => [
                            'placeholder' => 'Name in English',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in English');

                    echo $form->field($model, 'name_ar', [
                        'inputOptions' => [
                            'placeholder' => 'Name in Arabic',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in Arabic');
                }elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                    echo $form->field($model, 'name_en', [
                        'inputOptions' => [
                            'placeholder' => 'Name in English',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in English');

                    echo $form->field($model, 'name_ar', [
                        'inputOptions' => [
                            'placeholder' => 'Name in Arabic',
                            'class' => 'form-control'
                        ]
                    ])->textInput()->label('Name in Arabic');
                }
                ?>

                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1 || \Yii::$app->session['_eyadatAuth'] == 2) {
                    ?>

                    <label class="col-lg-2 control-label no-padding">
                        Image
                    </label>

                    <div class="col-lg-3">
                        <?php
                        echo FileUpload::widget([
                            'name' => 'PasswordForm[image]',
                            'url' => [
                                'upload/common?attribute=PasswordForm[image]'
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
                                        $('#progress_logo').show();
                                        $('#progress_logo .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                                'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:76px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-passwordform-image input[type=hidden]").val(data.result.files.name);$("#progress .progress-bar").attr("style","width: 0%;");
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

                        <div id="progress" class="progress" style="display: none;">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <div id="logo_preview">
                            <?php
                            if ($model->image != "") {
                                ?>
                                <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="width:76px;"/>
                                <?php
                            }
                            ?>
                        </div>

                    </div>

                    <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>
                    <?php
                }
                ?>

                <?php
                echo
                $form->field($model, 'oldPass', [
                    'inputOptions' => [
                        'placeholder' => 'Old password',
                        'class' => 'form-control',
                        'onchange' => 'profile.validation.checkPassword()'
                    ]
                ])->passwordInput()->label('Old password');
                ?>

                <?php
                echo
                $form->field($model, 'newPass', [
                    'inputOptions' => [
                        'placeholder' => 'New password',
                        'class' => 'form-control',
                        'onchange' => 'profile.validation.checkPassword()'
                    ]
                ])->passwordInput()->label('New password');
                ?>

                <?php
                echo
                $form->field($model, 'repeatNewPass', [
                    'inputOptions' => [
                        'placeholder' => 'Confirm password',
                        'class' => 'form-control',
                        'onchange' => 'profile.validation.checkPassword()'
                    ]
                ])->passwordInput()->label('Confirm password')
                ?>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-11">
                        <?php
                        echo
                        Html::submitButton('Save', [
                            'class' => 'btn btn-primary'
                        ])
                        ?>
                    </div>
                </div>


                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
