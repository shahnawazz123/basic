<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SettingsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'setting_id') ?>

    <?= $form->field($model, 'contact_email') ?>

    <?= $form->field($model, 'support_email') ?>

    <?= $form->field($model, 'support_phone') ?>

    <?= $form->field($model, 'smtp_host') ?>

    <?php // echo $form->field($model, 'smtp_username') ?>

    <?php // echo $form->field($model, 'smtp_password') ?>

    <?php // echo $form->field($model, 'smtp_port') ?>

    <?php // echo $form->field($model, 'enable_order_multiple_vendors') ?>

    <?php // echo $form->field($model, 'delivery_charge') ?>

    <?php // echo $form->field($model, 'delivery_interval') ?>

    <?php // echo $form->field($model, 'notify_for_quantity_below') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
