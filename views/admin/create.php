<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Admin */

$this->title = 'Create admin';
$this->params['breadcrumbs'][] = ['label' => 'Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?=
                $this->render('_form', [
                    'model' => $model,
                    'result' => $result,
                        'id' => $model->admin_id
                ])
                ?>
            </div>
        </div>
    </div>
</div>
