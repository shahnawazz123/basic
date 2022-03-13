<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Doctors */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->doctor_id], ['class' => 'btn btn-primary']):""; ?>
                    <?= ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->doctor_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                    ],
                    ]) :"";?>
                </p>

                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                            'name_en',
                            'name_ar',
                            'email:email',
                            'registration_number',
                            //'password',
                            'years_experience',
                            'qualification:ntext',
                            [
                                'attribute' => 'image',
                                'value' => function($image) {
                                    return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image;
                                },
                                'format' => ['image', ['width' => '96']],
                                'filter' => false,
                            ],
                            [
                         
                                'attribute' => 'gender',
                                'value' => function($model) {
                                    if($model->gender=='M')
                                        return 'Male';
                                    else if($model->gender=='W')
                                        return 'Women';
                                    else if($model->gender=='W')
                                        return 'Unisex';
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'attribute' =>'type',
                                'value' => function($model)
                                {
                                    $type = '';
                                    $types = explode(',', $model->type);
                                    if (count($types) > 1) {
                                        $type = 'Video Consultation & Person Consultation';
                                    } 
                                    else if($model->type == 'V')
                                    {
                                        $type = 'Video Consultation';
                                    }else if($model->type == 'I'){
                                        $type = 'Person Consultation';
                                    }
                                    return $type; 
                                }
                            ],
                            'consultation_time_online',
                            'consultation_time_offline',
                            
                            'accepted_payment_method',
                            [
                                'attribute' => 'clinic_id',
                                'value' => function($model) {
                                    return (isset($model->clinic)) ? $model->clinic->name_en : '';
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            'consultation_price_regular',
                            'consultation_price_final',
                            [
                                'attribute' => 'category_id',
                                'value' => function($model) 
                                {
                                    $list = '';
                                    if(isset($model->doctorCategories))
                                    {
                                        foreach($model->doctorCategories as $row)
                                        {
                                            $list .= '-'.$row->category->name_en.'<br>';
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
                                'value' => function($model) 
                                {
                                    $list = '';
                                    if(isset($model->doctorInsurances))
                                    {
                                        foreach($model->doctorInsurances as $row)
                                        {
                                            $list .= '-'.$row->insurance->name_en.'<br>';
                                             //array_push($c_list,$cats->category->name_en);
                                        }   
                                    }
                                    return $list;
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'symptom_id',
                                'value' => function($model) 
                                {
                                    $list = '';
                                    if(isset($model->doctorSymptoms))
                                    {
                                        foreach($model->doctorSymptoms as $row)
                                        {
                                            $list .= '-'.$row->symptoms->name_en.'<br>';
                                             //array_push($c_list,$cats->category->name_en);
                                        }   
                                    }
                                    return $list;
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'description_en',
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'description_ar',
                                'format' => 'raw'
                            ],
                            //'is_active',
                            //'is_deleted',
                            //'created_at',
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
                    <?php if(isset($model->doctorWorkingDays))
                            {
                                foreach($model->doctorWorkingDays as $row)
                                {?>
                                    <tr>
                                        <td><?=$row->day;?></td>
                                        <td><?=date('h:i A', strtotime($row->start_time));?></td>
                                        <td><?=date('h:i A', strtotime($row->end_time));?></td>
                                    </tr>
                        <?php } }else{?>
                            <tr>
                                <td colspan="3">No working time found</td></tr>
                            </tr>
                        <?php } ?>
                </table>
            </div>

        </div>
    </div>
</div>
