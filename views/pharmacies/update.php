<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pharmacies */

$this->title = (Yii::$app->session['_eyadatAuth'] == 1) ? 'Update Pharmacies: ' . $model->pharmacy_id : 'Update Profile';
if(Yii::$app->session['_eyadatAuth'] == 1)
{
$this->params['breadcrumbs'][] = ['label' => 'Pharmacies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pharmacy_id, 'url' => ['view', 'id' => $model->pharmacy_id]];
$this->params['breadcrumbs'][] = 'Update';
}
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
