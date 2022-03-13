<?php

use yii\helpers\Html;
use yii\helpers\BaseUrl;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\BannerHelper;
use app\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Promotions */
/* @var $form yii\widgets\ActiveForm */
\app\assets\SelectAsset::register($this);
\app\assets\DatePickerAsset::register($this);

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile(BaseUrl::home() . 'js/promotions.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$linkList = [];
?>

<div class="promotions-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'title_ar')->textInput(['maxlength' => true,'dir'=>'rtl']) ?>
        </div><div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
             <?= $form->field($model, 'discount')->textInput() ?>
        </div><div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'promo_count')->textInput() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'minimum_order')->textInput() ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'start_date')->textInput(['class' => 'datepicker form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date')->textInput(['class' => 'datepicker form-control']) ?>
        </div><div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'promo_type')->dropDownList([ 'M' => 'Multiple', 'S' => 'Single', ], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>
        </div>
        <div class="col-md-6">
            <?php //echo $form->field($model, 'promo_for')->dropDownList([ 'P' => 'Product', 'B' => 'Brand','D'=>'Doctor','C'=>'Clinic','L'=>'Lab','U'=>'User' ], ['prompt' => 'Please Select', 'class' => 'form-control select2']) ?>

            <?php echo $form->field($model, 'promo_for')->dropDownList(BannerHelper::$bannerTypes, [
                            'prompt' => 'Please select',
                            'class' => 'select2 form-control',
                            'onchange' => 'promotions.getListByType(this.value)'
                        ]) ?>
                    <?php
                        $css = 'display: none';
                        $css1 = '';
                        if (!$model->isNewRecord) {
                            if ($model->promo_for == 'Ls') {
                                $css = '';
                                $css1 = "display: none";
                            } elseif ($model->promo_for == 'BS') {
                                $css = $css1 = "display: none";
                            }
                        }
                        ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <div id="linkContainer" style="<?php echo $css1 ?>">
                <?php
                $label = 'Promo For Name';
                $array = [];
                if (!$model->isNewRecord) {
                    if ($model->promo_for == 'C') {
                        $label = 'Clinic / Hospital list';
                        $linkList = app\helpers\AppHelper::getClinicsList();
                        if (!empty($model->promotionClinics)) {
                            $array = [];
                            foreach ($model->promotionClinics as $key => $item) {
                                $array[] = $item->clinic_id;
                            }
                            $model->link_id = $array;
                        }
                    }
                    elseif ($model->promo_for == 'D') {
                        $label = 'Doctor list';
                        $linkList = app\helpers\AppHelper::getDoctorsList();
                        if (!empty($model->promotionDoctors)) {
                            $array = [];
                            foreach ($model->promotionDoctors as $key => $item) {
                                $array[] = $item->doctor_id;
                            }
                            $model->link_id = $array;
                        }
                    }
                    elseif ($model->promo_for == 'L') {
                        $label = 'Labs list';
                        $linkList = app\helpers\AppHelper::getLabsList();
                        if (!empty($model->promotionLabs)) {
                            $array = [];
                            foreach ($model->promotionLabs as $key => $item) {
                                $array[] = $item->lab_id;
                            }
                            $model->link_id = $array;
                        }
                    }
                    elseif ($model->promo_for == 'F') {
                        $label = 'Pharmacy list';
                        $linkList = app\helpers\AppHelper::getPharmacyList();

                        if (!empty($model->promotionPharmacy)) {
                            $array = [];
                            foreach ($model->promotionPharmacy as $key => $item) {
                                $array[] = $item->pharmacy_id;
                            }
                            $model->link_id = $array;
                        }
                    }
                }

                

                echo $form->field($model, 'link_id')->dropDownList($linkList, [
                    'prompt' => 'Please select',
                    'class' => 'form-control select2',
                    'multiple'=>'multiple'
                ])->label($label)
                ?>

            </div>

        </div>
        <div class="clearfix"></div>
        <div class="col-md-6" hidden>
             <?= $form->field($model, 'shipping_included')->checkbox() ?>
        </div>
        
        
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>
<?php 
$this->registerJs("
$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayBtn: 'linked',
    startDate: '0d',
    todayHighlight: true}).on('changeDate', function(e){
        if (e.target.id == 'product-start_date') {
            $('#Promotions-end_date').datepicker('setStartDate', new Date($(this).val()));
            $('#Promotions-end_date').val('');
        }
    });

", \yii\web\View::POS_END, 'date-range-picker');
?>