<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

app\assets\CmsEditorAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Cms */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .field-cms-content_ar .note-editing-area {
        direction: rtl;
    }

    .note-popover .popover-content .dropdown-menu, .panel-heading.note-toolbar .dropdown-menu {
        min-width: 190px;
    }

    .note-popover .popover-content .btn-group .note-table, .panel-heading.note-toolbar .btn-group .note-table {
        min-width: 190px;
    }


</style>
<div class="cms-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>

            <?=
            $form->field($model, 'content_en')->textarea([
                'class' => 'summernote'
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'title_ar')->textInput(['maxlength' => true, 'dir' => 'rtl']) ?>
            <?=
            $form->field($model, 'content_ar')->textarea([
                'class' => 'summernoteAr',
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("$(function () {

        $('.summernote').summernote({
            height: 200, 
          codemirror: {  
              theme: 'monokai',
              toolbar: [
                ['headline', ['style']],
                ['style', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                ['textsize', ['fontsize']],
                ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']],
            ]
      },
            
        });  
        
        
        $('.summernoteAr').summernote({
            lang: 'ar-AR',
            height: 200,
           codemirror: {  
              theme: 'monokai',
              toolbar: [
                ['headline', ['style']],
                ['style', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                ['textsize', ['fontsize']],
                ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']],
            ]
      },
        }); 
        
        
       /* $('.summernoteAr').summernote({
            lang: 'ar-AR',
            height: 200,
            toolbar: [
                ['headline', ['style']],
                ['style', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                ['textsize', ['fontsize']],
                ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']],
            ]
        });  */
        
         
        
    });", \yii\web\View::POS_END);
?>

