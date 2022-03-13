<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Manufacturers */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Manufacturers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= Html::a('Update', ['update', 'id' => $model->manufacturer_id], ['class' => 'btn btn-primary']) ?>
                    <?=
                    Html::a('Delete', ['delete', 'id' => $model->manufacturer_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ])
                    ?>
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
                        [
                            'label' => 'Image',
                            'value' => \app\helpers\AppHelper::getUploadUrl() . $model->image_name,
                            'format' => ['image', ['width' => '96']],
                        ],
                    ],
                ])
                ?>

            </div>
        </div>
    </div>
</div>
