<?php

use app\models\Pharmacies;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Banner */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Banners', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
   
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= Html::a('Update', ['update', 'id' => $model->banner_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->banner_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'image_en',
                            'value' => function ($image) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_en;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],

                        [
                            'attribute' => 'image_ar',
                            'value' => function ($image) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_ar;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],
                        'name_en',
                        'name_ar',
                        
                        'position',
                        [
                            'attribute' => 'link_type',
                            'value' => (!empty($model->link_type)) ? app\helpers\BannerHelper::$bannerTypes[$model->link_type] : ''
                        ],
                        [
                            'attribute' => 'link_id',
                            'value' => call_user_func(function ($model) {
                                if ($model->link_type == "C") {
                                    $clinic = app\models\Clinics::find()
                                        ->where(['clinic_id' => $model->link_id, 'is_deleted' => 0, 'type' => "C"])
                                        ->one();
                                    if (!empty($clinic)) {
                                        return $clinic->name_en;
                                    }
                                } elseif ($model->link_type == "H") {
                                    $doctor = \app\models\Clinics::find()
                                        ->where(['clinic_id' => $model->link_id, 'is_deleted' => 0, 'type' => "H"])
                                        ->one();
                                    if (!empty($doctor)) {
                                        return $doctor->name_en;
                                    }
                                } elseif ($model->link_type == "D") {
                                    $doctor = \app\models\Doctors::find()
                                        ->where(['doctor_id' => $model->link_id, 'is_deleted' => 0])
                                        ->one();
                                    if (!empty($doctor)) {
                                        return $doctor->name_en;
                                    }
                                } elseif ($model->link_type == "L") {
                                    $labs = \app\models\Labs::find()
                                        ->where(['lab_id' => $model->link_id, 'is_deleted' => 0])
                                        ->one();
                                    if (!empty($labs)) {
                                        return $labs->name_en;
                                    }
                                } elseif ($model->link_type == "P") {
                                    $pharmacy = \app\models\Pharmacies::find()
                                        ->where(['pharmacy_id' => $model->link_id, 'is_deleted' => 0])
                                        ->one();
                                    if (!empty($pharmacy)) {
                                        return $pharmacy->name_en;
                                    }
                                } else {
                                    return "";
                                }
                            }, $model),
                        ],
                        /*'is_active',
                            'is_deleted',
                            'url:ntext',
                            'sort_order',*/
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>