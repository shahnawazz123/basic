<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Doctors */

$this->title = (Yii::$app->session['_eyadatAuth'] == 1) ? 'Update Doctors: ' . $model->name_en : 'Update Profile';
if(Yii::$app->session['_eyadatAuth'] == 1)
{
    $this->params['breadcrumbs'][] = ['label' => 'Doctors', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name_en, 'url' => ['view', 'id' => $model->doctor_id]];
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
