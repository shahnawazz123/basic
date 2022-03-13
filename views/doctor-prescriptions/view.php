<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorPrescriptions */

$this->title = $model->doctor_appointment_prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull-right">
                    <?= Html::a('Update', ['update', 'id' => $model->doctor_appointment_prescription_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->doctor_appointment_prescription_id], [
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
                        'doctor_appointment_id',
                        'total_usage',
                        'referred_pharmacy_id',
                        'is_deleted',
                        'is_active',
                        [
                            'attribute' => 'area_id',
                            'value' => Yii::$app->formatter->asDateTime($model->created_at)

                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
</div>