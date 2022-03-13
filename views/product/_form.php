<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\ProductHelper;
use yii\helpers\BaseUrl;
use kartik\tree\TreeView;
use kartik\file\FileInput;
use yii\helpers\Url;
use dosamigos\fileupload\FileUpload;
use app\helpers\AppHelper;

\app\assets\SelectAsset::register($this);
\app\assets\DatePickerAsset::register($this);
\app\assets\DataTableAsset::register($this);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
app\assets\DateRangePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
$this->registerCssFile(yii\helpers\BaseUrl::home() . "css/fileinput.css");
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(yii\helpers\BaseUrl::home() . "css/magnific-popup.css");
$this->registerJsFile(yii\helpers\BaseUrl::home() . "js/jquery.magnific-popup.js", ['depends' => [yii\web\JqueryAsset::className()]]);

$firstScreen = '';
//$productDetailsForm = 'display: none';
//$goBtn = 'display: none';
if (!$model->isNewRecord) {
    $firstScreen = 'display: none';
    $productDetailsForm = '';
    $goBtn = '';

    if (!empty($model->productModels)) {
        $productModelSelect = [];
        foreach ($model->productModels as $pm) {
            array_push($productModelSelect, $pm->model_year_id);
        }
        $model->model_id = $productModelSelect;
    }
} else {
    $firstScreen = '';
    $productDetailsForm = 'display: none';
    $model->free_delivery = 1;
}
?>
<style>
    .datepicker-dropdown {
        max-width: 225px;
    }
