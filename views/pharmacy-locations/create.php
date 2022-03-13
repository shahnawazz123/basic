<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PharmacyLocations */

$this->title = 'Create Pharmacy Locations';
$this->params['breadcrumbs'][] = ['label' => 'Pharmacy Locations', 'url' => ['index']];
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
