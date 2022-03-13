<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Kids */

$this->title = 'Update Kids: ' . $model->kid_id;
$this->params['breadcrumbs'][] = ['label' => 'Kids', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kid_id, 'url' => ['view', 'id' => $model->kid_id]];
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
