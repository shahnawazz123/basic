<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Labs */

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Labs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->lab_id], ['class' => 'btn btn-primary']) : ''; ?>
                    <?= ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->lab_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ''; ?>
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
                        'email:email',
                        [
                            'attribute' => 'insurance_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->labInsurances)) {
                                    foreach ($model->labInsurances as $row) {
                                        $list .= '-' . $row->insurance->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'service_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->labServices)) {
                                    foreach ($model->labServices as $row) {
                                        $list .= '-' . $row->service->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'test_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->labTests)) {
                                    foreach ($model->labTests as $row) {
                                        $list .= '-' . $row->test->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'governorate_id',
                            'value' => $model->governorate->name_en
                        ],
                        [
                            'attribute' => 'area_id',
                            'value' => $model->area->name_en
                        ],
                        'block',
                        'street',
                        'building',
                        'home_test_charge',
                        'admin_commission',
                        'accepted_payment_method',
                        'consultation_time_interval',
                        'max_booking_per_lot',
                        'latlon',
                        //'start_time',
                        //'end_time',
                    ],
                ]) ?>

                <h4>Working Days</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th> End Time</th>
                    </tr>
                    <?php if (isset($model->labsWorkingDays)) {
                        foreach ($model->labsWorkingDays as $row) { ?>
                            <tr>
                                <td><?= $row->day; ?></td>
                                <td><?= date('h:i A', strtotime($row->lab_start_time)); ?></td>
                                <td><?= date('h:i A', strtotime($row->lab_end_time)); ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="3">No working time found</td>
                        </tr>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>