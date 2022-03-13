<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;
use kartik\time\TimePicker;
use yii\helpers\Url;

\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Doctors */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$insurances = [];
/*if (!$model->isNewRecord) {
    $countryId = $model->area->state->country_id;
    $model->country_id = $countryId;
    $states = AppHelper::getStatesByCountry($countryId);
    $sid = $model->area->state_id;
    $model->governorate_id = $sid;
}*/

?>

<style>
    .field-doctors-days {
        float: left;
    }

    .chkbox {
        width: 20px !important;
        height: 20px !important;
        margin-right: 10px !important;
    }
</style>
<div class="doctors-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a id="tab1" href="#tab_1" data-toggle="tab">Doctor Details</a></li>
                    <li><a id="tab3" href="#tab_3" data-toggle="tab">Working Days & Time</a></li>
                </ul>
            </div>
        </div>
        <br clear="all" />

        <div class="tab-content" style="margin-top: 40px;">

            <div class="tab-pane active" id="tab_1">
                <div class="col-md-6">
                    <?= $form->field($model, 'name_en')->textInput([
                        'maxlength' => true,
                        "onclick" => "common.triggerClinicInfo();"
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true, 'dir' => 'rtl']) ?>
                </div>
                <div class="clearfix"></div>

                <?php if (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 2) { ?>
                    <div class="col-md-6">
                        <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
                    </div>
                <?php } ?>
                <div class="col-md-6">
                    <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true]) ?> </div>

                <?php if (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 2) { ?>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'registration_number')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?php
                        if (Yii::$app->session['_eyadatAuth'] == 2) {
                            echo $form->field($model, 'clinic_id')->dropDownList(AppHelper::getClinicsList(), ['class' => 'form-control select', 'onfocus' => 'common.getClinicDays(this.value,"doctors-insurance_id")']);
                        } else {
                            echo $form->field($model, 'clinic_id')->dropDownList(AppHelper::getClinicsAndHospitalList(), ['empty' => 'Please Select', 'class' => 'form-control select', 'onchange' => 'common.getClinicDays(this.value,"doctors-insurance_id")']);
                        }
                        ?>
                    </div>

                    <div class="col-lg-6">
                        <?php
                        if (!$model->isNewRecord) {
                            $model->insurance_id = AppHelper::getSelectedInsuranceIds($model->doctor_id, 'D');
                        }
                        ?>
                        <?=
                        $form->field($model, 'insurance_id')->dropDownList($insurances, [
                            'class' => 'form-control select2',
                            'multiple' => 'multiple'
                        ])
                        ?>
                    </div>
                    <div class="col-lg-6">
                        <?php
                        if (!$model->isNewRecord) {
                            $model->category_id = AppHelper::getSelectedCategoriesIds($model->doctor_id, 'D');
                            //print_r($model->category_id);
                        }
                        ?>
                        <?=
                        $form->field($model, 'category_id')->dropDownList(AppHelper::getRecursiveCategory('D'), [
                            'class' => 'form-control select2',
                            'multiple' => 'multiple'
                        ])
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'gender')->dropDownList(['M' => 'Male', 'W' => 'Women', 'U' => 'Unisex',], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>
                    </div>


                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'years_experience')->textInput() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'qualification')->textInput() ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?php
                        if (!$model->isNewRecord) {
                            $model->type = explode(',', $model->type);
                        }
                        ?>

                        <?= $form->field($model, 'type')->dropDownList(
                            ['V' => 'Video Consultation', 'I' => 'Person Consultation',],
                            [
                                'empty' => 'Please Select',
                                'class' => 'form-control select2',
                                'multiple' => true
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'consultation_time_offline')->textInput() ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'consultation_price_regular')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'consultation_time_online')->textInput() ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'consultation_price_final')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        if (!$model->isNewRecord) {
                            $model->symptom_id = AppHelper::getSelectedSymptomsIds($model->doctor_id);
                            //print_r($model->category_id);
                        }
                        ?>
                        <?= $form->field($model, 'symptom_id')->dropDownList(AppHelper::getSymptomsList(), ['class' => 'form-control select2', 'multiple' => true]) ?>
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
                            'multiple' => true
                        ]) ?>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'description_en')->textarea([
                        'class' => 'form-control', 'rows' => '4'
                    ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'description_ar')->textarea([
                        'class' => 'form-control', 'dir' => 'rtl', 'rows' => '4'
                    ]);
                    ?>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-6">
                    <label>
                        Image (1000 X 1000)
                    </label>
                    <br />

                    <?php
                    echo FileUpload::widget([
                        'name' => 'doctors[image]',
                        'url' => [
                            'upload/common?attribute=doctors[image]'
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
                                                    $(".field-doctors-image input[type=hidden]").val(data.result.files.name);
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
                            if ($model->image != "") {
                        ?>
                                <br /><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="max-width:256px;" />
                        <?php
                            }
                        }
                        ?>
                    </div>

                    <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>
                </div>

                <div class="col-md-6">
                    <br>
                    <?php echo $form->field($model, 'is_featured')->checkbox(); ?>
                </div>

            </div>

            <div class="tab-pane" id="tab_3">
                <?php
                $days_array = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                if (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 2) { ?>
                    <div class="col-md-12">
                        <fieldset>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                </tr>
                                <?php
                                $i = 0;
                                $saved_days = [];
                                if (!$model->isNewRecord) {
                                    foreach ($model->doctorWorkingDays as $row) {
                                        array_push($saved_days, $row->day);
                                    }
                                }
                                foreach ($days_array as $days) { ?>
                                    <tr class="trDays tr<?= $i; ?>">
                                        <td>
                                            <?php
                                            $checked = (!empty($saved_days) && in_array($days, $saved_days)) ? true : false;
                                            if (!$model->isNewRecord) {
                                                $modelTime = app\models\DoctorWorkingDays::find()
                                                    ->where(['doctor_id' => $model->doctor_id, 'day' => $days])->one();
                                                $model->start_time = (!empty($modelTime)) ? date('h:i A', strtotime($modelTime->start_time)) : '';
                                                $model->end_time = (!empty($modelTime)) ? date('h:i A', strtotime($modelTime->end_time)) : '';
                                            }
                                            ?>
                                            <?php echo $form->field($model, 'days[]')->checkbox(['value' => $days, "checked" => $checked, 'class' => 'chkbox checkboxday' . $i]); ?> <?= $days; ?>
                                            <br>
                                            <span class="text-danger clinicmsg msg<?= $i; ?>">Clinic Off</span>
                                        </td>
                                        <td>

                                            <?php
                                            echo TimePicker::widget([
                                                'name' => "Doctors[start_time][$days]",
                                                'value' => $model->start_time,

                                                'options' => [
                                                    'class' => 'start_time_picker_' . $i,

                                                ],

                                                'pluginOptions' => [
                                                    'showSeconds' => false,
                                                    "defaultTime" => "08:00 AM"

                                                ]
                                            ]);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo TimePicker::widget([
                                                'name' => "Doctors[end_time][$days]",
                                                'value' => $model->end_time,
                                                'options' => [
                                                    'class' => 'end_time_picker_' . $i,
                                                    "onchange" => 'common.checkDoctorWorkingTime(' . $i . ')',
                                                ],
                                                'pluginOptions' => [
                                                    'showSeconds' => false,
                                                    "defaultTime" => "05:00 PM"

                                                ]
                                            ]);
                                            ?>
                                        </td>
                                    </tr>
                                <?php $i++;
                                } ?>
                            </table>
                        </fieldset>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>

            </div>

        </div>

    </div>

    <p>&nbsp;</p>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <br clear="all" />
    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
        $('.trDays input').attr('disabled',true);
        $('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
if (!$model->isNewRecord) {
    $this->registerJs("
        common.getClinicDays($model->clinic_id,'doctors-insurance_id');
        ", \yii\web\View::POS_END, 'time-picker');
}
?>