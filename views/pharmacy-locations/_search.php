<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PharmacyLocationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pharmacy-locations-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pharmacy_location_id') ?>

    <?= $form->field($model, 'pharmacy_id') ?>

    <?= $form->field($model, 'latlon') ?>

    <?= $form->field($model, 'governorate_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'block') ?>

    <?php // echo $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'building') ?>

    <?php // echo $form->field($model, 'name_en') ?>

    <?php // echo $form->field($model, 'name_ar') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
