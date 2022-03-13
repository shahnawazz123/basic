<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LabAppointments */

$this->title = 'Create Lab Appointments';
$this->params['breadcrumbs'][] = ['label' => 'Lab Appointments', 'url' => ['index']];
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
