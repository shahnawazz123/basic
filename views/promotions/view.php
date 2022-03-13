<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Promotions */

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');

$this->title = $model->title_en;
$this->params['breadcrumbs'][] = ['label' => 'Promotions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->promotion_id], ['class' => 'btn btn-primary']):''; ?>
                    <?= ($allowDelete)? Html::a('Delete', ['delete', 'id' => $model->promotion_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                    ],
                    ]):''; ?>
                </p>

                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                            'title_en',
                            'title_ar',
                            'code',
                            'start_date',
                            'end_date',
                            [
                                'attribute' => 'promo_type',
                                'value' => function($model) 
                                {
                                    if($model->promo_type == 'M')
                                    {
                                        return 'Multiple';
                                    }else if($model->promo_type == 'S')
                                    {
                                        return 'Single';
                                    }
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            'promo_count',
                            'discount',
                            [
                                'attribute' => 'promo_for',
                                'value' => function($model) 
                                {
                                    if($model->promo_for == 'D')
                                    {
                                        return 'Doctor';
                                    }else if($model->promo_for == 'C')
                                    {
                                        return 'Clinic';
                                    }else if($model->promo_for == 'L')
                                    {
                                        return 'Lab';
                                    }else if($model->promo_for == 'F')
                                    {
                                        return 'Pharmacy';
                                    }
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'label' => 'promo for list',
                                'value' => function($model) 
                                {
                                    $list = [];
                                    if($model->promo_for == 'D')
                                    {
                                        foreach($model->promotionDoctors as $row)
                                        {
                                            array_push($list,$row->doctor->name_en);
                                        }
                                    }else if($model->promo_for == 'C')
                                    {
                                        foreach($model->promotionClinics as $row)
                                        {
                                            array_push($list,$row->clinic->name_en);
                                        }
                                    }else if($model->promo_for == 'L')
                                    {
                                        foreach($model->promotionLabs as $row)
                                        {
                                            array_push($list,$row->lab->name_en);
                                        }
                                    }else if($model->promo_for == 'F')
                                    {
                                        foreach($model->promotionPharmacy as $row)
                                        {
                                            array_push($list,$row->pharmacy->name_en);
                                        }
                                    }

                                    if(!empty($list))
                                    {
                                        $html = '<ul>';
                                        foreach($list as $ls)
                                        {
                                            $html .= '<li>'.ucwords($ls).'</li>';
                                        }
                                        $html .='</ul>';
                                        return $html;
                                    }
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            'minimum_order',
                            'shipping_included',
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>
