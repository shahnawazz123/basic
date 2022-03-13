<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\detail\DetailView;
use yii\helpers\BaseUrl;

\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\DoctorAppointments */

$this->title = 'Appointment ID -' . $model->doctor_appointment_id . ' (' . $model->name . ')';
$this->params['breadcrumbs'][] = ['label' => 'Report Request', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->doctor_appointment_id]];
\yii\web\YiiAsset::register($this);

$btnStr = '';
$this->registerJsFile(BaseUrl::home() . 'js/tagsinput.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$btnStr .= '{view} ';

/*$btnStr .= '{delete} ';*/

?>
<link href="<?= BaseUrl::home(); ?>/css/tagsinput.css" rel="stylesheet">
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull-right">

                    <?= Html::a('Back', ['view?id=' . $model->doctor_appointment_id], ['class' => 'btn btn-danger']); ?>

                </p>
                <div class="clearfix"></div>
                <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'reports')->textInput(['maxlength' => true, 'data-role' => 'tagsinput', 'required' => true])->label('Enter your request <span class="text-error">*</span>') ?>

                        <?php //echo $form->field($model, 'reports')->textInput(['maxlength' => true,'class'=>'select2 form-control multiple','required'=>true])->label('Enter your request <span class="text-error">*</span>')
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Submit Request', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",tags:\"true\"});", \yii\web\View::POS_END, 'select-picker');
?>