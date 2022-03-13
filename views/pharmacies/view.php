<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pharmacies */
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');


$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Pharmacies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->pharmacy_id], ['class' => 'btn btn-primary']):''; ?>
                    <?= ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->pharmacy_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                    ],
                    ]):'' ?>
                </p>

                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                            'name_en',
                            'name_ar',
                            [
                                'attribute' => 'image_en',
                                'value' => function($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_en;
                                },
                                'format' => ['image', ['width' => '96']],
                                'filter' => false,
                            ],

                            [
                                'attribute' => 'image_ar',
                                'value' => function($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_ar;
                                },
                                'format' => ['image', ['width' => '96']],
                                'filter' => false,
                            ],
                            'minimum_order',
                            'latlon',
                            'email:email',
                            
                            [
                                'attribute' => 'is_free_delivery',
                                'value' => function($model) {
                                    return ($model->is_free_delivery == '1') ? 'Yes' : 'No';
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'is_featured',
                                'value' => function($model) {
                                    return ($model->is_featured == '1') ? 'Yes' : 'No';
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                           
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
                            'floor',
                            'shop_number',
                            'admin_commission',
                            'delivery_charge',
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
