<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\BaseUrl;

\app\assets\LoginAsset::register($this);

$this->title = 'Login';
$action = $this->context->action->id;
$title = 'ADMIN';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BaseUrl::home(); ?>favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo BaseUrl::home(); ?>favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BaseUrl::home(); ?>favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo BaseUrl::home(); ?>favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BaseUrl::home(); ?>favicon/favicon-16x16.png">
        <?= Html::csrfMetaTags() ?>
        <!-- Page title -->
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <style>
            .checkbox label{
                padding-left: 0px;
            }
            .form-group{margin-bottom: 0px;}
            .small{margin-bottom: 15px; margin-top: -5px;}
        </style>
    </head>
    <body class="blank">
        <?php $this->beginBody() ?>
        <div class="color-line"></div>
        <div class="login-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center m-b-md">
                        <img style="width: 100px; margin-bottom: 25px;" src="<?php echo BaseUrl::home() ?>images/header-logo.png" class="loginlogo">
                        <h3>LOGIN TO CONTROL PANEL</h3>
                    </div>

                    <div class="hpanel">
                        <div class="panel-body">
                            <?php
                            $form = ActiveForm::begin([
                                        'id' => 'loginForm',
                            ]);
                            ?>

                            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'E-mail address'])->label(false) ?>
                            <p class="help-block small">Your unique email</p>

                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>
                            <p class="help-block small">Your strong password</p>

                            <?=
                            $form->field($model, 'rememberMe')->checkbox(['class' => 'i-checks'
                            ])
                            ?>
                            <p class="help-block small">(if this is a private computer)</p>

                            <div class="form-group">
                                <?= Html::submitButton('Login', ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    Copyright &copy; <?= date('Y') ?> Eyadat
                </div>
            </div>
            <?php $this->endBody() ?>
    </body>

</html>
<?php $this->endPage() ?>
