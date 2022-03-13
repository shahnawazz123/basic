<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div id="ajxVal<?php echo $count; ?>" class="values">
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="col-md-12 no-padding-right">
                <label class="control-label" for="attributevalues_value_en">Value in English</label>
                <div class="form-group field-attributevalues_value_en-<?php echo $count; ?> required">
                    <?php
                    echo Html::activeTextInput($model, 'value_en[]', [
                        'maxlength' => true,
                        'class' => 'form-control attributevalues-value_en',
                        'id' => 'attributevalues-value_en-' . $count,
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
                    echo Html::activeTextInput($model, 'value_ar[]', [
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
                <a onclick="attribute.deleteAttributeValueFormAjax(<?php echo $count; ?>)" href="javascript:;" class="btn btn-md btn-danger pull pull-right"><i class="glyphicon glyphicon-remove-circle"></i></a>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery('#w0').yiiActiveForm("add", {
    "id": 'attributevalues-value_en-<?php echo $count; ?>',
    "name": "AttributeValues[value_en][]",
    "container": ".field-attributevalues_value_en-<?php echo $count; ?>",
    "input": '#attributevalues-value_en-<?php echo $count; ?>',
    "validate": function (attribute, value, messages, deferred, $form) {
    yii.validation.required(value, messages, {"message": "Value in English cannot be blank"});
    }
    });
    jQuery('#w0').yiiActiveForm("add", {
    "id": 'attributevalues-value_ar-<?php echo $count; ?>',
    "name": "AttributeValues[value_ar][]",
    "container": ".field-attributevalues_value_ar-<?php echo $count; ?>",
    "input": '#attributevalues-value_ar-<?php echo $count; ?>',
    "validate": function (attribute, value, messages, deferred, $form) {
    yii.validation.required(value, messages, {"message": "Value in Arabic cannot be blank"});
    }
    });
</script>