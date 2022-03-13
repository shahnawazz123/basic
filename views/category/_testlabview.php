<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2017
 * @package   yii2-tree-manager
 * @version   1.0.8
 */
use kartik\form\ActiveForm;
use kartik\tree\Module;
use kartik\tree\TreeView;
use kartik\tree\models\Tree;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use dosamigos\fileupload\FileUpload;


$this->registerJsFile(yii\helpers\BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(yii\helpers\BaseUrl::home() . 'js/category.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
/**
 * @var View       $this
 * @var Tree       $node
 * @var ActiveForm $form
 * @var array      $formOptions
 * @var string     $keyAttribute
 * @var string     $nameAttribute
 * @var string     $iconAttribute
 * @var string     $iconTypeAttribute
 * @var string     $iconsList
 * @var string     $action
 * @var array      $breadcrumbs
 * @var array      $nodeAddlViews
 * @var mixed      $currUrl
 * @var boolean    $showIDAttribute
 * @var boolean    $showFormButtons
 * @var boolean    $allowNewRoots
 * @var string     $nodeSelected
 * @var array      $params
 * @var string     $keyField
 * @var string     $nodeView
 * @var string     $noNodesMessage
 * @var boolean    $softDelete
 * @var string     $modelClass
 */
?>

<?php
/**
 * SECTION 1: Initialize node view params & setup helper methods.
 */
?>
<?php
extract($params);
$session = Yii::$app->has('session') ? Yii::$app->session : null;

// parse parent key
if ($noNodesMessage) {
    $parentKey = '';
} elseif (empty($parentKey)) {
    $parent = $node->parents(1)->one();
    $parentKey = empty($parent) ? '' : Html::getAttributeValue($parent, $keyAttribute);
}

// tree manager module
$module = TreeView::module();

// active form instance
$form = ActiveForm::begin(['action' => $action, 'options' => $formOptions]);

// helper function to show alert
$showAlert = function ($type, $body = '', $hide = true) {
    $class = "alert alert-{$type}";
    if ($hide) {
        $class .= ' hide';
    }
    return Html::tag('div', '<div>' . $body . '</div>', ['class' => $class]);
};

// helper function to render additional view content
$renderContent = function ($part) use ($nodeAddlViews, $params, $form) {
    if (empty($nodeAddlViews[$part])) {
        return '';
    }
    $p = $params;
    $p['form'] = $form;
    return $this->render($nodeAddlViews[$part], $p);
};
?>

<?php
/**
 * SECTION 2: Initialize hidden attributes. In case you are extending this and creating your own view, it is mandatory
 * to set all these hidden inputs as defined below.
 */
?>
<?= Html::hiddenInput('treeNodeModify', $node->isNewRecord) ?>
<?= Html::hiddenInput('parentKey', $parentKey) ?>
<?= Html::hiddenInput('currUrl', $currUrl) ?>
<?= Html::hiddenInput('modelClass', $modelClass) ?>
<?= Html::hiddenInput('nodeSelected', $nodeSelected) ?>

<?php
/**
 * SECTION 3: Hash signatures to prevent data tampering. In case you are extending this and creating your own view, it
 * is mandatory to include this section below.
 */
?>
<?php
$security = Yii::$app->security;
$id = $node->isNewRecord ? null : $node->$keyAttribute;

// save signature
$dataToHash = !!$node->isNewRecord . $currUrl . $modelClass;
echo Html::hiddenInput('treeSaveHash', $security->hashData($dataToHash, $module->treeEncryptSalt));

// manage signature
if (array_key_exists('depth', $breadcrumbs) && $breadcrumbs['depth'] === null) {
    $breadcrumbs['depth'] = '';
}
$icons = is_array($iconsList) ? array_values($iconsList) : $iconsList;
$dataToHash = $modelClass . !!$isAdmin . !!$softDelete . !!$showFormButtons . !!$showIDAttribute .
        $currUrl . $nodeView . $nodeSelected . Json::encode($formOptions) .
        Json::encode($nodeAddlViews) . Json::encode($icons) . Json::encode($breadcrumbs);
echo Html::hiddenInput('treeManageHash', $security->hashData($dataToHash, $module->treeEncryptSalt));

// remove signature
$dataToHash = $modelClass . $softDelete;
echo Html::hiddenInput('treeRemoveHash', $security->hashData($dataToHash, $module->treeEncryptSalt));

// move signature
$dataToHash = $modelClass . $allowNewRoots;
echo Html::hiddenInput('treeMoveHash', $security->hashData($dataToHash, $module->treeEncryptSalt));
?>

<?php
/**
 * BEGIN VALID NODE DISPLAY
 */
?>
<?php if (!$noNodesMessage): ?>
    <?php
    $isAdmin = ($isAdmin == true || $isAdmin === "true"); // admin mode flag
    $inputOpts = [];                                      // readonly/disabled input options for node
    $flagOptions = ['class' => 'kv-parent-flag'];         // node options for parent/child

    /**
     * the primary key input field
     */
    if ($showIDAttribute) {
        $options = ['readonly' => true];
        if ($node->isNewRecord) {
            $options['value'] = Yii::t('kvtree', '(new)');
        }
        $keyField = $form->field($node, $keyAttribute)->textInput($options);
    } else {
        $keyField = Html::activeHiddenInput($node, $keyAttribute);
    }

    /**
     * initialize for create or update
     */
    $depth = ArrayHelper::getValue($breadcrumbs, 'depth');
    $glue = ArrayHelper::getValue($breadcrumbs, 'glue');
    $activeCss = ArrayHelper::getValue($breadcrumbs, 'activeCss');
    $untitled = ArrayHelper::getValue($breadcrumbs, 'untitled');
    $name = $node->getBreadcrumbs($depth, $glue, $activeCss, $untitled);
    if ($node->isNewRecord && !empty($parentKey) && $parentKey !== TreeView::ROOT_KEY) {
        /**
         * @var Tree $modelClass
         * @var Tree $parent
         */
        $depth = empty($breadcrumbsDepth) ? null : intval($breadcrumbsDepth) - 1;
        if ($depth === null || $depth > 0) {
            $parent = $modelClass::findOne($parentKey);
            $name = $parent->getBreadcrumbs($depth, $glue, null) . $glue . $name;
        }
    }
    if ($node->isReadonly()) {
        $inputOpts['readonly'] = true;
    }
    if ($node->isDisabled()) {
        $inputOpts['disabled'] = true;
    }
    if ($node->isLeaf()) {
        $flagOptions['disabled'] = true;
    }
    ?>
    <?php
    /**
     * SECTION 4: Setup form action buttons.
     */
    ?>
    <div class="kv-detail-heading">

        <div class="kv-detail-crumbs">
            <?php
            echo ($node->isNewRecord) ? "Create new category" : $name . " ({$node->category_id})";
            ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php
    /**
     * SECTION 5: Setup alert containers. Mandatory to set this up.
     */
    ?>
    <div class="kv-treeview-alerts">
        <?php
        if ($session && $session->hasFlash('success')) {
            echo $showAlert('success', $session->getFlash('success'), false);
        } else {
            echo $showAlert('success');
        }
        if ($session && $session->hasFlash('error')) {
            echo $showAlert('danger', $session->getFlash('error'), false);
        } else {
            echo $showAlert('danger');
        }
        echo $showAlert('warning');
        echo $showAlert('info');
        ?>
    </div>

    <?php
    /**
     * SECTION 6: Additional views part 1 - before all form attributes.
     */
    ?>
    <?php
    echo $renderContent(Module::VIEW_PART_1);
    ?>

    <?php
    /**
     * SECTION 7: Basic node attributes for editing.
     */
    ?>

    <div class="row">

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a id="tab1" href="#tab_1" data-toggle="tab">General information</a></li>
                
            </ul>
        </div>
        <br clear="all"/>    
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">    
                <div class="col-sm-12" style="display: none;">
                    <?= $keyField ?>
                    <?= Html::activeHiddenInput($node, $iconTypeAttribute) ?>                
                </div>

                <div class="col-md-6">
                    <?= $form->field($node, 'name_en')->textInput($inputOpts) ?>
                    <?php
                    echo Html::activeHiddenInput($node, 'category_type', ['value' => 'T'])
                    ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($node, 'name_ar')->textInput(['dir' => 'rtl'] + $inputOpts) ?>
                </div>
                
                <div class="clearfix"></div>

                <div class="col-md-6">
                    <div class="form-group field-category-icon">
                        <label class="control-label">
                            App Banner in English(500 X 500)
                        </label>
                    </div>

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Category[icon]',
                        'url' => [
                            '/upload/common?attribute=Category[icon]'
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
                                        $('#progress_1').show();
                                        $('#progress_1 .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                            'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            var img = \'<br/><img id="target3" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:100%;"/>\';
                                            $("#icon_preview").html(img);
                                            $(".field-category-icon input[type=hidden]").val(data.result.files.name);
                                            $(".field-category-icon input[type=hidden]").blur();
                                            $("#progress_1 .progress-bar").attr("style","width: 0%;");
                                            $("#progress_1").hide();
                                        }
                                        else{
                                           $("#progress_1 .progress-bar").attr("style","width: 0%;");
                                           $("#progress_1").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#icon_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#icon_preview span").remove();
                                           },3000)
                                        }
                                    }',
                        ],
                    ]);
                    ?>

                    <div id="progress_1" class="progress m-t-xs full progress-small" style="display: none;">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="icon_preview">
                        <?php
                        if (!$node->isNewRecord) {
                            if ($node->icon != "") {
                                ?>
                                <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $node->icon ?>" alt="img" style="width:100%"/>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php echo $form->field($node, 'icon')->hiddenInput()->label(false); ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group field-category-icon_ar">
                        <label class="control-label">
                            App Banner in Arabic(500 X 500)
                        </label>
                    </div>

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Category[icon_ar]',
                        'url' => [
                            '/upload/common?attribute=Category[icon_ar]'
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
                                        $('#progress_2').show();
                                        $('#progress_2 .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                            'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            var img = \'<br/><img id="target4" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:100%"/>\';
                                            $("#icon_preview_ar").html(img);
                                            $(".field-category-icon_ar input[type=hidden]").val(data.result.files.name);
                                            $(".field-category-icon_ar input[type=hidden]").blur();
                                            $("#progress_2 .progress-bar").attr("style","width: 0%;");
                                            $("#progress_2").hide();
                                        }
                                        else{
                                           $("#progress_2 .progress-bar").attr("style","width: 0%;");
                                           $("#progress_2").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#icon_preview_ar").html(errorHtm);
                                           setTimeout(function(){
                                               $("#icon_preview_ar span").remove();
                                           },3000)
                                        }
                                    }',
                        ],
                    ]);
                    ?>

                    <div id="progress_2" class="progress m-t-xs full progress-small" style="display: none;">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="icon_preview_ar">
                        <?php
                        if (!$node->isNewRecord) {
                            if ($node->icon_ar != "") {
                                ?>
                                <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $node->icon_ar ?>" alt="img" style="width:100%"/>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php echo $form->field($node, 'icon_ar')->hiddenInput()->label(false); ?>
                </div>
                <div class="clearfix"></div>

                <div class="col-md-6" hidden>
                    <label>
                        Web Banner in English (500 X 500)
                    </label>
                    <br/>

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Category[image]',
                        'url' => [
                            '/upload/common?attribute=Category[image]'
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
                                        $('#progress_3').show();
                                        $('#progress_3 .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                            'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            var img = \'<br/><img id="target3" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:100%;"/>\';
                                            $("#image_preview").html(img);
                                            $(".field-category-image input[type=hidden]").val(data.result.files.name);
                                            $("#progress_3 .progress-bar").attr("style","width: 0%;");
                                            $("#progress_3").hide();
                                        }
                                        else{
                                           $("#progress_3 .progress-bar").attr("style","width: 0%;");
                                           $("#progress_3").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#image_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#image_preview span").remove();
                                           },3000)
                                        }
                                    }',
                        ],
                    ]);
                    ?>

                    <div id="progress_3" class="progress m-t-xs full progress-small" style="display: none;">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="image_preview">
                        <?php
                        if (!$node->isNewRecord) {
                            if ($node->image != "") {
                                ?>
                                <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $node->image ?>" alt="img" style="width:100%"/>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php echo $form->field($node, 'image')->hiddenInput()->label(false); ?>
                </div>
                <div class="col-md-6" hidden>
                    <label>
                        Web Banner in Arabic (500 X 500)
                    </label>
                    <br/>

                    <?php
                    echo FileUpload::widget([
                        'name' => 'Category[image_ar]',
                        'url' => [
                            '/upload/common?attribute=Category[image_ar]'
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
                                        $('#progress_4').show();
                                        $('#progress_4 .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                            'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            var img = \'<br/><img id="target4" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="width:100%"/>\';
                                            $("#image_preview_ar").html(img);
                                            $(".field-category-image_ar input[type=hidden]").val(data.result.files.name);
                                            $("#progress_4 .progress-bar").attr("style","width: 0%;");
                                            $("#progress_4").hide();
                                        }
                                        else{
                                           $("#progress_4 .progress-bar").attr("style","width: 0%;");
                                           $("#progress_4").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#image_preview_ar").html(errorHtm);
                                           setTimeout(function(){
                                               $("#image_preview_ar span").remove();
                                           },3000)
                                        }
                                    }',
                        ],
                    ]);
                    ?>

                    <div id="progress_4" class="progress m-t-xs full progress-small" style="display: none;">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="image_preview_ar">
                        <?php
                        if (!$node->isNewRecord) {
                            if ($node->image_ar != "") {
                                ?>
                                <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $node->image_ar ?>" alt="img" style="width:100%"/>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php echo $form->field($node, 'image_ar')->hiddenInput()->label(false); ?>
                </div>
                <div class="clearfix"></div>


                <div class="col-md-6">
                    <?php echo $form->field($node, 'show_in_home')->checkbox(); ?>
                </div>
                <div class="clearfix"></div>
                
            </div>

        </div>


    </div>
    <div class="form-group">
        <?php if (empty($inputOpts['disabled']) || ($isAdmin && $showFormButtons)): ?>
            <div class="pull-left">
                <button type="submit" class="btn btn-info" title="<?= Yii::t('kvtree', 'Save') ?>">
                    <i class="glyphicon glyphicon-floppy-disk"></i> Save
                </button>
                <button type="reset" class="btn btn-default" title="<?= Yii::t('kvtree', 'Reset') ?>">
                    <i class="glyphicon glyphicon-repeat"></i> Reset
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php
    /**
     * SECTION 8: Additional views part 2 - before admin zone.
     */
    ?>
    <?= $renderContent(Module::VIEW_PART_2) ?>

    <?php
    /**
     * SECTION 9: Administrator attributes zone.
     */
    ?>


<?php endif; ?>
<?php
/**
 * END VALID NODE DISPLAY
 */
?>

<?php ActiveForm::end() ?>

<?php
/**
 * SECTION 13: Additional views part 5 accessible by all users after admin zone.
 */
?>
<?= $noNodesMessage ? $noNodesMessage : $renderContent(Module::VIEW_PART_5) ?>
<?php
/*if (!$node->isNewRecord) {
    if ($node->is_featured != 0) {
        $js = '$(document).ready(function(){
                var myIntVal =  setInterval(function(){
                    if (typeof $(\'#categoryTree-nodeform\').data(\'yiiActiveForm\') !== \'undefined\') { 
                       category.showHideFeaturedImageSection();
                       clearInterval(myIntVal);
                    } else { 
                       console.log(\'form not initializing\');
                    }; 
                }, 100);               

          })';
        $this->registerJs($js, yii\web\View::POS_END);
    }
}*/