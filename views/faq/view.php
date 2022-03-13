<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Faq */

$this->title = $model->question_en;
$this->params['breadcrumbs'][] = ['label' => 'Faqs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowUpdate)?Html::a('Update', [ 'update', 'id' => $model->faq_id], ['class' => 'btn btn-primary']):"" ?>
                    <?=
                    ($allowDelete)?Html::a('Delete', [ 'delete', 'id' => $model->faq_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]):"";
                    ?>
                </p>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
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
                    ],
                ])
                ?>

            </div>

        </div>

    </div>

</div>
