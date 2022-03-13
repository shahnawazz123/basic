<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use himiklab\sortablegrid\SortableGridView;

/* @var $this yii\web\View */
/* @var $model app\models\Attributes */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Attributes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [

                        'name_en',
                        [
                            'attribute' => 'name_ar',
                        ],
                        'code',
                    ],
                ])
                ?>

                <div class="text-right">
                    <a tabindex="0" class="btn btn-md btn-warning" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" title="" data-content="Drag & Drop to change the order"><i class="fa fa-info-circle"></i></a>
                </div>
                <br clear="all"/>

                <?php
                $dataProvider = new ActiveDataProvider([
                    'query' => $model->getAttributeValues()->orderBy(['sort_order'=>SORT_ASC]),
                    'pagination' => [
                        'pageSize' => 100,
                    ],
                ]);

                echo SortableGridView::widget([
                    'dataProvider' => $dataProvider,
                    'sortableAction' => ['attribute-value/sort'],
                    //'filterModel' => $searchModel,
                    'summary' => '',
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'value_en',
                        [
                            'attribute' => 'value_ar',
                            'value' => function($model) {
                                return '<div dir="rtl">' . $model->value_ar . '</div>';
                            },
                            'format' => 'raw',
                        ],
                    ],
                ]);
                ?>

            </div>

        </div>
    </div>
</div>