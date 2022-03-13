<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $model app\models\Attributes */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(BaseUrl::home() . 'js/attribute.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="attributes-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true, 'dir' => 'rtl',]) ?>
        </div>
        <div class="col-md-12">
            <a id="addValueBtn" onclick="attribute.addMoreAttributeValue()" href="javascript:;" class="btn btn-info pull pull-right"><i class="fa fa-plus"></i>Add value</a>
        </div>
        <div id="ajaxValue">
            <?php
            if (!$model->isNewRecord) {
                $count = 0;
                foreach ($model->attributeValues as $value) {
                    ?>
                    <div id="ajxVal<?php echo $count; ?>" class="values">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="col-md-12 no-padding-right">
                                    <label class="control-label" for="attributevalues_value_en">Value in English</label>
                                    <div class="form-group field-attributevalues_value_en-<?php echo $count; ?> required">
                                        <?php
                                        echo Html::textInput('AttributeValues[value_en][]', $value->value_en, [
                                            'maxlength' => true,
                                            'class' => 'form-control attributevalues-value_en',
                                            'id' => 'attributevalues-value_en-' . $count,
                                        ])
                                        ?>
                                        <?php
                                        echo Html::hiddenInput('AttributeValues[attribute_value_id][]', $value->attribute_value_id, [
                                            'id' => 'attributevalues-attribute_value_id-' . $count,
                                        ])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12 no-padding-right">
                                <div class="col-md-12 no-padding">
                                    <label class="control-label" for="attributevalues_value_en">Value in Arabic</label>
                                    <div class="form-group field-attributevalues_value_ar-<?php echo $count; ?> required">
                                        <?php
                                        echo Html::textInput('AttributeValues[value_ar][]', $value->value_ar, [
                                            'maxlength' => true,
                                            'class' => 'form-control attributevalues-value_ar',
                                            'dir' => 'rtl',
                                            'id' => 'attributevalues-value_ar-' . $count,
                                        ])
                                        ?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="col-md-12">
                                    <label class="control-label" for="attributevalues_value_btn">&nbsp;</label><br clear="all"/>
                                    <a onclick="attribute.deleteAttributeValueFormAjax(<?php echo $count; ?>,<?php echo $value->attribute_value_id; ?>)" href="javascript:;" class="btn btn-md btn-danger pull pull-right"><i class="glyphicon glyphicon-remove-circle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $this->registerJs('$(window).on("load",function(){
                        var myIntVal =  setInterval(function(){
                            if (typeof $(\'#w0\').data(\'yiiActiveForm\') !== \'undefined\') { 
                                jQuery(\'#w0\').yiiActiveForm("add", {
                                    "id": \'attributevalues-value_en-' . $count . '\',
                                    "name": "AttributeValues[value_en][]",
                                    "container": ".field-attributevalues_value_en-' . $count . '",
                                    "input": \'#attributevalues-value_en-' . $count . '\',
                                    "validate": function (attribute, value, messages, deferred, $form) {
                                        yii.validation.required(value, messages, {"message": "Value in English cannot be blank"});
                                    }
                                });
                                jQuery(\'#w0\').yiiActiveForm("add", {
                                    "id": \'attributevalues-value_ar-' . $count . '\',
                                    "name": "AttributeValues[value_ar][]",
                                    "container": ".field-attributevalues_value_ar-' . $count . '",
                                    "input": \'#attributevalues-value_ar-' . $count . '\',
                                    "validate": function (attribute, value, messages, deferred, $form) {
                                        yii.validation.required(value, messages, {"message": "Value in Arabic cannot be blank"});
                                    }
                                });
                               clearInterval(myIntVal);
                            } else { 
                               console.log(\'form not initializing\');
                            }; 
                        }, 100);
                    })', \yii\web\View::POS_END);
                    $count++;
                }
            }
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
if ($model->isNewRecord) {
    $this->registerJs('$(window).on("load",function(){
        $("#addValueBtn").trigger("click");
    })', \yii\web\View::POS_END);
}

$this->registerJs("
    $('body').on('beforeSubmit', 'form#w0', function (e) {
        var form = $(this);
        if (form.find('.has-error').length) {
            return false;
        }
        else{
            var hasDuplicates = 0;
            var uniqueValueEnArr = [];
            $('.attributevalues-value_en').each(function(){
                var valueEn = $(this).val().trim();
                if(uniqueValueEnArr[valueEn]){
                    hasDuplicates = 1;
                    var str = $(this).attr('id');
                    var res = str.replace('-', '_'); 
                    var field_class = '.field-'+res;
                    $(field_class).removeClass('has-success');
                    $(field_class).addClass('has-error');
                } else{
                    uniqueValueEnArr[valueEn] = valueEn;
                    var str = $(this).attr('id');
                    var res = str.replace('-', '_'); 
                    var field_class = '.field-'+res;
                    $(field_class).removeClass('has-error');
                    $(field_class).addClass('has-success');
                }
            });
            
            var uniqueValueArArr = [];
            $('.attributevalues-value_ar').each(function(){
                var valueAr = $(this).val().trim();
                if(uniqueValueArArr[valueAr]){
                    hasDuplicates = 1;
                    var str = $(this).attr('id');
                    var res = str.replace('-', '_'); 
                    var field_class = '.field-'+res;
                    $(field_class).removeClass('has-success');
                    $(field_class).addClass('has-error');
                } else{
                    uniqueValueArArr[valueAr] = valueAr;
                    var str = $(this).attr('id');
                    var res = str.replace('-', '_'); 
                    var field_class = '.field-'+res;
                    $(field_class).removeClass('has-error');
                    $(field_class).addClass('has-success');
                }
            });
        }
        
        if(hasDuplicates == 1){
           swal('','Duplicate attribute values are not allowed.');
           return false;
        }
    });
", \yii\web\View::POS_END);
