<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use dosamigos\fileupload\FileUpload;

$this->registerCssFile(yii\helpers\BaseUrl::home() . "css/fileinput.css");
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(yii\helpers\BaseUrl::home() . "css/magnific-popup.css");
$this->registerJsFile(yii\helpers\BaseUrl::home() . "js/jquery.magnific-popup.js", ['depends' => [yii\web\JqueryAsset::className()]]);

$this->title = 'Bulk upload';
$this->params['breadcrumbs'][] = ['label' => 'Bulk upload', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <div class="product-form">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row">
                        <div class="tab-pane" id="tab_2">
                            <div class="col-md-12">
                                <div class="form-group field-product-images">
                                    <div class="alert alert-info">
                                        <p class="text-primary"><strong>Note:</strong></p>
                                        <p>Image has to be named with SKU & below mentioned criteria's. </p>
                                        <p>1. Image format should have SKU with underscore ( _ ) character. Eg: 665599 is a SKU. For uploading a single image for the SKU, the format should be  665599_1. 1, 2, 3, number sequence indicates the position of the image. </p>
                                        <p>2. If the SKU has multiple images the format will be 665599_1, 665599_2, 665599_3, 665599_4 and so on. </p>
                                    </div>
                                    <p></p>
                                    <label class="control-label" for="product-images">Images(1000  x 1000)</label>
                                    <div class="file-input file-input-ajax-new">
                                        <div class="file-preview">
                                            <div class="file-drop-zone">
                                                <div class="file-preview-thumbnails">
                                                    <div id="uploaded_img">
                                                        <div class="file-drop-zone-title">Drag &amp; drop files here
                                                            …
                                                        </div>
                                                    </div>
                                                    <br clear="all"/>
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
                                            'product/bulk-image-upload'
                                        ],
                                        'options' => [
                                            'accept' => 'image/*',
                                            'multiple' => 'multiple'
                                        ],
                                        'clientOptions' => [
                                            'dataType' => 'json',
                                            'maxFileSize' => 20000000,
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
                                                    <input type="hidden" name="Product[SKU][]" value="\'+data.result.files.sku+\'"/>\n\
                                                    <input type="hidden" name="Product[original_name][]" value="\'+data.result.files.original_name+\'"/>\n\
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

                    </div>

                    <p id="bulk_upload_error" style="color: red">
                    </p>

                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
        $('body').on('beforeSubmit', 'form#w0', function () { 
           var class_length = $('.file-preview-frame').length;
            if(class_length==0)
            { 
               $('#bulk_upload_error').html('Image is mandatory');
               setTimeout(function(){ 
                $('#bulk_upload_error').html('');
                }, 2500);
                
                return false;
            } 
        });
        ", \yii\web\View::POS_END);
?>


