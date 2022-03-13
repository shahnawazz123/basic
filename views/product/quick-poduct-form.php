<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\BaseUrl;
use app\helpers\ProductHelper;
use dosamigos\fileupload\FileUpload;
?>
<style>
    .fileinput-button{
        float: left;
    }
</style>
<div class="product-form">
    <div id="response"></div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'quick-create-form',
                'enableAjaxValidation' => true,
                'validationUrl' => Yii::$app->urlManager->createUrl(["product/validate"]),
    ]);
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'SKU')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php echo $form->field($model, 'supplier_barcode')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    if (!empty($pharmacy_id)) {
                        $model->pharmacy_id = $pharmacy_id;
                    }
                    echo $form->field($model, 'pharmacy_id')->dropDownList(ProductHelper::getPharmacyList(), [
                        'prompt' => 'Please select',
                        'class' => 'select7 pharmacy-select form-control'
                    ])
                    ?>

                    <?php
                    if (!empty($manufacturer_id)) {
                        $model->manufacturer_id = $manufacturer_id;
                    }
                    echo $form->field($model, 'manufacturer_id')->dropDownList(ProductHelper::getManufacturerList(), [
                        'prompt' => 'Please select',
                        'class' => 'select7 manufacturer-select form-control'
                    ])
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    if (!empty($brand_id)) {
                        $model->brand_id = $brand_id;
                    }
                    echo $form->field($model, 'brand_id')->dropDownList(ProductHelper::getBrandList(), [
                        'prompt' => 'Please select',
                        'class' => 'select7 brand-select form-control',
                    ])
                    ?>
                    <?= $form->field($model, 'store_id')->hiddenInput(['maxlength' => true])->label('') ?>
                    <?php 
                    /*echo $form->field($model, 'store_id')->dropDownList(ProductHelper::getAllStoreList(), [
                        'class' => 'select7 form-control',
                        'multiple' => 'multiple',
                    ])*/
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6" hidden>
            <?= $form->field($model, 'qf_cost_price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'qf_product_margin')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'qf_regular_price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'qf_final_price')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <?php
        $model->attribute_set_id = $attset;
        echo $form->field($model, 'attribute_set_id')->hiddenInput()->label(false);
        ?>
        <?php
        echo $result;
        ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label>
                Image(800x1200)
            </label>
            <br clear="all"/>

            <?php
            echo FileUpload::widget([
                'name' => 'Product[quick_product_image]',
                'url' => [
                    'upload/common?attribute=Product[quick_product_image]'
                ],
                'options' => [
                    'accept' => 'image/*',
                    'id' => 'quick_product_image'
                ],
                'clientOptions' => [
                    'dataType' => 'json',
                    'maxFileSize' => 2000000,
                ],
                'clientEvents' => [
                    'fileuploadprogressall' => "function (e, data) {
                                        var progress = parseInt(data.loaded / data.total * 100, 10);
                                        $('#progress_quick_product_image').show();
                                        $('#progress_quick_product_image .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                    'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            
                                            var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#quick_product_image_preview").html(img);
                                            $(".field-product-quick_product_image input[type=hidden]").val(data.result.files.name);$("#qpi_progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                        }
                                        else{
                                           $("#qpi_progress .progress-bar").attr("style","width: 0%;");
                                           $("#qpi_progress").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#quick_product_image_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#quick_product_image_preview span").remove();
                                           },3000)
                                        }
                                    }',
                ],
            ]);
            ?>

            <div id="qpi_progress" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <span class="clearfix"></span>
            <div id="quick_product_image_preview">
            </div>
            <?php echo $form->field($model, 'quick_product_image')->hiddenInput()->label(false); ?>
        </div>
    </div>

    <?php
    echo Html::hiddenInput('Product[qf-name_en]', null, [
        'id' => 'qf-product-name_en'
    ]);
    echo Html::hiddenInput('Product[qf-name_ar]', null, [
        'id' => 'qf-product-name_ar'
    ]);
    echo Html::hiddenInput('Product[qf-width]', null, [
        'id' => 'qf-product-width'
    ]);
    echo Html::hiddenInput('Product[qf-height]', null, [
        'id' => 'qf-product-height'
    ]);
    echo Html::hiddenInput('Product[qf-length]', null, [
        'id' => 'qf-product-length'
    ]);
    echo Html::hiddenInput('Product[qf-weight]', null, [
        'id' => 'qf-product-weight'
    ]);
    echo Html::hiddenInput('Product[show_as_individual]', null, [
        'id' => 'qf-show_as_individual'
    ]);
    echo Html::hiddenInput('Product[qf-sku]', null, [
        'id' => 'qf-product-sku'
    ]);
    ?>
    <p>&nbsp;</p>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'id' => 'quickSimple']) ?>
    </div>

    <!--    <div id="response2"></div>-->

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("$('#quick-create-form').on('beforeSubmit', function () {
    var name_en = $('#product-name_en').val();
    $('#qf-product-name_en').val(name_en);
    var name_ar = $('#product-name_ar').val();
    $('#qf-product-name_ar').val(name_ar);
    var width = $('#product-width').val();
    $('#qf-product-width').val(width);
    var height = $('#product-height').val();
    $('#qf-product-height').val(height);
    var length = $('#product-length').val();
    $('#qf-product-length').val(length);
    var weight = $('#product-weight').val();
    $('#qf-product-weight').val(weight);
    
    $('#qf-show_as_individual').val(1);
    var sku = $('#product-sku').val();
    $('#qf-product-sku').val(sku);
    
    //var supplier_id = $('#product-shop_id').val();
    //$('#qf-shop_id').val(supplier_id);
    
    var form = $(this);
    var formData = form.serialize();
    $('#globalLoader').show();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (response) {
           $('#globalLoader').hide();
            if(response.success==1){
               $('#response').html('<div class=\"alert alert-success\">'+response.msg+'</div>');
               //$('#response2').html('<div class=\"alert alert-success\">'+response.msg+'</div>');
               $('#quick-create-form')[0].reset();
               $('#product-quick_product_image').val('');
               $('#quick_product_image_preview').html('');
               $('.select6').select2(\"val\", \"\");
               setTimeout(function(){
                    $('#response').html('');
                    //$('#response2').html('');
               },4000);
               var htm = '<input id=\"ap_'+response.child_id+'\" type=\"hidden\" name=\"ap_id[]\" value=\"'+response.child_id+'\"/>';
               $('#associatedElement').append(htm);
               $('#associatedProduct').DataTable().ajax.reload();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
           $('.global-loader').hide();
           alert(jqXHR.responseText);
        }
    });
    return false;
});", \yii\web\View::POS_END);
?>

