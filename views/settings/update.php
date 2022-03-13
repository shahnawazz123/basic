<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */

$this->title = 'General Settings';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['update']];
//$this->params['breadcrumbs'][] = ['label' => $model->setting_id, 'url' => ['view', 'id' => $model->setting_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="settings-update">
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
</div>
