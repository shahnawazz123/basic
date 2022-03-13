<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\field\FieldRange;
use kartik\detail\DetailView;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use app\helpers\AppHelper;

\app\assets\SelectAsset::register($this);
$this->title = 'Doctor Appointment Report';
$this->params['breadcrumbs'][] = $this->title;
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
$admin_commission = 0;
$allowExport = true;?>
<style>
    .kv-field-range input[type=text]{
        width: 70px;
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <div class="table table-responsive">
                    <?php 
                    $gridColumns = [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'label' => 'Payment Date',
                                'value' => function ($model) {
                                   return date('d M,Y', strtotime($model->payment->payment_date)).' at '.date('H:i A', strtotime($model->payment->payment_date));
                                }
                            ],
                            [
                                'attribute' => 'appointment_datetime',
                                'value' => function ($model) {
                                   return date('d M,Y', strtotime($model->appointment_datetime)).' at '.date('H:i A', strtotime($model->appointment_datetime));
                                }
                            ],
                            'appointment_number',
                            [
                                'attribute' => 'user_id',
                                'value' => function ($model) {
                                    return $model->user->first_name . ' ' . $model->user->last_name;
                                },
                                'filter'=>false,
                            ],
                            [
                                'label' => 'Doctor',
                                'value' => function ($model) {
                                    return $model->doctor->name_en;
                                }
                            ],
                            [
                                'label' => 'Clinic',
                                'value' => function ($model) {
                                    return (!empty($model->doctor->clinic)) ? $model->doctor->clinic->name_en : '';
                                }
                            ],
                            [
                                'label' => 'Total Payment',
                                'value' => function ($model) {
                                    return number_format($model->amount,3).' KWD';
                                }
                            ],
                            [
                                'label' => 'Commission %',
                                'value' => function ($model) 
                                {
                                    return $model->admin_commission.'%';
                                }
                            ],
                            [
                                'label' => 'Admin Commission',
                                'value' => function ($model) 
                                {
                                    $admin_commission = ($model->amount * $model->admin_commission) / 100;
                                    return number_format($admin_commission,3).' KWD';
                                }
                            ],
                            
                            [
                                'label'=>'Paymode',
                                'value'=>function($model){
                                        if($model->payment->paymode == 'K')
                                            return 'K-Net';
                                        else if($model->payment->paymode =='C')
                                            return 'Pay Cash At Clinic';
                                        else if($model->payment->paymode == 'CC')
                                            return 'Visa Card';
                                        else if($model->payment->paymode == 'W')
                                            return 'Wallet Discount';
                                },
                            ],
                    ];
                    ?>
                    <?php echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,
                        'exportConfig' => [
                            ExportMenu::FORMAT_TEXT => false,
                            ExportMenu::FORMAT_PDF => false,
                            ExportMenu::FORMAT_CSV => false,
                            ExportMenu::FORMAT_HTML => false
                        ]
                    ]); ?>

                    <?= \kartik\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => $gridColumns,
                    ]); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>