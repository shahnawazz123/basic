<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\AttributeSets */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Attribute sets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?php //echo Html::a('Update', ['update', 'id' => $model->attribute_set_id], ['class' => 'btn btn-primary']) ?>
                    <?php 
                    //echoHtml::a('Delete', ['delete', 'id' => $model->attribute_set_id], [
                    //    'class' => 'btn btn-danger',
                    //    'data' => [
                    //        'confirm' => 'Are you sure you want to delete this item?',
                    //        'method' => 'post',
                    //    ],
                    //])
                    ?>
                </p>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
                        'attribute_set_code',
                    ],
                ])
                ?>

                <?php
                $dataProvider = new ActiveDataProvider([
                    'query' => $model->getAttributeSetGroups(),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'summary' => '',
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'attribute0.code',
                        'attribute0.name_en',
                        'attribute0.name_ar',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $data) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', yii\helpers\BaseUrl::home().'attribute/view?id='.$data->attribute_id, [
                                                'title' => Yii::t('yii', 'view'),
                                    ]);
                                }
                                    ]
                                ],
                            ],
                        ]);
                        ?>

            </div>
        </div>
    </div>
</div>
