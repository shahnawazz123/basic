<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?= $form->field($model, 'gender') ?>

    <?= $form->field($model, 'dob') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'is_phone_verified') ?>

    <?php // echo $form->field($model, 'is_email_verified') ?>

    <?php // echo $form->field($model, 'is_social_register') ?>

    <?php // echo $form->field($model, 'social_register_type') ?>

    <?php // echo $form->field($model, 'device_token') ?>

    <?php // echo $form->field($model, 'device_type') ?>

    <?php // echo $form->field($model, 'device_model') ?>

    <?php // echo $form->field($model, 'app_version') ?>

    <?php // echo $form->field($model, 'os_version') ?>

    <?php // echo $form->field($model, 'push_enabled') ?>

    <?php // echo $form->field($model, 'newsletter_subscribed') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
