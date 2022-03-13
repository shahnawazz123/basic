<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $model app\models\Admin */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(BaseUrl::home() . 'js/permission.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style>
    ul.tree-make {
        padding: 0px;
    }

    li {
        list-style-type: none;
    }

    .chk {
        height: 25px;
        margin: 10px;
        font-weight: 600;
        color: #6a6c6f;
        width: 160px;
        float: left;
    }

    .mcollapse {
        padding: 8px 8px 3px 8px;
        margin-bottom: 5px;
        background: #f1f4f6;
    }

    .mexpand {
        padding: 8px 8px 3px 8px;
        margin-bottom: 5px;
        border: 1px solid #ccc;
    }

    ul.tree-model li:before,
    ul.tree-year li:before,
    ul.tree-engine li:before {
        border-top: none;
    }
</style>

<div class="admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <label>
                Image
            </label>
            <br />

            <?php
            echo FileUpload::widget([
                'name' => 'Admin[image]',
                'url' => [
                    'upload/admin-image'
                ],
                'options' => [
                    'accept' => 'image/*',
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
                                            
                                            var img = \'<br/><img class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:256px;"/>\';
                                            $("#logo_preview").html(img);
                                            $(".field-admin-image input[type=hidden]").val(data.result.files.name);$("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
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
                <?php
                if (!$model->isNewRecord) {
                    if ($model->image != "") {
                ?>
                        <br /><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="width:256px;" />
                <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>

            <?php
            if ($model->isNewRecord) {
                echo $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]);
            }
            ?>
        </div>
        <div class="col-md-12">
            <ul class="tree-make">
                <h4>Admin Permissions</h4>
                <hr>
                <?php
                if (!empty($result)) {
                    foreach ($result as $row) {
                        $countModuleRole = \app\helpers\PermissionHelper::countUserModuleRole($id, $row['auth_module_id'], 'A');
                        //debugPrint($countModuleRole);
                        $checkAll = 0;
                        if (sizeof($row['items']) == $countModuleRole) {
                            $checkAll = 1;
                        }
                ?>
                        <li style="list-style-type: none;">
                            <div class="mcollapse" id="module<?php echo $row['auth_module_id']; ?>">
                                <input class="i-checks mIcheck" <?php echo ($checkAll == 0) ? '' : 'checked="checked"' ?> id="chk<?php echo $row['auth_module_id']; ?>" type="checkbox" name="module_list[]" value="<?php echo $row['auth_module_id']; ?>" onclick="permission.checkUncheckCheckbox(<?php echo $row['auth_module_id']; ?>)" />
                                <a style="width: 98%;" class="pull pull-right" onclick="permission.showHideTreeNode(this,<?php echo $row['auth_module_id']; ?>)" href="javascript:;"> <span class="pull" style="margin: 1px 0px 0 4px;display: inline-block"><b><?php echo $row['auth_module_name']; ?></b></span>
                                    <i class="fa fa-plus-square-o fa-2x pull pull-right"></i>
                                </a>

                                <ul class="tree-engine" id="tree-engine-194" style="display:none">
                                    <?php
                                    if (!empty($row['items'])) {
                                    ?>
                                        <div class="checkbox no-top-padding">
                                            <ul style="margin: 0px;padding: 0px;">
                                                <?php
                                                foreach ($row['items'] as $itm) {
                                                    $hasRole = \app\helpers\PermissionHelper::checkUserHasRole($id, $itm['auth_item_id'], 'A');
                                                ?>
                                                    <li class="chk">
                                                        <input class="i-checks iIcheck" <?php echo ($hasRole == 0) ? '' : 'checked="checked"' ?> id="itemChk<?php echo $itm['auth_item_id']; ?>" data-module_id="<?php echo $row['auth_module_id']; ?>" type="checkbox" name="item_list[]" value="<?php echo $itm['auth_item_id']; ?>" onclick="permission.checkUncheckItemCheckbox(<?php echo $row['auth_module_id']; ?>,<?php echo $itm['auth_item_id']; ?>)" />
                                                        <span class="pull" style="margin: 1px 0px 0 10px;position: absolute"><?php echo $itm['auth_item_name']; ?></span>
                                                    </li>
                                                    <!--<br clear="all"/>-->
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <br clear="all" /><br clear="all" />
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
$(".mIcheck").on(\'ifChecked\', function (e) {
    permission.checkUncheckCheckbox($(this).val());
});
$(".mIcheck").on(\'ifUnchecked\', function (e) {
    permission.checkUncheckCheckbox($(this).val());
});
$(".iIcheck").on(\'ifChecked\', function (e) {
    var mid = $(this).data("module_id");
    permission.checkUncheckItemCheckbox(mid,$(this).val());
});
$(".iIcheck").on(\'ifUnchecked\', function (e) {
    var mid = $(this).data("module_id");
    permission.checkUncheckItemCheckbox(mid,$(this).val());
});
', yii\web\View::POS_END);
