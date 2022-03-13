<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PharmacyLocations */

$this->title = 'Update Pharmacy Locations: ' . $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Pharmacy Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_en, 'url' => ['view', 'id' => $model->pharmacy_location_id]];
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
