<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CurrenciesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Currencies';
$this->params['breadcrumbs'][] = $this->title;
$permissionStr = '';
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowRefresh = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'refresh', Yii::$app->user->identity->admin_id, 'A');
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                <p class="pull pull-right">
                    <?= ($allowRefresh)?Html::a('Refresh Rates', ['refresh'], ['class' => 'btn btn-success']):"" ?>
                </p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        //'currency_id',
                        'name_en',
                        'name_ar',
                        'code_en',
                        'code_ar',
                        'currency_rate',

                        ['class' => 'yii\grid\ActionColumn', 'template' => '{update}'],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>