<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Translator */

$this->title = 'Update Translator: ' . $model->translator_id;
$this->params['breadcrumbs'][] = ['label' => 'Translators', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->translator_id, 'url' => ['view', 'id' => $model->translator_id]];
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
