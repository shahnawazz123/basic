<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\field\FieldRange;
use kartik\detail\DetailView;
use kartik\daterange\DateRangePicker;
use app\helpers\PermissionHelper;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\app\assets\SelectAsset::register($this);
$this->title = 'Sale Report';
$this->params['breadcrumbs'][] = $this->title;
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
$admin_commission = 0;
$allowExport = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'export', Yii::$app->user->identity->admin_id, 'A');
?>
<style>
    .kv-field-range input[type=text]{
        width: 70px;
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                  
                <div class="table table-responsive">
                    

                
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>