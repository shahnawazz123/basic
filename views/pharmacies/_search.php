<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PharmaciesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pharmacies-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pharmacy_id') ?>

    <?= $form->field($model, 'name_en') ?>

    <?= $form->field($model, 'name_ar') ?>

    <?= $form->field($model, 'image_en') ?>

    <?= $form->field($model, 'image_ar') ?>

    <?php // echo $form->field($model, 'minimum_order') ?>

    <?php // echo $form->field($model, 'is_free_delivery') ?>

    <?php // echo $form->field($model, 'is_featured') ?>

    <?php // echo $form->field($model, 'latlon') ?>

    <?php // echo $form->field($model, 'enable_login') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'governorate_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'block') ?>

    <?php // echo $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'building') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'shop_number') ?>

    <?php // echo $form->field($model, 'admin_commission') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
