<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cms';
$this->params['breadcrumbs'][] = $this->title;
$permissionStr = '';
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
if ($allowView) {
    $permissionStr .= '{view}';
}
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
                    <?php // Html::a('Create cms', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'title_en',
                        [
                            'attribute' => 'content_en',
                            'format' => 'raw'
                        ],
                        // 'is_deleted',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $permissionStr
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>