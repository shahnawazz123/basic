<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;
use yii\helpers\ArrayHelper;
\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\LabAdminsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lab Admins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Lab Admins', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                                'name_en',
                                'name_ar',
                                [
                                    'label'=>'Lab',
                                    'value'=>function($model)
                                    {
                                        return (isset($model->lab)) ? $model->lab->name_en : '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'lab_id', AppHelper::getLabsList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Lab']),
                                ],
                                'email:email',
                                //'is_active',
                                //'is_deleted',
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