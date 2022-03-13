<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Kids */

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Kids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->kid_id], ['class' => 'btn btn-primary']):''; ?>
                    <?= ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->kid_id], [
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
                            'name_en',
                            'name_ar',
                            'civil_id',
                            'dob',
                            [
                                'attribute' => 'user_id',
                                'value' => ($model->user->first_name.' '.$model->user->last_name)
                            ],
                        ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
