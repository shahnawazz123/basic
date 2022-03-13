<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Clinics */

$controller = $this->context->action->controller->id;
$this->title = 'Create ' . ucwords($controller);
$this->params['breadcrumbs'][] = ['label' => ucwords($controller), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>