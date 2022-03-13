<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\ManufacturerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile(BaseUrl::home() . 'js/manufacturer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = 'Manufacturers';
$this->params['breadcrumbs'][] = $this->title;

$permissionStr = '';
$allowActivate = $allowCreate = $allowPush = $allowUpdate = $allowDelete = $allowView = true;
if ($allowPush) {
    $permissionStr .= '{push}';
}
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
if ($allowView) {
    $permissionStr .= '{view}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Manufacturers', ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'image_name',
                            'value' => function($model) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image_name;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],
                        'name_en',
                        'name_ar',
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function ($model, $url) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->manufacturer_id,
                                    'onclick' => 'manufacturer.changeStatus("manufacturer/change-status",this,' . $model->manufacturer_id . ')',
                                    'disabled' => ($allowActivate)?false:true,
                                ])
                                . '<label class="onoffswitch-label" for="myonoffswitch' . $model->manufacturer_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $permissionStr,
                        ],
                    ],
                ]);
                ?>


            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>