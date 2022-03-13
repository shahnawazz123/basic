<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LabAdmins */

$this->title = 'Update Lab Admins: ' . $model->lab_admin_id;
$this->params['breadcrumbs'][] = ['label' => 'Lab Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lab_admin_id, 'url' => ['view', 'id' => $model->lab_admin_id]];
$this->params['breadcrumbs'][] = 'Update';
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
