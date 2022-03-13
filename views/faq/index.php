<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\FaqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Faqs';
$this->params['breadcrumbs'][] = $this->title;
$permissionStr = '';
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowCreate)?Html::a('Create Faq', ['create'], ['class' => 'btn btn-success']):"" ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'question_en',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'answer_en',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'question_ar',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'answer_ar',
                            'format' => 'raw',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn', 'template' => $permissionStr
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>

    </div>

</div>

