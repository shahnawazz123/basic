<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PharmacyLocations */

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Pharmacy Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->pharmacy_location_id], ['class' => 'btn btn-primary']):''; ?>
                    <?= ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->pharmacy_location_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',                    'method' => 'post',
                    ],
                    ]):''; ?>
                </p>

                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                            [
                                'attribute' => 'pharmacy_id',
                                'value' => $model->pharmacy->name_en
                            ],
                            'name_en',
                            'name_ar',
                            [
                                'attribute' => 'governorate_id',
                                'value' => $model->governorate->name_en
                            ],
                            [
                                'attribute' => 'area_id',
                                'value' => $model->area->name_en
                            ],
                            'block',
                            'street',
                            'building',
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
