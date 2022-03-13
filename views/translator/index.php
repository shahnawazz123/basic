<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
/* @var $this yii\web\View */
/* @var $searchModel app\models\TranslatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\app\assets\SelectAsset::register($this);
$this->title = 'Translators';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Translator', ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                                                        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                                    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

            'name_en',
            'name_ar',
            'email:email',

            [
                'label' => 'Status',
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function($model) {
                    return '<div class="onoffswitch">'
                        . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->translator_id,
                            'onclick' => 'common.changeStatus("translator/change-status",this,' . $model->translator_id . ')'
                        ])
                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->translator_id . '"></label></div>';
                },
                'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
            ],
            //'is_deleted',
            //'created_at',
            //'updated_at',

                    ['class' => 'yii\grid\ActionColumn'],
                    ],
                    ]); ?>
                
                
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>