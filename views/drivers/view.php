<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Drivers */

$this->title = $model->driver_id;
$this->params['breadcrumbs'][] = ['label' => 'Drivers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= Html::a('Update', ['update', 'id' => $model->driver_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->driver_id], [
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
                    [
                        'attribute' => 'image',
                        'value' => function($image) {
                            return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image;
                        },
                        'format' => ['image', ['width' => '96']],
                        'filter' => false,
                    ],
                    'name_en',
                    'name_ar',
                    'email:email',
                    'phone',
                    'location',
                    'civil_id_number',
                    'license_number',
                    'device_token',
                    'device_type',
                    'device_model',
                    'app_version',
                    'os_version',
                    //'push_enabled',
                    
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
