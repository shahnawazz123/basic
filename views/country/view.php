<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\PermissionHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Country */

$this->title = $model->nicename;
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');


?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->country_id], ['class' => 'btn btn-primary']):"" ?>
                    <?=
                    ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->country_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]):""
                    ?>
                </p>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
                        'nicename',
                        'iso',
                        'iso3',
                        'numcode',
                        'phonecode',
                        //'currency_en',
                        //'currency_ar',
                        [
                            'label' => 'Flag',
                            'value' => \yii\helpers\BaseUrl::home() . 'uploads/' . $model->flag,
                            'format' => ['image', ['width' => '96']],
                        ],
                        //'delivery_interval',
                        //'express_delivery_interval',
                        //'free_delivery_limit',
                        //'vat',
                        //'shipping_cost',
                        //'cod_cost',
                        //'standard_shipping_cost_actual',
                        //'express_shipping_cost_actual'
                    ],
                ])
                ?>

            </div>
        </div>
    </div>
</div>
