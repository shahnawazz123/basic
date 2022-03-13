<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Clinics */

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = true; //PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = true; //PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = true; //PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');

$controller = $this->context->action->controller->id;
$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => ucwords($controller), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->clinic_id], ['class' => 'btn btn-primary']) : ""; ?>
                    <?= ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->clinic_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ""; ?>
                </p>

                <?= DetailView::widget([
                   
                    'model' => $model,
                    'attributes' => [
                        'name_en',
                        'name_ar',
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
                        'description_en',
                        'description_ar',
                        'admin_commission',
                        'latlon',
                        [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return ($model->type == 'H') ? 'Hospital' : 'Clinic';
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
                        'email:email',
                        //'password',
                        //'is_active',
                        //'is_deleted',
                        [
                            'label' => 'Created Date and Time',
                            // 'attribute' => 'area_id',
                            'value' => Yii::$app->formatter->asDateTime($model->created_at)
                            
                        ],
                        [
                            'attribute' => 'category_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->clinicCategories)) {
                                    foreach ($model->clinicCategories as $row) {
                                        $list .= '-' . $row->category->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'insurance_id',
                            'value' => function ($model) {
                                $list = '';
                                if (isset($model->clinicInsurances)) {
                                    foreach ($model->clinicInsurances as $row) {
                                        $list .= '-' . $row->insurance->name_en . '<br>';
                                        //array_push($c_list,$cats->category->name_en);
                                    }
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        //'updated_at',
                    ],
                ]) ?>

                <h4>Working Days</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th> End Time</th>
                    </tr>
                    <?php if (isset($model->clinicWorkingDays)) {
                        foreach ($model->clinicWorkingDays as $row) { ?>
                            <tr>
                                <td><?= $row->day; ?></td>
                                <td><?= date('h:i A', strtotime($row->start_time)); ?></td>
                                <td><?= date('h:i A', strtotime($row->end_time)); ?></td>
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