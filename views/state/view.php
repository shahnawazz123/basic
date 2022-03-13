<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $model app\models\State */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->state_id], ['class' => 'btn btn-primary']):"" ?>
                    <?=
                    ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->state_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]):""
                    ?>
                </p>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'country_id',
                            'value' => $model->country->nicename,
                        ],
                        'name_en',
                        'name_ar',
                       
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
</div>
