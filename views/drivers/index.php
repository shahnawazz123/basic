<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DriversSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'Drivers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Drivers', ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'image',
                                'value' => function($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image;
                                },
                                'format' => ['image', ['width' => '70','height'=>'70']],
                                'filter' => false,
                            ],
                            'name_en',
                            'name_ar',
                            'email:email',
                            'phone',
                            'location',
                            [
                                'label' => 'Status',
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return '<div class="onoffswitch">'
                                            . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->driver_id,
                                                'onclick' => 'common.changeStatus("drivers/change-status",this,' . $model->driver_id . ')'
                                            ])
                                            . '<label class="onoffswitch-label" for="myonoffswitch' . $model->driver_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                            ],

                    ['class' => 'yii\grid\ActionColumn'],
                    ],
                    ]); ?>
                
                
            </div>
        </div>
    </div>
</div>
