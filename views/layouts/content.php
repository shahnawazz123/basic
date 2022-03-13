<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\widgets\Alert;
use yii\helpers\BaseUrl;
?>

<!-- Main Wrapper -->
<div id="wrapper">
    <div class="content">
        <?= Alert::widget() ?>
        <?php
        if ($this->context->action->controller->id != 'dashboard') {
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-body">
                            <div id="snackbar"></div>
                            <div id="snackbarFailed"></div>
                            <?php if (isset($this->blocks['content-header'])) { ?>
                                <h1><?= $this->blocks['content-header'] ?></h1>
                                <?php
                            }
                            else {
                                ?>
                                <h1>
                                    <?php
                                    if ($this->title !== null) {
                                        echo \yii\helpers\Html::encode($this->title);
                                    }
                                    else {
                                        echo \yii\helpers\Inflector::camel2words(
                                                \yii\helpers\Inflector::id2camel($this->context->module->id)
                                        );
                                        echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                                    }
                                    ?>
                                </h1>
                            <?php } ?>

                            <div id="hbreadcrumb" class="pull-right">
                                <?php
                                echo
                                Breadcrumbs::widget([
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                    'tag' => 'ol',
                                    'options' => [
                                        'class' => 'hbreadcrumb breadcrumb'
                                    ]
                                ])
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>


        <?php echo $content; ?>

        <div id="blueimp-gallery" class="blueimp-gallery">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>
    </div>
    <!-- Footer-->
    <footer class="footer">
        <span class="pull-right">
            Copyright &copy; <?php echo date('Y') ?>
        </span>
        Eyadat
    </footer>


</div>

<div class="global-loader">
    <div class="main-content">
        <div class="loading">
            <img src="<?php echo BaseUrl::home() ?>images/loading-bars.svg" alt="loading"/>
        </div>
    </div>
</div>