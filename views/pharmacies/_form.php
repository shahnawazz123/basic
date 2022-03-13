<?php

use yii\helpers\Html;
use yii\helpers\BaseUrl;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;

\app\assets\SelectAsset::register($this);
$this->registerJsFile(BaseUrl::home() . 'js/pharmacies.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['googleMapKey'] . '&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);

$states = [];
$areas = [];
if (!$model->isNewRecord) {
    $countryId = $model->area->state->country_id;
    $model->country_id = $countryId;
    $states =  AppHelper::getStatesByCountry($countryId);
    $sid =  $model->area->state_id;
    $model->governorate_id = $sid;
    $areas = AppHelper::getAreaByState($sid);
}


/* @var $this yii\web\View */
/* @var $model app\models\Pharmacies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pharmacies-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">


        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true, 'dir' => 'rtl']) ?>
        </div>
        <?php if (Yii::$app->session['_eyadatAuth'] == 1) { ?>
            <div class="col-md-6">
                <?= $form->field($model, 'minimum_order')->textInput() ?>
            </div>
        <?php } ?>
        <div class="col-md-6">
            <?= $form->field($model, 'shop_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?php
            if (!$model->isNewRecord) {
                $model->accepted_payment_method = explode(',', $model->accepted_payment_method);
            }
            ?>

            <?php echo $form->field($model, 'accepted_payment_method')->dropDownList(AppHelper::$payment_mode, [
                'class' => 'select2 form-control',
                'multiple' => true,

            ]) ?>
        </div>
        <div class="clearfix"></div>
        <?php if (Yii::$app->session['_eyadatAuth'] == 1) { ?>
            <div class="col-md-6">
                <?=
                $form->field($model, 'country_id')->dropDownList(AppHelper::getCountryList(), [
                    'class' => 'form-control select2',
                    'prompt' => 'Please select',
                    'onchange' => "common.getState(this.value, 'pharmacies-governorate_id')"
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'governorate_id')->dropDownList($states, [
                    'prompt' => 'Please select',
                    'class' => 'form-control select2',
                    'onchange' => "common.getArea(this.value, 'pharmacies-area_id')"
                ])
                ?>
            </div>

            <div class="col-md-6">
                <?=
                $form->field($model, 'area_id')->dropDownList($areas, [
                    'class' => 'form-control select2',
                    'prompt' => 'Please select',
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'street')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'floor')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'block')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'building')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-12">
                <div class="form-group field-select_location">
                    <label class="control-label" for="select_lication">Select Location</label>
                    <input type="text" id="select_location" class="form-control" name="select_location">
                </div>
                <!--Google Maps-->
                <div id="map-canvas" style="height: 300px; position: relative; overflow: hidden;"></div>

            </div>

            <div class="col-md-12">
                <?php echo $form->field($model, 'latlon')->textInput(['maxlength' => true, 'readonly' => true, 'id' => 'google-latlon', 'class' => 'google-latlon form-control'])->label('Geo Coordinates') ?>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-6">
                <?= $form->field($model, 'admin_commission')->textInput(['maxlength' => true]) ?>
            </div>
            <?php $delivery_charge = ''; ?>
            <div id="deliveryCharge">
                <div class="col-md-6" style="<?= $delivery_charge; ?>">
                    <?= $form->field($model, 'delivery_charge')->textInput(['value' => 0]) ?>
                </div>
            </div>


            <div class="clearfix"></div>
            <div class="col-md-12">
                <?= $form->field($model, 'is_featured')->checkbox() ?>
            </div>

            <!-- /* --------------------------------- IS Free -------------------------------- */ -->
            <div class="col-md-12">
                <?= $form->field($model, 'is_free_delivery')->checkbox(['class' => 'freeDeliveryCheck']) ?>
            </div>
            <?php

            if (!$model->isNewRecord) {
                if ($model->is_free_delivery == 1) {
                    $delivery_charge = "display:none";
                }
                $model->password = "";
            }
            if ($model->isNewRecord && $model->is_free_delivery == '1') {
                $delivery_charge = "display:none";
            }
            ?>

            <!-- /* --------------------------------- IS Free -------------------------------- */ -->

            <!-- /* ------------------------------ enable login ------------------------------ */ -->
            <div class="col-md-12">
                <?= $form->field($model, 'enable_login')->checkbox(['class' => 'loginCheck']) ?>
            </div>
            <?php
            $display = 'display:none;';
            if (!$model->isNewRecord) {
                if ($model->enable_login == 1) {
                    $display = "";
                }
                $model->password = "";
            }
            if ($model->isNewRecord && $model->enable_login == '1') {
                $display = "";
            }
            ?>
            <div id="login-section" style="<?= $display; ?>">
                <div class="col-md-6">
                    <?php
                    // $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true])
                    ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-6">
                    <?=
                    $form->field($model, 'password')->passwordInput([
                        'autocomplete' => 'off',
                        'readonly' => 'readonly',
                        'onfocus' => 'this.removeAttribute(\'readonly\');',
                        'style' => 'background-color:#fff'
                    ])
                    ?>
                </div>
                <span class="clearfix"></span>
                <!-- <div class="col-md-6">
                    <?= $form->field($model, 'confirm_password')->passwordInput() ?>
            </div> -->
            </div>
        <?php } ?>
        <!-- /* ------------------------------ enable login ------------------------------ */ -->


        <div class="clearfix"></div>
        <div class="col-md-6">
            <label>
                Image in English (1000*1000)
            </label>
            <br />

            <?php
            echo FileUpload::widget([
                'name' => 'pharmacies[image_en]',
                'url' => [
                    'upload/common?attribute=pharmacies[image_en]'
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
                                            $(".field-pharmacies-image_en input[type=hidden]").val(data.result.files.name);
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
                        <br /><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image_en ?>" alt="img" style="max-width:256px;" />
                <?php
                    }
                }
                ?>
            </div>
            <?php echo $form->field($model, 'image_en')->hiddenInput()->label(false); ?>
        </div>
        <div class="col-md-6">
            <label>
                Image in Arabic (1000*1000)
            </label>
            <br />
            <?php
            echo FileUpload::widget([
                'name' => 'pharmacies[image_ar]',
                'url' => [
                    'upload/common?attribute=pharmacies[image_ar]'
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
                                            $(".field-pharmacies-image_ar input[type=hidden]").val(data.result.files.name);
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
                        <br /><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image_ar ?>" alt="img" style="max-width:256px;" />
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

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');

if (!$model->isNewRecord) {
    if ($model->enable_login == 1) {
        $js = '$(window).on(\'load\',function(){
                    jQuery("#w0").yiiActiveForm("add", {
                        "id": "pharmacies-email",
                        "name": "pharmacies[email]",
                        "container": ".field-pharmacies-email",
                        "input": "#pharmacies-email",
                        "validate": function (attribute, value, messages, deferred, $form) {
                            yii.validation.required(value, messages, {"message": "Email can\'t be blank"});
                            yii.validation.email(value, messages, {
                                "pattern": /^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,
                                "fullPattern": /^[^@]*<[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,
                                "allowName": false,
                                "message": "Email is not a valid email address.",
                                "enableIDN": false,
                                "skipOnEmpty": 1
                            })
                        }
                    });
                    /*$("#pharmacies-password").on(\'change\',function(){
                        var pass = $(this).val();
                        if($.trim(pass)!=""){
                            jQuery("#w0").yiiActiveForm("add", {
                                "id": "pharmacies-confirm_password",
                                "name": "Pharmacies[confirm_password]",
                                "container": ".field-pharmacies-confirm_password",
                                "input": "#pharmacies-confirm_password",
                                "validate": function (attribute, value, messages, deferred, $form) {
                                    yii.validation.required(value, messages, {"message": "Confirm Password can\'t be blank"});
                                    yii.validation.compare(value, messages, {
                                        "operator": "==",
                                        "type": "string",
                                        "compareAttribute": "pharmacies-password",
                                        "skipOnEmpty": 1,
                                        "message": "Password and Confirm password must match"}, $form);
                                }
                            });
                        }
                        else{
                            $("#pharmacies-confirm_password").val("");
                            $("#w0").yiiActiveForm("remove", "pharmacies-confirm_password");
                            $(".field-pharmacies-confirm_password").removeClass("has-error");
                            $(".field-pharmacies-confirm_password").addClass("has-success");
                            $(".field-pharmacies-confirm_password .help-block").html("");
                        }
                   })*/
                });';
        $this->registerJs($js);
    }
}
$this->registerJs('
    $ (".select2").select2 ("val", "");
jQuery(document).on(\'icheck\', function(){
    jQuery(\'input[type=checkbox]\').iCheck({
        checkboxClass: \'icheckbox_square-green\'
    });
}).trigger(\'icheck\');

$(".loginCheck").on(\'ifChecked\', function (e) {
    pharmacies.showHideSupportLogin()
});
$(".loginCheck").on(\'ifUnchecked\', function (e) {
    pharmacies.showHideSupportLogin()
});
$(".freeDeliveryCheck").on(\'ifChecked\', function (e) {
    pharmacies.showHideDeliveryCharge();
});
$(".freeDeliveryCheck").on(\'ifUnchecked\', function (e) {
    pharmacies.showHideDeliveryCharge();
});

');

$this->registerJsFile('@web/js/google_maps.js', ['depends' => 'yii\web\JqueryAsset']);

?>