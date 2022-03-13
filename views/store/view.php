<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\helpers\PermissionHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Stores */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Stores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->store_id], ['class' => 'btn btn-primary']) : "" ?>
                    <?=
                    ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->store_id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) : "";
                    ?>
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
                        [
                            'attribute' => 'flag',
                            'value' => \yii\helpers\BaseUrl::home() . 'uploads/' . $model->flag,
                            'format' => ['image', ['width' => '96']],
                        ],
                        'code',
                        [
                            'attribute' => 'currency_id',
                            'value' => $model->currency->code_en
                        ],
                        [
                            'attribute' => 'is_default',
                            'value' => ($model->is_default == 1) ? "Yes" : "No"
                        ],
                    ],
                ])
                ?>

                <!-- <b>Store Products</b> -->

                <?php
                /*$dataProvider1 = new ActiveDataProvider([
                    'query' => $model->getStoreProducts(),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider1,
                    //'filterModel' => $searchModel,
                    'summary' => '',
                    'columns' => [
                        'product.name_en',
                    ],
                ]);*/
                ?>

            </div>

        </div>

    </div>

</div>
