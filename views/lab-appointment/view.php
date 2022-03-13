<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use kartik\detail\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\LabAppointments */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lab Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$newTimeZone = new \DateTimeZone(Yii::$app->params['timezone']);
$allowCancel = true;
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
if ($model->is_cancelled == 0 && $minutes > \Yii::$app->params['allowed_cancel_minutes'] && $model->is_paid == 1) {
    $isCancelable = 1;
    $slotDateTime = $model->appointment_datetime;
    if (strtotime($slotDateTime) <= time()) {
        $isCancelable = 0;
    }
} else {
    $isCancelable = 0;
}

$user_id = $model->user_id;
$lab_type = $model->type;
$tests = $model->labAppointmentTests;
$labAdress = (!empty($model->lab)) ? $model->lab : '';

$payment = \app\models\Payment::find()
        ->where(['type_id' => $model->lab_appointment_id, 'type' => 'LA'])
        ->orderBy(['payment_id' => SORT_DESC])
        ->one();
?>
<style>
form .table tr th{ text-align: left !important; width: 25% !important;background: #ffffff !important;
    }
.panel-heading{
    padding: 19px;
  color: #fff !important;
  background: #4FB3CD !important;
  font-weight: 700 !important;
}
 .panel-title{
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
                    <?= ($allowCancel) ? (($isCancelable == 1) ? Html::a('Cancel booking', ['cancel-booking', 'id' => $model->lab_appointment_id], ['class' => 'btn btn-danger', 'onclick' => 'return confirm("Are you sure you want to cancel this booking?")']) : Html::a('Cancel booking', "javascript:;", ['class' => 'btn btn-danger', 'disabled' => 'disabled'])) : ""; ?>

                    <?php if ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date) 
                        { 
                            echo Html::a('Complete', ['lab-appointment/complete-url?id='.$model->lab_appointment_id], ['class' => 'btn btn-warning']);
                        } ?>
                    
                    <?php 
                    if($model->uploaded_report!=NULL)
                    {
                        $report = (!empty($model->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->uploaded_report) : '';
                        echo '<a href="'.$report.'" download title="download" class="btn btn-primary"><i class="fa fa-download"></i> Download Report</a>';
                    }

                    $today_date = strtotime(date('Y-m-d h:i:s'));
                    $appointment_datetime = strtotime($model->appointment_datetime);
                    // show only in upcoming and completed status
                    if((($appointment_datetime > $today_date && $model->is_cancelled == 0 && $model->is_completed == 0) || ($model->is_paid == 1 && $model->is_cancelled == 0 && $model->not_show == 0 && $model->is_completed == 1)))
                    {
                        echo '<a href="javascript:void(0)" class="uploadreport btn btn-primary" data-id="'.$model->lab_appointment_id.'" title="Upload Report"><i class="fa fa-upload"></i> Upload Report</a>';
                    }
                    ?>

                    <?php if ($model->not_show == 0 && $model->is_paid == 1 && $model->is_cancelled == 0 && $model->is_completed == 0 && $appointment_datetime > $today_date) 
                        { 
                            $url = "lab-appointment/complete";
                            echo Html::a('No Show', ['lab-appointment/not-show-url?id='.$model->lab_appointment_id], ['class' => 'btn btn-danger']);
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
                                
                                [
                                    'attribute' => 'lab_id',
                                    'value' => ($model->lab) ? $model->lab->name_en : "",
                                ],
                                [
                                    'attribute' => 'type',
                                    'value' => ($model->type == 'L') ? 'Lab' : 'Home',
                                ],
                                [
                                    'label' => 'Test',
                                    'value' => function($model) use($tests)
                                    {
                                        if(!empty($tests))
                                        {
                                            foreach($tests as $t)
                                            {
                                                return $t->test->name_en;
                                            }
                                        }
                                    },
                                ],
                                [
                                    'label'=>'Sample Collection Address',
                                    'value' => function($model) use($user_id,$lab_type,$labAdress)
                                    {
                                        $country_name = (isset($labAdress)) ? ((!empty($labAdress->area)) ? $labAdress->area->state->country->name_en : '') : '';

                                        $lab_address = 
                                            ((isset($labAdress->building)) ?$labAdress->building.',': '').
                                            ((isset($labAdress->street)) ?$labAdress->street.',': '').
                                            ((isset($labAdress->block)) ? $labAdress->block .',': '').
                                            ((isset($labAdress->area)) ? $labAdress->area->name_en.',' : '').
                                            ((isset($labAdress->governorate)) ? $labAdress->governorate->name_en.',' : '').
                                            ((isset($labAdress->governorate)) ? $labAdress->governorate->name_en.',' : '').$country_name;

                                        $userAddresses = \app\models\ShippingAddresses::find()
                                            ->where(['user_id' => $user_id, 'is_deleted' => 0,'is_default'=>1])
                                            ->one();
                                        if(!empty($userAddresses))
                                        {
                                            $block = (!empty($userAddresses->block_id)) ? \app\helpers\AppHelper::getBlockNameById($userAddresses->block_id, 'en') : "";
                                            $area = !empty($userAddresses->area) ? $userAddresses->area->name_en :"";
                                            $state = !empty($userAddresses->state) ?  $userAddresses->state->name_en:"";
                                            $country = !empty($userAddresses->country_id) ? $userAddresses->country->name_en : "";
                                            $user_address = 
                                            $user_address = $userAddresses->flat . ','. $userAddresses->building.','.$userAddresses->street.','.$block.','.$area.','.$state.','.$country.'<br>'.
                                                'Notes : '.$userAddresses->notes.'<br>'.
                                                'Contact No.'.$userAddresses->mobile_number;
                                        }else{
                                            $user_address = new \stdClass();
                                        }
                                        return ($lab_type == 'L') ? $lab_address : $user_address;
                                    },
                                    'format'=>'raw',
                                ],
                                [
                                    'attribute' => 'kid_id',
                                    'label'=>'Appointment booked for',
                                    'value' => ($model->kid) ? $model->kid->name_en : "Self",
                                ],
                                [
                                    'attribute' => 'appointment_datetime',
                                    'label' => 'Appointment Date',
                                    'value' => date('d M,Y', strtotime($model->appointment_datetime)),
                                ],
                                [
                                    'attribute' => 'appointment_datetime',
                                    'label'=>'Sample Collection Time',
                                    'value' => date('H:i A', strtotime($model->appointment_datetime)),
                                ],

                                [
                                    'attribute' => 'duration',
                                    'label'=>'Duration',
                                    'value' => $model->duration. " minutes",
                                ],
                                
                                
                            ],
                        ])
                        ?>
                    </div> 
                    
                    <?php
                    if (!empty($payment)) {
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
                                    'lab_amount',
                                    [
                                        'attribute' => 'is_paid',
                                        'value' => ($model->is_paid == 1) ? "Yes" : "No",
                                    ],
                                    [
                                        'attribute'=>'sub_total',
                                        'value'=> $model->sub_total.' KD',
                                    ],

                                    [
                                        'attribute'=>'discount_price',
                                        'value'=> $model->discount_price.' KD',
                                    ],
                                    [
                                        'attribute'=>'admin_commission',
                                        'value'=> $model->admin_commission. ' %',
                                    ],

                                    [
                                        'attribute'=>'amount',
                                        'value'=> $model->amount.' KD',
                                    ],

                                ],
                            ])
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    
                    <?php
                    if (!empty($payment)) {
                        ?>
                        <div class="col-md-12">
                            <?=
                            DetailView::widget([
                                'model' => $payment,
                                'buttons1' => '',
                                'panel' => [
                                    'heading' => 'Transcation Information',
                                    'type' => DetailView::TYPE_DEFAULT,
                                ],
                                'attributes' => [
                                    [
                                        'attribute'=>'paymode',
                                        'value'=> AppHelper::getPaymodeType($payment->paymode),
                                    ],
                                    'result',
                                    //'gross_amount',
                                    //'PaymentID',
                                    [
                                        'label'=>'Payment Date',
                                        'value' => date('d M,Y', strtotime($payment->payment_date)).' at '.date('H:i A', strtotime($payment->payment_date)),
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

                    <?php if($model->uploaded_report!=NULL)
                    {?>
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
                                            <td><?=$model->report_title_en?></td>
                                            <td><?=$model->report_title_ar?></td>
                                            <td><?=$model->report_upload_date?></td>
                                            <td>
                                                <?php 
                                                $report = (!empty($model->uploaded_report)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->uploaded_report) : '';
                                                ?>
                                                <a href="<?=$report;?>" download title="download" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> Download Report</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="LabReport" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      
      <div class="modal-body">
            <div class="msg"></div>
            <?php $form = ActiveForm::begin(); ?>
            <input type="hidden" name="LabAppointment[lab_appointment_id]" id="modal_lab_appointment_id">
            <label>Title In English <span class="text text-danger">*</span></label>
            <input type="text" name="LabAppointment[report_title_en]" id="modal_report_title_en" class="form-control" required><br>

            <label>Title In Arabic <span class="text text-danger">*</span></label>
            <input type="text" name="LabAppointment[report_title_ar]" id="modal_report_title_ar" class="form-control" required>
            <br>

            <label>
                Upload Doctor Report (Extension .pdf only) <span class="text-danger">*</span>
            </label>
            <br/>
            <?php
            echo FileUpload::widget([
                'name' => 'LabAppointment[uploaded_report]',
                'url' => [
                    'upload/common?attribute=LabAppointment[uploaded_report]'
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
                                            $("#labs-uploaded_report").val(data.result.files.name);
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
            <input type="hidden" id="labs-uploaded_report">
            <div class="form-group"><br><Br>
                <button type="button" onclick="common.addLabReport()" class="btn btn-primary">Submit</button>
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
    $('.uploadreport').click(function(){
        var aid = $(this).data('id');
        $('#modal_lab_appointment_id').val(aid);
        $('#LabReport').modal('show');

    });
    ", \yii\web\View::POS_END, 'select-picker');