<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\helpers\AppHelper;

\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\DoctorPrescriptions */
/* @var $form yii\widgets\ActiveForm */
$get = Yii::$app->request->queryParams;
$doctor_appointment_id = '';
$referred_pharmacy_id = '';
$product_name = '';
if (isset($get['ProductSearch']['doctor_appointment_id'])) {
    $doctor_appointment_id = $get['ProductSearch']['doctor_appointment_id'];
}
if (isset($get['ProductSearch']['pharmacy_id'])) {
    $referred_pharmacy_id = $get['ProductSearch']['pharmacy_id'];
}
if (isset($get['ProductSearch']['name'])) {
    $product_name = $get['ProductSearch']['name'];
}
$model->doctor_appointment_id = $doctor_appointment_id;
$model->referred_pharmacy_id = $referred_pharmacy_id;
$model1->name_en = $product_name;
?>

<div class="doctor-prescriptions-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?=$form->field($model, 'referred_pharmacy_id')->dropDownList(AppHelper::getPharmacyList(), [
                'prompt'=>'Select Pharmacy',
                'class' => 'form-control select2 referred_pharmacy_id',
            ])?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'doctor_appointment_id')->hiddenInput(['class'=>'doctor_appointment_id form-control'])->label('') ?>
        </div>
        <div class="col-md-12">
            <?php
            //echo "<pre>";print_r($model1);
            ?>
            <div class="col-md-12" style="margin-top:12px;">
                <h4>Medicine List</h4>
                <div class="pull pull-left">
                    <?php
                    $queryStr = '';
                    if (isset($_GET['ProductSearch'])) {
                        unset($_GET['ProductSearch']['page_size']);
                        foreach ($_GET['ProductSearch'] as $k => $ps) {
                            $queryStr .= 'ProductSearch[' . $k . ']=' . $ps . '&';
                        }
                    }
                    echo Html::activeTextInput($searchModel, 'page_size', [
                        'class' => 'form-control',
                        'placeholder' => 'Page Size',
                        'onchange' => "window.location.href=baseUrl+'doctor-prescriptions/create?" . $queryStr . "ProductSearch[page_size]='+this.value"
                    ]);
                    ?>
                </div>
            </div><br>
            <div id="product_error" class="text-danger"></div>
            <?php
                   // print_r($dataProvider);die;
                        if ($dataProvider != '') {?>
                          <?php $gridColumns = [
                              //['class' => 'yii\grid\SerialColumn'],
                                [
                                  'class' => 'yii\grid\CheckboxColumn',
                                  'checkboxOptions' => function ($model, $key, $index, $column) {
                                      //return $model->shop_order_id;
                                  },
                                ],
                                [
                                    'label' => 'Image',
                                    'value' => function ($model) {
                                        $image = $model->getProductImage($model->product_id);
                                        if (!empty($image)) {
                                            return '<a class="fancybox"  href="' . $image . '">
                                                        <img  src="' . $image . '" style="max-height: 100px;" />
                                                    </a>';
                                        } else {
                                            return '';
                                        }
                                    },
                                    'format' => 'raw',
                                    'filter' => false,
                                ],
                                [
                                    'label'=>'Product Name',
                                    'value'=> function ($model1) {
                                        return $model1->name_en;
                                    },
                                    'filter' => Html::activeTextInput($searchModel, 'name_en', ['class' => 'form-control']),
                                ],
                                [
                                    'label'=>'Brand',
                                    'value'=> function ($model1) {
                                        return $model1->brand->name_en;
                                    },
                                ],
                                [
                                    'label'=>'Stock',
                                    'value'=> function ($model1) {
                                        return $model1->remaining_quantity;
                                    },
                                ],
                                [
                                    'label'=>'Price',
                                    'value'=> function ($model1) {
                                        return $model1->final_price;
                                    },
                                ],
                                [
                                    'label'=>'Quantity',
                                    'value'=> function ($model1) {
                                        return '<input type="number" name="qty_'.$model1->product_id.'" class="form-control qtyinp" max="'.$model1->remaining_quantity.'" data-max="'.$model1->remaining_quantity.'" min="0">';
                                    },
                                    'format'=>'raw',
                                ],
                                [
                                    'label'=>'Instruction',
                                    'value'=> function ($model1) {
                                        return '<input type="text" name="instruction_'.$model1->product_id.'" class="form-control">';
                                    },
                                    'format'=>'raw',
                                ],
                              
                          ];

                          echo \kartik\grid\GridView::widget([
                              'dataProvider' => $dataProvider,
                              'columns' => $gridColumns,
                              'filterModel'  =>  $searchModel,
                          ]);
                        } else {
                            echo "Search";
                        }
                    ?>
        
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

/*$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
*/$this->registerJs('
        $(".qtyinp").keyup(function()
        {
            var max = $(this).data("max");
            if($(this).val() > max)
            {
                alert("You can not add qty more then stock");
                $(this).val(max);
            }
        });
          $(".referred_pharmacy_id").change(function(){
            var doctor_appointment_id = $(".doctor_appointment_id").val();
            var referred_pharmacy_id = $(".referred_pharmacy_id option:selected").val();
            url = baseUrl + "doctor-prescriptions/create?ProductSearch[doctor_appointment_id]="+doctor_appointment_id+"&ProductSearch[pharmacy_id]="+referred_pharmacy_id;
            if (url) { // require a URL
                window.location = url; // redirect
            }
            return false;
          });

          $("#productsearch-name_en").keyup(function(e)
          {
            if(e.keyCode == 13)
            {
                var doctor_appointment_id = $(".doctor_appointment_id").val();
                var referred_pharmacy_id = $(".referred_pharmacy_id option:selected").val();
                var product_name = $(this).val();
                url = baseUrl + "doctor-prescriptions/create?ProductSearch[doctor_appointment_id]="+doctor_appointment_id+"&ProductSearch[pharmacy_id]="+referred_pharmacy_id+"&ProductSearch[name]="+product_name;
                if (url) { // require a URL
                    window.location = url; // redirect
                }
                return false;
            }
          });
         ', \yii\web\View::POS_END, 'check');

$validateProduct = 0;
if ($model->isNewRecord) {
    $validateProduct = 1;
} elseif (!$model->isNewRecord && $model->show_as_individual == 1) {
    $validateProduct = 1;
}
if ($validateProduct == 1) {
    $this->registerJs("
        $('body').on('beforeSubmit', 'form#w0', function () {
           
           var product_length = $('.w1 input:checkbox:checked').length;
            if(product_length==0)
            {
                $('#product_error').html('Select atleast one product');
                return false;
            }else{
                return true;
            }
            
        });", \yii\web\View::POS_END);
}
