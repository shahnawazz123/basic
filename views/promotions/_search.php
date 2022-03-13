<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PromotionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promotions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'promotion_id') ?>

    <?= $form->field($model, 'title_en') ?>

    <?= $form->field($model, 'title_ar') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'end_date') ?>

    <?php // echo $form->field($model, 'promo_type') ?>

    <?php // echo $form->field($model, 'promo_count') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'promo_for') ?>

    <?php // echo $form->field($model, 'minimum_order') ?>

    <?php // echo $form->field($model, 'shipping_included') ?>

    <?php // echo $form->field($model, 'registration_start_date') ?>

    <?php // echo $form->field($model, 'registration_end_date') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
