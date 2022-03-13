<?php

use yii\helpers\Html;

?>
<style>
    .submit-button {
        margin-right: 40px;
        margin-top: 10px;
    }
</style>
<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version" style="padding: 0px;">
        <img style="position: relative; margin-top: 10px; max-height: 40px;" src="<?php echo \yii\helpers\BaseUrl::home() ?>images/header-logo.png" alt="logo"/>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo" style="padding-top: 2px;">
            <span class="text-primary">
                <img style="width: 165px; top: 5px; position: relative;" src="<?php echo \yii\helpers\BaseUrl::home() ?>images/header-logo.png" alt="logo"/>
            </span>
        </div>
        <div class="mobile-menu">
        </div>
        <div class="pull-right">
            <ul class="nav navbar-nav no-borders">
                <li class="dropdown">
                    <?php
                    if(!Yii::$app->user->isGuest) {
                        //$name = Yii::$app->user->identity->name;
                        if (\Yii::$app->session['_eyadatAuth'] == 1) {
                            $name = ucfirst(Yii::$app->user->identity->name);
                        } elseif (\Yii::$app->session['_eyadatAuth'] == 2) {
                            $name = ucfirst(Yii::$app->user->identity->name_en);
                        }elseif (\Yii::$app->session['_eyadatAuth'] == 3) {
                            $name = ucfirst(Yii::$app->user->identity->name_en);
                        }elseif (\Yii::$app->session['_eyadatAuth'] == 4) {
                            $name = ucfirst(Yii::$app->user->identity->name_en);
                        }elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                            $name = ucfirst(Yii::$app->user->identity->name_en);
                        }elseif (\Yii::$app->session['_eyadatAuth'] == 8) {
                            $name = ucfirst(Yii::$app->user->identity->name_en);
                        }
                        ?>
                        <?= Html::beginForm(['/site/logout'], 'post') ?>
                        <?= Html::submitButton('<i class="pe-7s-upload pe-rotate-90"></i> ' .$name , ['class' => 'btn btn-default submit-button']) ?>
                        <?= Html::endForm() ?>
                        <?php
                    }
                    ?>
                </li>
            </ul>
        </div>
    </nav>
</div>