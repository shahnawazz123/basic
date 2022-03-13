<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'boutique_id') ?>

    <?= $form->field($model, 'admin_id') ?>

    <?= $form->field($model, 'name_en') ?>

    <?= $form->field($model, 'name_ar') ?>

    <?php // echo $form->field($model, 'short_description_en') ?>

    <?php // echo $form->field($model, 'short_description_ar') ?>

    <?php // echo $form->field($model, 'description_en') ?>

    <?php // echo $form->field($model, 'description_ar') ?>

    <?php // echo $form->field($model, 'SKU') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'regular_price') ?>

    <?php // echo $form->field($model, 'final_price') ?>

    <?php // echo $form->field($model, 'base_currency_id') ?>

    <?php // echo $form->field($model, 'remaining_quantity') ?>

    <?php // echo $form->field($model, 'posted_date') ?>

    <?php // echo $form->field($model, 'updated_date') ?>

    <?php // echo $form->field($model, 'is_featured') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'new_from_date') ?>

    <?php // echo $form->field($model, 'new_to_date') ?>

    <?php // echo $form->field($model, 'meta_title_en') ?>

    <?php // echo $form->field($model, 'meta_title_ar') ?>

    <?php // echo $form->field($model, 'meta_keywords_en') ?>

    <?php // echo $form->field($model, 'meta_keywords_ar') ?>

    <?php // echo $form->field($model, 'meta_description_en') ?>

    <?php // echo $form->field($model, 'meta_description_ar') ?>

    <?php // echo $form->field($model, 'show_as_individual') ?>

    <?php // echo $form->field($model, 'brand_id') ?>

    <?php // echo $form->field($model, 'attribute_set_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
