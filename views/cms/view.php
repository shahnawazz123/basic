<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Cms */

$this->title = $model->title_en;
$this->params['breadcrumbs'][] = ['label' => 'Cms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->cms_id], ['class' => 'btn btn-primary']):"" ?>
                    <?php 
//                    echo Html::a('Delete', ['delete', 'id' => $model->cms_id], [
//                        'class' => 'btn btn-danger',
//                        'data' => [
//                            'confirm' => 'Are you sure you want to delete this item?',
//                            'method' => 'post',
//                        ],
//                    ])
                    ?>
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'title_en',
                        'title_ar',
                        [
                            'attribute' => 'content_en',
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'content_ar',
                            'format' => 'raw'
                        ],
                    ],
                ])
                ?>

            </div>
        </div>
    </div>
</div>