</style>
<div class="product-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?php
        //if ($model->isNewRecord) {
        ?>
        <div id="firstScreen" style="<?php echo $firstScreen ?>">
            <div class="col-md-12" style="<?php //echo $goBtn                                                                                                                                 
                                            ?>">
                <a href="javascript:;" onclick="product.showDetailsForm()" class="text text-info">Go to product details <i class="fa fa-arrow-right"></i></a>
                <hr />
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'attribute_set_id')->dropDownList(ProductHelper::getAttributeSetList(), [
                    'prompt' => 'Please select',
                    'class' => 'select2 form-control',
                    'onchange' => 'product.getAttributeValues(this.value)'
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'type')->dropDownList(['S' => 'Simple', 'G' => 'Grouped',], [
                    'prompt' => 'Please select',
                    'class' => 'select2 form-control',
                    'onchange' => 'product.showProductDetailsForm()'
                ])
                ?>
            </div>
        </div>
        <div id="productDetailsForm" style="<?php echo $productDetailsForm ?>">
            <div class="col-md-12">
                <?php
                ?>
                <a href="javascript:;" onclick="product.showFirstScreen()" class="text text-info"><i class="fa fa-arrow-left"></i> Back to product setting</a>
                <hr />
                <?php
                //}
                ?>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a id="tab1" href="#tab_1" data-toggle="tab">General</a></li>
                        <li><a id="tab2" href="#tab_2" data-toggle="tab">Images</a></li>
                        <?php
                        $catStyle = '';
                        ?>
                        <li><a style="<?php echo $catStyle ?>" id="tab7" href="#tab_7" data-toggle="tab">Categories</a></li>
                        <?php
                        $apStyle = 'display: none';
                        if (!$model->isNewRecord) {
                            if ($model->type == "G") {
                                $apStyle = '';
                            }
                        }
                        ?>
                        <li class="associated-product" style="<?php echo $apStyle ?>">
                            <a id="tab6" href="#tab_6" data-toggle="tab">Associated product</a>
                        </li>
                        <li><a id="tab4" href="#tab_4" data-toggle="tab">Related product</a></li>
                    </ul>
                </div>
            </div>
            <br clear="all" />
            <div class="tab-content" style="margin-top: 20px;">
                <div class="tab-pane active" id="tab_1">

                    <div class="col-md-6">
                        <?php echo $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'short_description_en')->textInput() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'short_description_ar')->textInput() ?>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'description_en')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'description_ar')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6" id="productQty">
                                <?= $form->field($model, 'specification_en')->textarea(['rows' => 6]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'specification_ar')->textarea(['rows' => 6]) ?>
                            </div>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'SKU')->textInput([
                                    'maxlength' => true,
                                    'readonly' => ((!$model->isNewRecord) && !empty($model->SKU)) ? true : false
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo $form->field($model, 'barcode')->textInput(['maxlength' => true, 'readonly' => ((!$model->isNewRecord) && !empty($model->barcode)) ? true : false]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php echo $form->field($model, 'supplier_barcode')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo $form->field($model, 'remaining_quantity')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?=
                                $form->field($model, 'start_date')->textInput(['class' => 'datepicker form-control'])
                                ?>

                            </div>
                            <div class="col-md-6">
                                <?=
                                $form->field($model, 'end_date')->textInput(['class' => 'datepicker form-control'])
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row" hidden>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'product_margin')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'regular_price')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'final_price')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $selected_stores = [];
                    if (!empty($model->storeProducts)) {
                        foreach ($model->storeProducts as $stores_for_product) {
                            $selected_stores[] = $stores_for_product->store_id;
                        }
                    }
                    $model->store_id = $selected_stores;
                    ?>
                    <div class="row" hidden>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php
                                /*$form->field($model, 'store_id')->dropDownList(ProductHelper::getAllStoreList(), [
                                    'class' => 'select2 form-control',
                                    'multiple' => 'multiple',
                                ])*/
                                ?>
                                <?= $form->field($model, 'store_id')->textInput(['maxlength' => true, 'value' => 1]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="attributes">
                                <?php
                                if (!$model->isNewRecord) {
                                    $productAttributeValues = [];
                                    foreach ($model->productAttributeValues as $productAttributeValue) {
                                        $productAttributeValues[] = $productAttributeValue->attribute_value_id;
                                    }
                                    if (!empty($model->attributeSet)) {
                                        foreach ($model->attributeSet->attributeSetGroups as $attributeGroup) {
                                            $tmp = [];
                                            $attributeValueModel = \app\models\AttributeValues::find()->where(['attribute_id' => $attributeGroup->attribute_id])->orderBy(['sort_order' => SORT_ASC])->all();
                                            foreach ($attributeValueModel as $attributeValue) {
                                                $tmp[$attributeValue->attribute_value_id] = $attributeValue->value_en;

                                                if (in_array($attributeValue->attribute_value_id, $productAttributeValues)) {
                                                    $model->attribute_values = $attributeValue->attribute_value_id;
                                                }
                                            }
                                ?>
                                            <div class="col-md-6">
                                                <?php
                                                echo $form->field($model, 'attribute_values')->dropDownList($tmp, [
                                                    'class' => 'select2 form-control',
                                                    'prompt' => 'Please Select',
                                                    'name' => 'Product[attribute_values][]',
                                                    'id' => 'product-attribute_values_' . $attributeGroup->attribute_id
                                                ])->label($attributeGroup->attribute0->name_en);
                                                ?>
                                            </div>
                                <?php
                                            $this->registerJs("$(window).on('load',function(){
                                                                    var myIntVal =  setInterval(function(){
                                                                    if (typeof $('#w0').data('yiiActiveForm') !== 'undefined') { 
                                                                       common.addvalidation('w0', 'product-attribute_values_" . $attributeGroup->attribute_id . "','Product[attribute_values][]', '.field-product-attribute_values_" . $attributeGroup->attribute_id . "', 'Please select the value.');
                                                                       clearInterval(myIntVal);
                                                                    } else { 
                                                                       console.log('form not initializing');
                                                                    }; 
                                                                }, 100);
                                                            })", \yii\web\View::POS_LOAD);
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?=
                                $form->field($model, 'brand_id')->dropDownList(ProductHelper::getBrandList(), [
                                    'prompt' => 'Please select',
                                    'class' => 'select2 form-control'
                                ])
                                ?>

                                <?=
                                $form->field($model, 'manufacturer_id')->dropDownList(ProductHelper::getManufacturerList(), [
                                    'prompt' => 'Please select',
                                    'class' => 'select2 form-control'
                                ])
                                ?>
                            </div>

                            <div class="col-md-6">
                                <?php
                                if (\Yii::$app->session['_eyadatAuth'] == 5) {
                                    echo $form->field($model, 'pharmacy_id')->dropDownList(ProductHelper::getPharmacyList(), [
                                        'class' => 'select2 form-control'
                                    ]);
                                } else {
                                    echo $form->field($model, 'pharmacy_id')->dropDownList(ProductHelper::getPharmacyList(), [
                                        'prompt' => 'Please select',
                                        'class' => 'select2 form-control'
                                    ]);
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div>
                                    <?php
                                    echo $form->field($model, 'is_featured')->checkbox();
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <?php
                                    echo $form->field($model, 'is_trending')->checkbox();
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div>
                                    <?php
                                    if (!$model->isNewRecord) {
                                        echo $form->field($model, 'show_as_individual')->checkbox();
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
                <div class="tab-pane" id="tab_2">
                    <div class="col-md-12">
                        <div class="form-group field-product-images">
                            <label class="control-label" for="product-images">Images(1000 x 1000)</label>
                            <div class="file-input file-input-ajax-new">
                                <div class="file-preview">
                                    <div class="file-drop-zone">
                                        <div class="file-preview-thumbnails">
                                            <div id="uploaded_img">
                                                <?php
                                                if (!$model->isNewRecord) {
                                                    if (!empty($model->productImages)) {
                                                        $k = 0;
                                                        foreach ($model->productImages as $proimg) {
                                                            $checkImgInGalery = 0;
                                                ?>
                                                            <div class="file-preview-frame krajee-default  file-preview-initial file-sortable kv-preview-thumb" id="preview-<?php echo $k ?>">
                                                                <div class="kv-file-content">
                                                                    <img id="target<?php echo $k ?>" class="magpop uploadedImg" src="<?php echo AppHelper::getUploadUrl() . $proimg->image ?>" alt="img" style="height: 160px;">
                                                                </div>
                                                                <div class="file-thumbnail-footer">
                                                                    <div class="file-actions">
                                                                        <div class="file-footer-buttons">
                                                                            <?php
                                                                            if ($checkImgInGalery == 0) {
                                                                            ?>
                                                                                <button onclick="product.deleteUploadedImg(<?php echo $k ?>)" type="button" class="kv-file-remove btn btn-xs btn-default" title="Remove file">
                                                                                    <i class="glyphicon glyphicon-trash text-danger"></i>
                                                                                </button>
                                                                            <?php
                                                                            } else {
                                                                            ?>
                                                                                <button onclick="product.removePhotoGalleryImg(<?php echo $k ?>)" type="button" class="kv-file-remove btn btn-xs btn-default" title="Remove file">
                                                                                    <i class="glyphicon glyphicon-trash text-danger"></i>
                                                                                </button>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                            <button onclick="product.viewUploadedImg(<?php echo $k ?>)" type="button" class="kv-file-zoom btn btn-xs btn-default" title="View Details">
                                                                                <i class="glyphicon glyphicon-zoom-in"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input name="Product[images][]" value="<?php echo $proimg->image ?>" type="hidden">
                                                            </div>
                                                        <?php
                                                            $k++;
                                                        }
                                                    } else {
                                                        ?>
                                                        <div class="file-drop-zone-title">Drag &amp; drop files here …</div>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="file-drop-zone-title">Drag &amp; drop files here …</div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <br clear="all" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="color: #dd4b39" id="upload_error" class="help-block"></div>
                            <div id="progress" class="progress m-t-xs full progress-small" style="display: none">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>

                            <?php
                            echo FileUpload::widget([
                                'name' => 'Product[img]',
                                'url' => [
                                    'product/image-upload'
                                ],
                                'options' => [
                                    'accept' => 'image/*',
                                    'multiple' => 'multiple'
                                ],
                                'clientOptions' => [
                                    'dataType' => 'json',
                                    'maxFileSize' => 2000000,
                                ],
                                'clientEvents' => [
                                    'fileuploaddone' => 'function(e, data) {
                                                    if(data.result.files.error==""){
                                                        $(".file-drop-zone-title").hide();
                                                        var l = $("#uploaded_img .uploadedImg").length;                                   
                                                        var img = \'<div class="file-preview-frame krajee-default  file-preview-initial file-sortable kv-preview-thumb" id="preview-\'+l+\'">\n\
<div class="kv-file-content">\n\
<img id="target\'+l+\'" class="magpop uploadedImg" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="height: 160px;">\n\
</div>\n\
<div class="file-thumbnail-footer">\n\
<!--<span class="file-drag-handle drag-handle-init text-info" title="Move / Rearrange">\n\
<i class="glyphicon glyphicon-menu-hamburger"></i>\n\
</span>-->\n\
<div class="file-actions">\n\
<div class="file-footer-buttons">\n\
<button onclick="product.deleteUploadedImg(\'+l+\')" type="button" class="kv-file-remove btn btn-xs btn-default" title="Remove file">\n\
<i class="glyphicon glyphicon-trash text-danger"></i>\n\
</button>\n\
<button onclick="product.viewUploadedImg(\'+l+\')" type="button" class="kv-file-zoom btn btn-xs btn-default" title="View Details">\n\
<i class="glyphicon glyphicon-zoom-in"></i>\n\
</button>\n\
</div>\n\
</div>\n\
</div>\n\
<input type="hidden" name="Product[images][]" value="\'+data.result.files.name+\'"/>\n\
</div>\';
                                                        $("#uploaded_img").append(img);                                  
                                                        $("#progress .progress-bar").attr("style","width: 0%;");
                                                        $("#progress").hide();
                                                    }
                                                    else{
                                                        $("#progress .progress-bar").attr("style","width: 0%;");
                                                        $("#progress").hide();
                                                        var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                                        $("#uploaded_img_error").html(errorHtm);
                                                        setTimeout(function(){
                                                            $("#uploaded_img_error span").remove();
                                                        },3000)
                                                    }
                                                }',
                                    'fileuploadfail' => 'function(e, data) {
                                                    console.log(e);
                                                    console.log(data);
                                                }',
                                    'fileuploadprogressall' => "function (e, data) {
                                            var progress = parseInt(data.loaded / data.total * 100, 10);
                                            $('#progress').show();
                                            $('#progress .progress-bar').css(
                                                'width',
                                                progress + '%'
                                            );
                                         }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_4">
                    <div class="col-md-12">
                        <table id="example" class="display select table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>
                                        <input name="select_all" value="1" id="example-select-all" type="checkbox">
                                    </th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Final price</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Final price</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="tab_6">
                    <div class="col-md-12">
                        <button type="button" onclick="product.quickSimpleProductForm(<?php echo $model->product_id ?>)" class="btn btn-sm btn-info">Quick create simple products</button>
                        <br clear="all" />
                        <hr />
                        <table id="associatedProduct" class="display select table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>
                                        <input name="select_all" value="1" id="associated-product-select-all" type="checkbox">
                                    </th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Final price</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Final price</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab_7">
                    <?php
                    echo \app\modules\treemanager\TreeViewWithoutForm::widget([
                        'query' => \app\models\Category::find()
                            ->addOrderBy('root, lft')
                            ->where(['is_deleted' => 0, 'active' => 1, 'type' => 'P']),
                        'headingOptions' => ['label' => 'Categories'],
                        'rootOptions' => ['label' => '<span class="text-primary">Root</span>'],
                        'fontAwesome' => true,
                        'isAdmin' => true,
                        'id' => 'categoryTree',
                        'displayValue' => 1,
                        'softDelete' => true,
                        'showCheckbox' => true,
                        'cacheSettings' => ['enableCache' => false]
                    ]);
                    ?>
                    <span class="clearfix"></span>
                    <div class="col-md-12">
                        <label id="category_error" style="color: #d62c1a"></label>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div id="dynamicElement">
        <div id="associatedElement">
            <?php
            if (!$model->isNewRecord) {
                if (!empty($model->associatedProducts)) {
                    foreach ($model->associatedProducts as $rp) {
            ?>
                        <input id="ap_<?php echo $rp->child_id; ?>" type="hidden" name="ap_id[]" value="<?php echo $rp->child_id; ?>" />
            <?php
                    }
                }
            }
            ?>
        </div>
        <div id="relatedElement">
            <?php
            if (!$model->isNewRecord) {
                if (!empty($model->relatedProducts)) {
                    foreach ($model->relatedProducts as $rp) {
            ?>
                        <input id="rp_<?php echo $rp->related_id; ?>" type="hidden" name="rp_id[]" value="<?php echo $rp->related_id; ?>" />
            <?php
                    }
                }
            }
            ?>
        </div>
    </div>
    <p>&nbsp;</p>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="modal fade" id="quick-simple-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Quick simple product creation</h4>
            </div>
            <div class="modal-body" id="quickSimpleForm">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<br clear="all" />


<script type="application/javascript">
    var associatedProductUrl = '<?php echo BaseUrl::home(); ?>product/get-list?type=A&exclude=<?php echo $model->product_id; ?>';
</script>

<?php
if ($model->isNewRecord) {
    $startDay = 'today';
    $relatedIds = 0;
    $associatedIds = 0;
} else {
    if (!empty($model->relatedProducts)) {
        $relatedList = [];
        foreach ($model->relatedProducts as $rp) {
            array_push($relatedList, $rp->related_id);
        }
        $relatedIds = implode(',', $relatedList);
    } else {
        $relatedIds = 0;
    }

    if (!empty($model->associatedProducts)) {
        $associatedList = [];
        foreach ($model->associatedProducts as $rp) {
            array_push($associatedList, $rp->child_id);
        }
        $associatedIds = implode(',', $associatedList);
    } else {
        $associatedIds = 0;
    }

    $startDay = '';
}
$this->registerJs("$('.datepicker').datepicker({
        autoclose: true,
        format: \"yyyy-mm-dd\",
        startDate: \"" . $startDay . "\",
        todayBtn: 'linked',
        todayHighlight: true});", \yii\web\View::POS_END, 'date-picker');

$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker-2');
$this->registerJs("$('.select3').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker-3');
$this->registerJs("$('.select5').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker-dyn');
$this->registerJs("$('.select4').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker-tag');

$this->registerJs("$(document).ready(function (){
   var relatedId = [" . $relatedIds . "];
   var table = $('#example').DataTable({
      'processing':true,
      'serverSide':true,
      'ajax': {
         'url': '" . BaseUrl::home() . "product/get-list?type=R&exclude=" . $model->product_id . "' 
      },
      'bSort' : false,
      'columnDefs': [{
         'targets': 0,
         'searchable': false,
         'orderable': false,
         'className': 'dt-body-left',
         'render': function (data, type, full, meta){
             $('#relatedElement input').each(function(i,v){
                var selectedVal = parseInt(v.value);
                //console.log(selectedVal);
                if(jQuery.inArray(selectedVal, relatedId) ==-1){
                   relatedId.push(selectedVal);
                }
            });
             var product_id = $('<div/>').text(data).html();
             var checkId = parseInt(product_id);
             var str = '';
             if(jQuery.inArray(checkId, relatedId) !==-1){
                str = 'checked=\"checked\"';
             }
             return '<input '+str+' type=\"checkbox\" name=\"related_id[]\" value=\"' + product_id + '\">';
         }
      }],
      'order': [[1, 'asc']]
   });

   // Handle click on \"Select all\" control
   $('#example-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type=\"checkbox\"]', rows).prop('checked', this.checked);
      if ($('#example-select-all').is(':checked')) {
        $('#relatedElement').html('');
        $('#example tbody :checkbox:checked').each(function (i) {
            var htm = '<input id=\"rp_'+$(this).val()+'\" type=\"hidden\" name=\"rp_id[]\" value=\"'+$(this).val()+'\"/>';
            $('#relatedElement').append(htm);
        })
      }
      else{
         $('#relatedElement').html('');
      }
   });

   // Handle click on checkbox to set state of \"Select all\" control
   $('#example tbody').on('change', 'input[type=\"checkbox\"]', function(){
      var id = $(this).val();
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If \"Select all\" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of \"Select all\" control 
            // as 'indeterminate'
            el.indeterminate = true;
         }
         $('#rp_'+id).remove();
         relatedId.splice($.inArray(id, relatedId), 1);
      }
      else{
         var hasId = $('#relatedElement').find('#rp_'+id).length;
         if(hasId < 1)
         {
            var htm = '<input id=\"rp_'+id+'\" type=\"hidden\" name=\"rp_id[]\" value=\"'+id+'\"/>';
            $('#relatedElement').append(htm);
         }
      }
   });

});", \yii\web\View::POS_END, 'related-product-list');

$this->registerJs("$(document).ready(function (){
   var associatedId = [" . $associatedIds . "];
   var table = $('#associatedProduct').DataTable({
      'processing':true,
      'serverSide':true,
      'ajax': {
         'url': associatedProductUrl
      },
      'bSort' : false,
      'columnDefs': [{
         'targets': 0,
         'searchable': false,
         'orderable': false,
         'className': 'dt-body-left',
         'render': function (data, type, full, meta){             
            $('#associatedElement input').each(function(i,v){
                var selectedVal = parseInt(v.value);
                //console.log(selectedVal);
                if(jQuery.inArray(selectedVal, associatedId) ==-1){
                   associatedId.push(selectedVal);
                }
            });
             //console.log(associatedId);
             var product_id = $('<div/>').text(data).html();
             var checkId = parseInt(product_id);
             var str = '';
             if(jQuery.inArray(checkId, associatedId) !==-1){
                str = 'checked=\"checked\"';
             }
             return '<input '+str+' type=\"checkbox\" name=\"associated_product_id[]\" value=\"' + product_id + '\">';
         }
      }],
      'order': [[1, 'asc']]
   });

   // Handle click on \"Select all\" control
   $('#associated-product-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type=\"checkbox\"]', rows).prop('checked', this.checked);      
      if ($('#associated-product-select-all').is(':checked')) {
        $('#associatedElement').html('');
        $('#associatedProduct tbody :checkbox:checked').each(function (i) {
            var htm = '<input id=\"ap_'+$(this).val()+'\" type=\"hidden\" name=\"ap_id[]\" value=\"'+$(this).val()+'\"/>';
            $('#associatedElement').append(htm);
        })
      }
      else{
         $('#associatedElement').html('');
      }
   });

   // Handle click on checkbox to set state of \"Select all\" control
   $('#associatedProduct tbody').on('change', 'input[type=\"checkbox\"]', function(){
      var id = $(this).val();
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#associated-product-select-all').get(0);
         // If \"Select all\" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of \"Select all\" control 
            // as 'indeterminate'
            el.indeterminate = true;
         }
         $('#ap_'+id).remove();
         associatedId.splice($.inArray(id, associatedId), 1);
      }
      else{
         var hasId = $('#associatedElement').find('#ap_'+id).length;
         if(hasId < 1)
         {
            var htm = '<input id=\"ap_'+id+'\" type=\"hidden\" name=\"ap_id[]\" value=\"'+id+'\"/>';
            $('#associatedElement').append(htm);
         }
      }
   });

});", \yii\web\View::POS_END, 'associated-product-list');

if (!$model->isNewRecord) {
    $startDate = isset($model->start_date) ? $model->start_date : date("Y-m-d", strtotime('monday this week'));
    $endDate = isset($model->end_date) ? $model->end_date : date("Y-m-d", strtotime("sunday this week"));
} else {
    $startDate = date("Y-m-d", strtotime('monday this week'));
    $endDate = date("Y-m-d", strtotime("sunday this week"));
}

$this->registerJs("$('#product-startdate_to_enddate').daterangepicker({
    autoUpdateInput: false,
    locale: {
      format: 'YYYY-MM-DD'
    },   
    timePicker: false,
    startDate:'" . $startDate . "',
    endDate:'" . $endDate . "',
});
$('input[id=\"product-startdate_to_enddate\"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
});

$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayBtn: 'linked',
    startDate: '0d',
    todayHighlight: true}).on('changeDate', function(e){
        if (e.target.id == 'product-start_date') {
            $('#product-end_date').datepicker('setStartDate', new Date($(this).val()));
            $('#product-end_date').val('');
        }
    });
", \yii\web\View::POS_END, 'date-range-picker');

$validateCategoryNImg = 0;
if ($model->isNewRecord) {
    $validateCategoryNImg = 1;
} else if (!$model->isNewRecord && $model->show_as_individual == 1) {
    $validateCategoryNImg = 1;
}
if ($validateCategoryNImg == 1) {
    $this->registerJs("
        product.showProductDetailsForm();
        $('body').on('beforeSubmit', 'form#w0', function () {
           
           var cat_length = $('.kv-selected').length;
            if(cat_length==0)
            {
                $('.tab-pane').removeClass('active');
                $('#tab_7').addClass('active');
                
                $('#w0 .nav-tabs li').removeClass('active');
                $('#tab7').parent().addClass('active');
                
                $('#category_error').html('Category is mandatory');
                return false;
            }
            
        });
        $('.fa-square-o').click(function(){
            $('#category_error').html('');
        });", \yii\web\View::POS_END);
}
