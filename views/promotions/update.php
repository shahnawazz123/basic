<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Promotions */

$this->title = 'Update Promotions: ' . $model->title_en;
$this->params['breadcrumbs'][] = ['label' => 'Promotions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title_en, 'url' => ['view', 'id' => $model->promotion_id]];
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
