<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tests */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Tests', 'url' => ['index']];
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
                    <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->test_id], ['class' => 'btn btn-primary']) : ''; ?>
                    <?= ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->test_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ''; ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
                        'price',
                        [
                            'attribute' => 'is_home_service',
                            'value' => ($model->is_home_service == 1) ? 'Yes' : 'No',
                        ],
                        [
                            'attribute' => 'category_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->testCategories)) {
                                    foreach ($model->testCategories as $row) {
                                        $list .= '-' . $row->category->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>