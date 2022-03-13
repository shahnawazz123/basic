<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Symptoms */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Symptoms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)? Html::a('Update', ['update', 'id' => $model->symptom_id], ['class' => 'btn btn-primary']):''; ?>
                    <?= ($allowDelete)? Html::a('Delete', ['delete', 'id' => $model->symptom_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                    ],
                    ]):''; ?>
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
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
