<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\DoctorAppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$ptype = '';

$get = Yii::$app->request->queryParams;
if (isset($get['DoctorAppointmentSearch']['type'])) {
    $ptype = $get['DoctorAppointmentSearch']['type'];
}
$app_type = '';
if ($ptype == 'U')
    $app_type = 'Upcoming';
else if ($ptype == 'C')
    $app_type = 'Completed';
else if ($ptype == 'F')
    $app_type = 'Failed';
else
    $app_type = "All";


$this->title = $app_type . ' - Doctor Appointments';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-left">
                    <?php echo Html::a('Calender View', ['calender'], ['class' => 'btn btn-info']) ?>
                </p>

                <span class="clearfix"></span>
                <?php // echo $this->render('_search', ['model' => $searchModel]);  
                ?>
                <div class="table-responsive">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'appointment_number',
                            'name',
                            'email:email',
                            'phone_number',
                            [
                                'attribute' => 'consultation_type',
                                'value' => function ($model) {
                                    return ($model->consultation_type == 'V') ? 'Video Consultation' : 'In-person Consultation';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'consultation_type', ['V' => 'Video Consultation', 'I' => 'In-person Consultation'], ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            ],
                            'consultation_fees',

                            [
                                'attribute' => 'appointment_datetime',
                                'value' => function ($model) {
                                    // return date('Y-m-d h:i A', strtotime($model->appointment_datetime));
                                    return date('d M,Y', strtotime($model->appointment_datetime)) . ' at ' . date('H:i A', strtotime($model->appointment_datetime));
                                }
                            ],
                            [
                                'attribute' => 'user_id',
                                'value' => function ($model) {
                                    return $model->user->first_name . ' ' . $model->user->last_name;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'user_id', AppHelper::getAllUser(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                            ],
                            [
                                'attribute' => 'doctor_id',
                                'value' => function ($model) {
                                    return $model->doctor->name_en;
                                },
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 2) ? true : false,
                                'filter' => Html::activeDropDownList($searchModel, 'doctor_id', AppHelper::getDoctorsList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Doctor']),
                            ],
                            [
                                'label' => 'Translator',
                                'attribute' => 'translator_id',

                                'value' => function ($model) {
                                    return $model->translator ? $model->translator->name_en : "";
                                },
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 8) ? false : true,
                                'filter' => Html::activeDropDownList($searchModel, 'translator_id', AppHelper::getTranslatorList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Translator']),
                            ],
                            /*[
                                'label'=>'Commission%',
                                'value'=> function($model)
                                {
                                    return $model->admin_commission;
                                },
                            ],
                            [
                                'label'=>'Admin Commission',
                                'value'=> function($model)
                                {
                                    $commission = ($model->consultation_fees * $model->admin_commission) /100;
                                    return 'KWD '.number_format($commission,3) ;
                                },
                            ],*/
                            //'user_id',
                            //'doctor_id',
                            //'prescription_file',
                            //'is_deleted',
                            //'created_at',
                            //'updated_at',
                            // [
                            //     'attribute' => 'kid_id',
                            //     'value' => function ($model) {
                            //         return ($model->kid) ? $model->kid->name_en : "Self";
                            //     }
                            // ],
                            [
                                'attribute' => 'kid_id',
                                'value' => function ($model) {
                                    return ($model->kid) ? $model->kid->name_en : "Self";
                                },
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 2) ? true : false,
                                'filter' => Html::activeDropDownList($searchModel, 'kid_id', AppHelper::getAllKids(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                            ],
                            //'is_cancelled',
                            //'is_paid',
                            //'discount',
                            //'sub_total',
                            'amount',
                            //'payment_initiate_time',
                            //'has_gone_payment',
                            'duration',
                            [
                                'label' => 'Status',
                                'value' => function ($model) {
                                    $today_date = strtotime(date('Y-m-d h:i:s'));
                                    $appointment_datetime = strtotime($model->appointment_datetime);
                                    $returnTxt = "";
                                    if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date && $model->not_show == 0) {
                                        $returnTxt = 'Upcoming';
                                    } elseif (($model->is_paid == 1 || $model->is_paid == 0) && $model->is_cancelled == 1 && $model->is_completed == 0 && $model->not_show == 0) {
                                        $returnTxt = 'Cancelled';
                                    } elseif (($model->is_paid == 2 || $model->is_paid == 0 || $model->is_paid == 1) && $model->is_cancelled == 0 && $model->is_completed == 0  && $model->not_show == 0 && $appointment_datetime < $today_date) {
                                        $returnTxt = 'Failed';
                                    } elseif ($model->not_show == 0 && $model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 1) {
                                        $returnTxt = 'Completed';
                                    } elseif ($model->not_show == 1 && $model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0) {
                                        $returnTxt = 'No Show';
                                    }
                                    return '<span id="return' . $model->doctor_appointment_id . '">' . $returnTxt . '<span>';
                                },
                                'format' => 'raw',
                            ],
                            /*[
                                'label' => 'Completed?',
                                'attribute' => 'is_completed',
                                'format' => 'raw',
                                'value' => function ($model) 
                                {
                                    $today_date = strtotime(date('Y-m-d h:i:s'));
                                    $appointment_datetime = strtotime($model->appointment_datetime);
                                    $returnTxt = "";
                                    $is_enebled = ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date ) ? 0 : 1;
                                    
                                        return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_completed, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->doctor_appointment_id,
                                            'onclick' => 'common.changeAppoitmentStatus("doctor-appointment/complete",this,' . $model->doctor_appointment_id . ')',
                                            'disabled'=>($is_enebled==1)? true : false
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->doctor_appointment_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_completed', [1 => 'Yes', 0 => 'No'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                            ],*/
                            [
                                'attribute' => 'uploaded_report',
                                'value' => function ($model) {
                                    if ($model->uploaded_report != NULL) {
                                        $report = (!empty($model->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->uploaded_report) : '';
                                        return '<a href="' . $report . '" download title="download"><i class="fa fa-download"></i> Download</a>';
                                    } else {
                                        if ($model->is_completed == 1) {
                                            return '<a href="javascript:(0)" class="uploadreport" data-id="' . $model->doctor_appointment_id . '" title="Upload Report"><i class="fa fa-upload"></i> Upload</a>';
                                        }
                                    }
                                },
                                'format' => 'raw'
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {delete}'
                            ],
                        ],
                    ]);
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>
<div id="UploadReport" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-body">
                <div class="msg"></div>
                <?php $form = ActiveForm::begin(); ?>
                <input type="hidden" name="DoctorAppointment[doctor_appointment_id]" id="modal_doctor_appointment_id">
                <label>Title In English <span class="text text-danger">*</span></label>
                <input type="text" name="DoctorAppointment[report_title_en]" id="modal_report_title_en" class="form-control" required><br>

                <label>Title In Arabic <span class="text text-danger">*</span></label>
                <input type="text" name="DoctorAppointment[report_title_ar]" id="modal_report_title_ar" class="form-control" required>
                <br>

                <label>
                    Upload Doctor Report (Extension .pdf only) <span class="text-danger">*</span>
                </label>
                <br />
                <br />
                <?php
                echo FileUpload::widget([
                    'name' => 'DoctorAppointment[uploaded_report]',
                    'url' => [
                        'upload/common?attribute=DoctorAppointment[uploaded_report]'
                    ],
                    'options' => [
                        'accept' => 'application/pdf',
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
                                            
                                            var img = \'<br/>Uploaded\';
                                            $("#logo_preview").html(img);
                                            $(".msg").hide()
                                            $(".field-uploaded_report input[type=hidden]").val(data.result.files.name);
                                            $("#doctors-uploaded_report").val(data.result.files.name);
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
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

                </div>
                <input type="hidden" id="doctors-uploaded_report">
                <div class="form-group"><br><Br>
                    <button type="button" onclick="common.addDoctorReport()" class="btn btn-primary">Submit</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});
    $('.uploadreport').click(function(){
        var aid = $(this).data('id');
        $('#modal_doctor_appointment_id').val(aid);
        $('#UploadReport').modal('show');

    });
    ", \yii\web\View::POS_END, 'select-picker');
