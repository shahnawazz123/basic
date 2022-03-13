<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$method = $this->context->action->id;

$url = yii\helpers\BaseUrl::home() . 'images/add_product_sample.xlsx';
$title = 'Import Products';

$this->title = Yii::t('app', $title);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php
                $form = \yii\widgets\ActiveForm::begin([
                            'options' => [
                                'enctype' => 'multipart/form-data'
                            ]
                        ])
                ?>
                <?= $form->field($model, 'file')->fileInput() ?>
                <small><a class="btn btn-info" download href="<?php echo $url; ?>">Download Sample Excel file </a></small>
                <div class="form-group">
                    <br/>
                    <?php
                    echo Html::submitButton(Yii::t('app', 'Upload'), ['class' => 'btn btn-sm btn-primary']);
                    ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
$(\'form#w0\').submit(function() {
  $(this).find("button[type=\'submit\']").prop(\'disabled\',true);
});', \yii\web\View::POS_END);
