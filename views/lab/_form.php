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


$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


/* @var $this yii\web\View */
/* @var $model app\models\Labs */
/* @var $form yii\widgets\ActiveForm */

$states = [];
$areas = [];
if (!$model->isNewRecord) {
    $countryId = (isset($model->area)) ? $model->area->state->country_id : '';
    $model->country_id = $countryId;
    $states = AppHelper::getStatesByCountry($countryId);
    $sid = (isset($model->area)) ? $model->area->state_id : '';
    $model->governorate_id = $sid;
    $areas = AppHelper::getAreaByState($sid);
}
?>

<div class="labs-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a id="tab1" href="#tab_1" data-toggle="tab">Lab Details</a></li>
                    <li><a id="tab2" href="#tab_2" data-toggle="tab">Address</a></li>
                    <li><a id="tab3" href="#tab_3" data-toggle="tab">Working Days & Time</a></li>
                </ul>
            </div>
        </div>
        <br clear="all" />

        <div class="tab-content" style="margin-top: 40px;">

            <div class="tab-pane active" id="tab_1">

                <div class="col-md-6">
                    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="clearfix"></div>

                <?php if (Yii::$app->session['_eyadatAuth'] == 1) { ?>
                    <div class="col-md-6">
                        <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
                    </div>
                <?php } ?>
                <div class="col-md-6">
                    <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true, 'value' => null]) ?>
                </div>
                <div class="clearfix"></div>

                <div class="col-lg-6">
                    <?php
                    if (!$model->isNewRecord) {
                        $model->insurance_id = AppHelper::getSelectedInsuranceIds($model->lab_id, 'L');
                        //print_r($model->insurance_id);
                    }
                    ?>
                    <?=
                    $form->field($model, 'insurance_id')->dropDownList(AppHelper::getInsuranceList(), [
                        'class' => 'form-control select2',
                        'multiple' => 'multiple'
                    ])
                    ?>
                </div>
                <div class="col-lg-6">
                    <?php
                    if (!$model->isNewRecord) {
                        $model->service_id = AppHelper::getSelectedServicesIds($model->lab_id, 'L');
                        //print_r($model->category_id);
                    }
                    ?>
                    <?=
                    $form->field($model, 'service_id')->dropDownList(AppHelper::getServicesList('L'), [
                        'class' => 'form-control select2',
                        'multiple' => 'multiple'
                    ])
                    ?>
                </div>
                <div class="clearfix"></div>

                <div class="col-lg-6">
                    <?php
                    if (!$model->isNewRecord) {
                        $model->test_id = AppHelper::getSelectedTestsIds($model->lab_id);
                    }
                    ?>
                    <?=
                    $form->field($model, 'test_id')->dropDownList(AppHelper::getTestsList('L'), [
                        'class' => 'form-control select2',
                        'multiple' => 'multiple'
                    ])
                    ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'consultation_time_interval')->textInput() ?>
                </div>
                <div class="clearfix"></div>

                <div class="col-md-6">
                    <?= $form->field($model, 'home_test_charge')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'max_booking_per_lot')->textInput() ?>
                </div>
                <div class="clearfix"></div>

                <div class="col-md-6" style="display:none;">
                    <?php
                    if (!$model->isNewRecord) {
                        $model->start_time = (!empty($model)) ? date('h:i A', strtotime($model->start_time)) : '';
                        $model->end_time = (!empty($model)) ? date('h:i A', strtotime($model->end_time)) : '';
                    }
                    echo $form->field($model, 'start_time')->widget(TimePicker::className(), [
                        'name' => 'start_time',
                        'value' => $model->end_time,
                       
                        'pluginOptions' => [
                            'showSeconds' => false,
                           

                        ]

                    ]);
                    ?>
                </div>
                <div class="col-md-6" style="display:none;">
                    <?php
                    echo $form->field($model, 'end_time')->widget(TimePicker::className(), [
                        'name' => 'end_time',
                        'value' => $model->end_time,
                        'options' => [
                           
                        ],
                        'pluginOptions' => [
                            'showSeconds' => false,
                           
                        ]
                    ]);
                    ?>
                </div>
                <div class="clearfix"></div>


                <div class="col-md-6">
                    <?= $form->field($model, 'admin_commission')->textInput(['maxlength' => true]) ?>
                </div>

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
                <div class="clearfix"></div>


                <div class="col-md-6">
                    <label>
                        Logo in English (500 X 500)
                    </label>
                    <br />

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Labs[image_en]',
                        'url' => [
                            'upload/common?attribute=Labs[image_en]'
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
                                            $(".field-labs-image_en input[type=hidden]").val(data.result.files.name);
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
                        Logo in Arabic (500 X 500)
                    </label>
                    <br />

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Labs[image_ar]',
                        'url' => [
                            'upload/common?attribute=Labs[image_ar]'
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
                                            $(".field-labs-image_ar input[type=hidden]").val(data.result.files.name);
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

            <div class="tab-pane" id="tab_2">
                <?php if (Yii::$app->session['_eyadatAuth'] == 1) { ?>
                    <div class="col-md-6">
                        <?=
                        $form->field($model, 'country_id')->dropDownList(AppHelper::getCountryList(), [
                            'class' => 'form-control select2',
                            'prompt' => 'Please select',
                            'onchange' => "common.getState(this.value, 'labs-governorate_id')"
                        ])
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?=
                        $form->field($model, 'governorate_id')->dropDownList($states, [
                            'prompt' => 'Please select',
                            'class' => 'form-control select2',
                            'onchange' => "common.getArea(this.value, 'labs-area_id')"
                        ])
                        ?>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <?=
                        $form->field($model, 'area_id')->dropDownList($areas, [
                            'class' => 'form-control select2',
                            'prompt' => 'Please select',
                        ])
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'block')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'street')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'building')->textInput(['maxlength' => true]) ?>
                    </div>
                <?php } ?>

                <div class="col-md-12">
                    <div class="form-group field-select_location">
                        <label class="control-label" for="select_lication">Select Location</label>
                        <input type="text" id="select_location" class="form-control" name="select_location">
                    </div>
                    <!--Google Maps-->
                    <div id="map-canvas" style="height: 300px; position: relative; overflow: hidden;"></div>

                </div>

                <div class="col-md-12">
                    <?php echo $form->field($model, 'latlon')->textInput(['maxlength' => true, 'readonly' => true, 'id' => 'google-latlon'])->label('Geo Coordinates') ?>
                </div>
            </div>

            <div class="tab-pane" id="tab_3">
                <?php if (Yii::$app->session['_eyadatAuth'] == 1) { ?>
                    <?php
                    $days_array = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                    ?>
                    <div class="col-md-12">
                        <fieldset>
                            <!-- <legend>Working Days:</legend> -->
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
                                    foreach ($model->labsWorkingDays as $row) {
                                        array_push($saved_days, $row->day);
                                    }
                                }

                                foreach ($days_array as $days) { ?>
                                   <tr class="trDays tr<?= $i; ?>">
                                        <td>
                                            <?php
                                            $checked = (!empty($saved_days) && in_array($days, $saved_days)) ? true : false;
                                            if (!$model->isNewRecord) {
                                                $modelTime = app\models\LabsWorkingDays::find()
                                                    ->where(['lab_id' => $model->lab_id, 'day' => $days])->one();
                                                $model->lab_start_time = (!empty($modelTime)) ? date('h:i A', strtotime($modelTime->lab_start_time)) : '';
                                                $model->lab_end_time = (!empty($modelTime)) ? date('h:i A', strtotime($modelTime->lab_end_time)) : '';
                                            }
                                            ?>
                                            <?php echo $form->field($model, 'days[]')->checkbox(['value' => $days, "checked" => $checked]); ?> <?= $days; ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo TimePicker::widget([
                                                'name' => "Labs[lab_start_time][$days]",
                                                'value' => $model->lab_start_time,
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
                                                'name' => "Labs[lab_end_time][$days]",
                                                'value' => $model->lab_end_time,
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
                    <div class="clearfix"></div>
                <?php } ?>
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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');

$this->registerJsFile('@web/js/google_maps.js', ['depends' => 'yii\web\JqueryAsset']);


$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['googleMapKey'] . '&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);


?>