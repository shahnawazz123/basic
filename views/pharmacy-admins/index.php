<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\AppHelper;
use yii\helpers\ArrayHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\PharmacyAdminsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pharmacy Admins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= Html::a('Create Pharmacy Admins', ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                <?php // echo $this->render('_search', ['model' => $searchModel]);?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute'=>'pharmacy_id',
                                    'label'=>'Pharmacy',
                                    'value'=> function ($model) {
                                        return $model->pharmacy ? $model->pharmacy->name_en : "";
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'pharmacy_id', AppHelper::getPharmacyList(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                                ],
                                'name_en',
                                'name_ar',
                                'email:email',
                                //'password',
                                //'is_active',
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