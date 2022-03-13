<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LabAdmins */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Lab Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= Html::a('Update', ['update', 'id' => $model->lab_admin_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->lab_admin_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                    ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                            'name_en',
                            'name_ar',
                            [
                                'attribute'=>'lab_id',
                                'value'=>(isset($model->lab)) ? $model->lab->name_en : '',
                            ],
                            'email:email'
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
