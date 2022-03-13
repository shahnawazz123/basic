<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use kartik\detail\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\models\Settings;
use app\models\Translator;
use app\models\User;
use app\models\Users;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorAppointments */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$model->is_call_initiated = 0;
$model->save(false);

$newTimeZone = new \DateTimeZone(Yii::$app->params['timezone']);
$allowCancel = true;
$payment = \app\models\Payment::find()
    ->where(['type_id' => $model->doctor_appointment_id, 'type' => 'DA'])
    ->orderBy(['payment_id' => SORT_DESC])
    ->one();

$datetime = $model->appointment_datetime;
$bookingDatetime = new \DateTime($datetime);
$currentTime = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone(date_default_timezone_get()));
$currentTime->setTimezone($newTimeZone);
$interval = $currentTime->diff($bookingDatetime);
$minutes = $interval->days * 24 * 60;
$minutes += $interval->h * 60;
$minutes += $interval->i;
// if ($model->is_cancelled == 0 && $currentTime <= $bookingDatetime && $model->is_paid == 1) {
// Allow cancel only before 15 minutes
if ($model->is_cancelled == 0 && $minutes > \Yii::$app->params['allowed_cancel_minutes'] && $model->is_paid == 1 && $model->is_completed == 1) {
    $isCancelable = 1;
    $slotDateTime = $model->appointment_datetime;
    if (strtotime($slotDateTime) <= time() && $model->is_completed == 0) {
        echo $isCancelable = 0;
    }
} else {
    $isCancelable = 0;
}
?>
<style>
    form .table tr th {
        text-align: left !important;
        width: 25% !important;
        background: #ffffff !important;
    }

    .panel-heading {
        padding: 19px;
        color: #fff !important;
        background: #4FB3CD !important;
        font-weight: 700 !important;
    }

    .panel-title {
        font-weight: 800 !important;
    }

    form table td {
        background: #EFFBFF !important;
        color: #000 !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull-right">

                    <?php

                    $today_date = strtotime(date('Y-m-d H:i:s'));
                    $appointment_datetime = strtotime($model->appointment_datetime);

                    ?>

                    <?= ($isCancelable == 1 && $model->is_completed == 0) ? Html::a('Cancel booking', ['cancel-booking', 'id' => $model->doctor_appointment_id], ['class' => 'btn btn-red', 'onclick' => 'return confirm("Are you sure you want to cancel this booking?")']) : ""
                    ?>

                    <?php if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date && $model->consultation_type == 'V') { ?>
                        <?php if (Yii::$app->session['_eyadatAuth'] == 8) { ?>
                            <?= Html::a('Join Video Consultation', ['video-call', 'id' => $model->doctor_appointment_id], ['class' => 'btn btn-primary']); ?>
                        <?php } else {  ?>
                            <?= Html::a('Start Video Consultation', ['video-call', 'id' => $model->doctor_appointment_id], ['class' => 'btn btn-primary']); ?>

                        <?php } ?>
                    <?php } ?>

                    <?php if (Yii::$app->session['_eyadatAuth'] != 8) { ?>
                        <?= Html::a('Request Report', ['report-request', 'id' => $model->doctor_appointment_id], ['class' => 'btn btn-info']); ?>
                        <?php

                        // show only in upcoming and completed status
                        if ((($appointment_datetime > $today_date && $model->is_cancelled == 0 && $model->is_completed == 0) || ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 1))) {
                            echo "&nbsp;" . Html::a('Create Prescription', ['doctor-prescriptions/create', 'ProductSearch[doctor_appointment_id]' => $model->doctor_appointment_id], ['class' => 'btn btn-success']);

                            echo "&nbsp;" . '<a href="javascript:void(0)" class="uploadreport btn btn-primary" data-id="' . $model->doctor_appointment_id . '" title="Upload Report"><i class="fa fa-upload"></i> Upload Report</a>';
                        }
                        ?>

                        <?php
                        if ($model->uploaded_report != null) {
                            $report = (!empty($model->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->uploaded_report) : '';
                            echo '<a href="' . $report . '" download title="download" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> Download Report</a>';
                        } ?>

                        <?php if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $model->not_show == 0 && $appointment_datetime > $today_date) {
                            $url = "doctor-appointment/complete";
                            echo Html::a('Complete', ['doctor-appointment/complete-url?id=' . $model->doctor_appointment_id], ['class' => 'btn btn-warning']);
                        } ?>

                        <?php if ($model->not_show == 0 && $model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date) {
                            $url = "doctor-appointment/complete";
                            echo Html::a('No Show', ['doctor-appointment/not-show-url?id=' . $model->doctor_appointment_id], ['class' => 'btn btn-danger']);
                        } ?>
                    <?php
                    } ?>
                </p>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            //'mode'=>DetailView::MODE_VIEW,
                            'buttons1' => '',
                            'panel' => [
                                'heading' => 'Account Information',
                                'type' => DetailView::TYPE_DEFAULT,
                            ],
                            'attributes' => [
                                [
                                    'label' => 'Customer Name',
                                    'value' => $model->user->first_name . ' ' . $model->user->last_name,
                                    'labelColOptions' => ['style' => 'width:25%;text-align:right'],
                                ],
                                [
                                    'label' => 'Email',
                                    'value' => $model->user->email
                                ],
                                [
                                    'label' => 'Phone',
                                    'value' => $model->user->phone
                                ],
                            ],
                        ])
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'buttons1' => '',
                            'panel' => [
                                'heading' => 'Booking Information',
                                'type' => DetailView::TYPE_DEFAULT,
                            ],
                            'attributes' => [

                                //'name',
                                //'email:email',
                                //'phone_number',
                                [
                                    'attribute' => 'consultation_type',
                                    'value' => ($model->consultation_type == 'V') ? 'Video Consultation' : 'In-person Consultation',
                                ],
                                [
                                    'attribute' => 'need_translator',
                                    'value' => ($model->need_translator == 1) ?  "Yes" : "No",
                                ],
                                [
                                    "label" => "Select Translator",
                                    "attribute" => "translator_id",
                                    'visible' => ($model->need_translator == 1 && Yii::$app->session['_eyadatAuth'] != 8) ? true : false,
                                    'value' => function ($data) use ($model) {
                                        $translator_list =  \app\helpers\AppHelper::getTranslatorList();
                                        return Html::dropDownList('status', $model->translator_id, $translator_list, [
                                            'prompt' => 'Select Status', 'class' => 'form-control',
                                            'onchange' => 'common.changeTranslatorInAppointment(this.value,' . $model->doctor_appointment_id . ')'
                                        ]);
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    "label" => "Translator Amount",
                                    'visible' => ($model->need_translator == 1 && Yii::$app->session['_eyadatAuth'] != 8) ? true : false,
                                    'value' => Yii::$app->formatter->asDecimal(Settings::find()->one()->translator_price, 2) . " KD",
                                ],


                                /*[
                                    'attribute' => 'user_id',
                                    'value' => $model->user->first_name . ' ' . $model->user->last_name
                                ],*/
                                [
                                    'attribute' => 'doctor_id',
                                    'value' => $model->doctor->name_en
                                ],
                                //'prescription_file',
                                //'created_at',
                                //'updated_at',
                                [
                                    'attribute' => 'kid_id',
                                    'label' => 'Appointment booked for',
                                    'value' => ($model->kid) ? $model->kid->name_en : "Self",
                                ],
                                [
                                    'attribute' => 'appointment_datetime',
                                    'value' => date('d M,Y', strtotime($model->appointment_datetime)) . ' at ' . date('H:i A', strtotime($model->appointment_datetime)),
                                ],
                                [
                                    'attribute' => 'duration',
                                    'label' => 'Duration',
                                    'value' => $model->duration . " minutes",
                                ],
                                /*[
                                    'attribute' => 'is_cancelled',
                                    'value' => ($model->is_cancelled == 1) ? "Yes" : "No",
                                ],

                                //'discount',
                                'consultation_fees',
                                [
                                    'attribute' => 'is_paid',
                                    'value' => ($model->is_paid == 1) ? "Yes" : "No",
                                ],
                                'sub_total',
                                'discount_price',
                                'amount',*/

                            ],
                        ])
                        ?>
                    </div>
                    <?php if ($model->uploaded_report != null && Yii::$app->session['_eyadatAuth'] != 8) { ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="pull-right"></div>
                                    <h3 class="panel-title">Reports from the doctor</h3>
                                    <div class="clearfix"></div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Title In English</th>
                                            <th>Title In Arabic</th>
                                            <th>Date</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $model->report_title_en ?></td>
                                            <td><?= $model->report_title_ar ?></td>
                                            <td><?= $model->report_upload_date ?></td>
                                            <td>
                                                <?php
                                                $report = (!empty($model->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->uploaded_report) : '';
                                                ?>
                                                <a href="<?= $report; ?>" download title="download" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> Download Report</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getRequestReports()
                                ->orderBy(['doctor_report_request_id' => SORT_ASC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider1) && Yii::$app->session['_eyadatAuth'] != 8) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider1,
                                'panel' => [
                                    'heading' => 'Request Report',
                                    'type' => DetailView::TYPE_DEFAULT,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    //'doctor_report_request_id',
                                    [
                                        'attribute' => 'doctor_request_for',
                                        'label' => 'Report requested for',
                                        'value' => function ($model) {
                                            return $model->doctor_request_for;
                                        },
                                    ],
                                    'request_date',
                                    [
                                        'attribute' => 'Request Status',
                                        'label' => 'Status',
                                        'value' => function ($model) {
                                            if ($model->status == 'P') {
                                                return 'Pending';
                                            } elseif ($model->status == 'A') {
                                                return 'Accepted';
                                            } elseif ($model->status == 'R') {
                                                return 'Rejected';
                                            }
                                        },
                                        'format' => 'raw',
                                    ],
                                    [
                                        'label' => 'Report',
                                        'value' => function ($data) {
                                            $images = '';
                                            if (!empty($data->userReport->userReportsImages)) {
                                                foreach ($data->userReport->userReportsImages as $img) {
                                                    $img1 = (!empty($img->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $img->image) : '';

                                                    if ($data->status == 'A') {
                                                        $images .= '<img src="' . $img1 . '" class="img-responsive imgPop" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="View Image" data-src="' . $img1 . '" data-title="' . $data->userReport->title . '">';
                                                    } else {
                                                        $img1 = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                                                        $images .= '<img src="' . $img1 . '" class="img-responsive" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="Not Accessed">';
                                                    }
                                                }
                                            } else {
                                                $img1 = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                                                $images .= '<img src="' . $img1 . '" class="img-responsive" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="Not Uploaded Yet">';
                                            }
                                            return $images;
                                        },
                                        'format' => 'raw',
                                    ]
                                    /*[
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => $btnStr,
                                        ],*/
                                ]
                            ]);
                        }
                        ?>
                    </div>
                    <div class="col-md-12">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'buttons1' => '',
                            'panel' => [
                                'heading' => 'Patient Reports',
                                'type' => DetailView::TYPE_DEFAULT,
                            ],
                            'attributes' => [
                                [
                                    "label" => "Patient Name",
                                    "value" => $model->user->first_name,
                                ],
                                [
                                    'attribute' => 'doctor_id',
                                    'value' => $model->doctor->name_en
                                ],
                                [
                                    'label' => 'Reports',
                                    'value' => function ($data) use ($model) {
                                        $images = "";
                                        $user = Users::find()
                                            ->where(['user_id' => $model->user->user_id])
                                            ->with('reports.userReportsImages')
                                            ->asArray()
                                            ->one();
                                        $reportImages = [];
                                        foreach ($user['reports'] as $reports) {
                                            foreach ($reports['userReportsImages'] as $image) {
                                                $reportImages[] = $image['image'];
                                                $images .= "
                                                <img class='img-responsive imgPop'
                                                style='width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;'
                                                src='" . Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $image['image']) . "' >
                                                ";
                                            }
                                        }
                                        return $images;
                                    },
                                    'format' => 'raw',
                                ],
                            ],
                        ])
                        ?>
                    </div>

                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getPrescriptionList($model->doctor_appointment_id)
                                ->orderBy(['doctor_appointment_prescription_id' => SORT_DESC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider1) && Yii::$app->session['_eyadatAuth'] != 8) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider1,
                                'panel' => [
                                    'heading' => 'Prescription List',
                                    'type' => DetailView::TYPE_DEFAULT,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    'doctor_appointment_prescription_id',
                                    [
                                        'label' => 'Pharmacy',
                                        'value' => function ($model) {
                                            return (!empty($model->pharmacy)) ? $model->pharmacy->name_en : '';
                                        },
                                    ],
                                    //'total_usage',
                                    /*[
                                                'label' => 'Total Used',
                                                'value' => function($model)
                                                {
                                                    $modelTotalUsed = \app\models\Orders::find()
                                                    ->where(['prescription_id' => $model->doctor_appointment_prescription_id, 'is_paid'=>1])
                                                    ->count();
                                                    return $modelTotalUsed;
                                                },
                                            ],*/
                                    [
                                        'attribute' => 'created_at',
                                        'label' => 'Prescription date',
                                        'value' => function ($model) {
                                            return $model->created_at;
                                        },
                                    ],
                                    [
                                        'label' => '#',
                                        'value' => function ($model) {
                                            $html = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['doctor-appointment-medicines/index', 'DoctorAppointmentMedicinesSearch[doctor_appointment_prescription_id]' => $model->doctor_appointment_prescription_id], ['title' => 'View Medicine']);

                                            $html .= "&nbsp;" . Html::a('<span class="glyphicon glyphicon-trash"></span>', ['doctor-prescriptions/delete', 'id' => $model->doctor_appointment_prescription_id, 'doctor_appointment_id' => $model->doctor_appointment_id], ['title' => 'Delete Precription', 'data-method' => 'post', 'data-pajax' => 0, 'data-confirm' => "Are you sure you want to delete this item?"]);
                                            return $html;
                                        },
                                        'format' => 'raw',
                                    ],

                                ],
                            ]);
                        }
                        ?>
                    </div>



                    <?php
                    if (!empty($payment) && Yii::$app->session['_eyadatAuth'] != 8) {
                    ?>
                        <div class="col-md-12">
                            <?=
                            DetailView::widget([
                                'model' => $model,
                                'buttons1' => '',
                                'panel' => [
                                    'heading' => 'Payment Information',
                                    'type' => DetailView::TYPE_DEFAULT,
                                ],
                                'attributes' => [
                                    [
                                        'attribute' => 'consultation_fees',
                                        'value' => 'KW.' . number_format($model->consultation_fees, 3),
                                    ],
                                    [
                                        'label' => 'Admin Commission%',
                                        'value' => $model->admin_commission,
                                    ],
                                    [
                                        'label' => 'Admin Commission',
                                        'value' => 'KW ' . number_format(($model->consultation_fees * $model->admin_commission) / 100, 3),
                                    ],
                                    [
                                        'attribute' => 'is_paid',
                                        'value' => ($model->is_paid == 1) ? "Yes" : "No",
                                    ],
                                    [
                                        'attribute' => 'sub_total',
                                        'value' => 'KW ' . number_format($model->sub_total, 3),
                                    ],
                                    [
                                        "label" => "Translator Amount",
                                        'visible' => ($model->need_translator == 1) ? true : false,
                                        'value' => Yii::$app->formatter->asDecimal(Settings::find()->one()->translator_price, 2) . " KD",
                                    ],

                                    [
                                        'attribute' => 'discount_price',
                                        'value' => 'KW ' . number_format($model->discount_price, 3),
                                    ],

                                    [
                                        'attribute' => 'amount',
                                        'value' => 'KW ' . number_format($model->amount, 3),
                                    ],
                                ],
                            ])
                            ?>
                        </div>
                    <?php
                    }
                    ?>

                    <?php
                    if (!empty($payment) && Yii::$app->session['_eyadatAuth'] != 8) {
                    ?>
                        <div class="col-md-12">
                            <?=
                            DetailView::widget([
                                'model' => $payment,
                                'buttons1' => '',
                                'panel' => [
                                    'heading' => 'Transaction Information',
                                    'type' => DetailView::TYPE_DEFAULT,
                                ],
                                'attributes' => [
                                    [
                                        'attribute' => 'paymode',
                                        'value' => AppHelper::getPaymodeType($payment->paymode),
                                    ],
                                    'result',
                                    //'gross_amount',
                                    //'PaymentID',
                                    [
                                        'label' => 'Payment Date',
                                        'value' => date('d M,Y', strtotime($payment->payment_date)) . ' at ' . date('H:i A', strtotime($payment->payment_date)),
                                    ],
                                    'transaction_id',
                                    'auth',
                                    'ref',
                                    'TrackID',
                                ],
                            ])
                            ?>
                        </div>
                    <?php
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 5px 30px; !important">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <img src="" class="modal_report_src" style="width: 100%;"> <br><br>
                <center><a href="" class="modal_download btn btn-primary btn-sm" download title="Download Image"><i class="fa fa-download"></i> Download</a></center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
$this->registerJs("
    $('.imgPop').click(function(){
        var title = $(this).data('title');
        var src = $(this).data('src');
        $('#myModal').modal('show');
        $('#myModal .modal-title').text(title);
        $('#myModal .modal_report_src').attr('src',src);
        $('#myModal .modal_download').attr('href',src);
    });
", \yii\web\View::POS_END);
?>

<?php
$this->registerJs("
    $('.uploadreport').on('click', function(){
        var aid = $(this).data('id');
        $('#modal_doctor_appointment_id').val(aid);
        $('#UploadReport').modal('show');

    });
    ", \yii\web\View::POS_END, 'select-picker');
