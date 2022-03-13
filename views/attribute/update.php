<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Attributes */

$this->title = 'Update attribute: ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Attributes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->attribute_id, 'url' => ['view', 'id' => $model->attribute_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?=
                $this->render('_form', [
                    'model' => $model,
                ])
                ?>
            </div>
        </div>
    </div>
</div>

