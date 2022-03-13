<?php

use yii\helpers\BaseUrl;
use kartik\helpers\Html;
use yii\grid\GridView;
use app\helpers\AppHelper;
use kartik\daterange\DateRangePicker;
use kartik\field\FieldRange;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile(BaseUrl::home() . 'js/order.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
$btnStr = '';
$allowExport = $allowView = true;

/*$allowExport = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'export', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
*/
if ($allowView) {
    $btnStr .= '{view} ';
}
define('time', microtime());
$this->registerJsFile(BaseUrl::home() . 'js/order.js?t='.time, ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style>
    .kv-field-range input[type=text]{
        /*        width: 70px;*/
    }
    .form-control[readonly]
    {background-color: #fff;}
</style>
<?php
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?= ($allowExport) ? Html::a('Export to excel', ['export' . $urlQuery], ['class' => 'btn btn-info', 'data-pjax' => 'false']) : "" ?>
                </p>
                <p class="pull-left">
                    <button type="button" onclick="order.bulkStatus()" class="btn btn-primary">Bulk Status Update</button>
                </p>
                <div class="clearfix">&nbsp;</div>
                <div class="table-responsive">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'formatter' => [
                            'class' => 'yii\i18n\Formatter',
                            'nullDisplay' => ''
                        ],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'class' => 'yii\grid\CheckboxColumn',

                            ],
                            [
                                'label' => 'Order number',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a($model->order_number, BaseUrl::home() . 'order/view?id=' . $model->order_id);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'order_number', ['class' => 'form-control'])
                            ],
                            [
                                'label' => 'Purchased On',
                                'value' => function($model) {
                                    $newTimeZone = new \DateTimeZone(Yii::$app->params['timezone']);
                                    $dateTime = new \DateTime($model->create_date, new \DateTimeZone('UTC'));
                                    $dateTime->setTimezone($newTimeZone);

                                    return $dateTime->format('d-m-Y');
                                },
                                'filter' => '<div class="input-group drp-container" style="width: 230px;">' . DateRangePicker::widget([
                                        'model' => $searchModel,
                                        'attribute' => 'date_range',
                                        'presetDropdown' => false,
                                        'convertFormat' => true,
                                        'useWithAddon' => true,
                                        'pluginOptions' => [
                                            'locale' => [
                                                'format' => 'd-M-y',
                                                'separator' => ' to ',
                                            ],
                                            'opens' => 'left',
                                            'ranges' => [
                                                "Today" => ["moment().startOf('day')", "moment()"],
                                                "Yesterday" => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                                                "Last 7 Days" => ["moment().startOf('day').subtract(6, 'days')", "moment()"],
                                                "Last 30 Days" => ["moment().startOf('day').subtract(29, 'days')", "moment()"],
                                                "Last Week" => ["moment().startOf('day').subtract(7, 'days')", "moment()"],
                                                "Last Month" => ["moment().startOf('day').subtract(31, 'days')", "moment()"],
                                            ],
                                        ],
                                    ]) . '</div>',
                            ],
                            [
                                'label' => 'Username',
                                'attribute' => 'user_id',
                                'value' => function($model) {
                                    if (!empty($model->user)) {
                                        return $model->user->first_name . ' ' . $model->user->last_name;
                                    } else {
                                        return $model->recipient_name;
                                    }
                                },
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'filter' => Html::activeDropDownList($searchModel, 'user_id', AppHelper::getAllUser(), ['class' => 'form-control select2', 'prompt' => 'Filter By User']),
                            ],
                            [
                                'label' => 'Total Bill',
                                'attribute' => 'total_amount',
                                'value' => function($model) {
                                    return $model->currency_code . " " . number_format($model->total_bill, 3);
                                },
                                'filter' => FieldRange::widget([
                                    'model' => $searchModel,
                                    'label' => '',
                                    'attribute1' => 'from_amt',
                                    'attribute2' => 'to_amt',
                                    'separator' => 'to',
                                    'template' => '{widget}'
                                ]),
                            ],
                            [
                                'label' => 'Quantity',
                                'value' => function($model) {
                                    $qty = 0;
                                    if (\Yii::$app->session['_eyadatAuth'] == 2) {
                                        $pharmacyOrders = $model->getPharmacyOrders()->where(['pharmacy_id' => Yii::$app->user->identity->pharmacy_id])->all();
                                        foreach ($pharmacyOrders as $pharmacyOrder) {
                                            foreach ($pharmacyOrder->orderItems as $bo) {
                                                $qty += $bo->quantity;
                                            }
                                        }
                                    } else {
                                        foreach ($model->pharmacyOrders as $pharmacyOrder) {
                                            foreach ($pharmacyOrder->orderItems as $bo) {
                                                $qty += $bo->quantity;
                                            }
                                        }
                                    }
                                    return $qty;
                                },
                            ],
                            [
                                'attribute' => 'shipping_address_id',
                                'visible' => (!empty($_GET['OrdersSearch']['status_id']) && $_GET['OrdersSearch']['status_id'] == 6) ? false : true,
                                'value' => function($model) {
                                    if ($model->user_id != null) {
                                        $shipping = $model->shippingAddress;
                                        if (!empty($shipping)) {
                                            $string = '';
                                            $string .= $model->formatAddress($shipping);
                                            /*if (!empty($shipping->building))
                                                $string .= 'Building: ' . $shipping->building . ', ';
                                            if (!empty($shipping->zone))
                                                $string .= 'Zone: ' . $shipping->zone . '<br />';
                                            if (!empty($shipping->street))
                                                $string .= 'Street: ' . $shipping->street . '<br />';
                                            if (!empty($shipping->addressline_1))
                                                $string .= 'Address Line: ' . $shipping->addressline_1 . '<br />';
                                            if (!empty($shipping->notes))
                                                $string .= 'Notes: ' . $shipping->notes . '<br />';
                                            if (!empty($shipping->block))
                                                $string .= '' . $shipping->block->name_en . ', ';
                                            if (!empty($shipping->area))
                                                $string .= '' . $shipping->area->name_en . '<br />';
                                            if (!empty($shipping->state))
                                                $string .= '' . $shipping->state->name_en;                                      
                                            if (!empty($shipping->country))
                                                $string .= '' . $shipping->country->nicename;*/
                                            return $string;
                                        }
                                    } else {
                                        return app\helpers\AppHelper::getGuestShippingAddress($model->order_id);
                                    }
                                },
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'payment_mode',
                                'value' => function($model) {
                                    if ($model->payment_mode == 'K')
                                        return 'Knet';
                                    elseif ($model->payment_mode == 'CC')
                                        return 'Visa/MasterCard';
                                    elseif ($model->payment_mode == 'C')
                                        return 'Cash on Delivery';
                                    else
                                        return '';
                                },
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'filter' => Html::activeDropDownList($searchModel, 'payment_mode', ['K' => 'Knet', 'CC' => 'Visa/MasterCard', 'C' => 'Cash on Delivery'], ['class' => 'form-control select2', 'prompt' => 'Filter By Payment Mode']),
                            ],
                            [
                                'label' => 'Admin Commission',
                                'attribute' => 'total_amount',
                                'value' => function($model) {
                                    return "KWD " . number_format($model->admin_commission, 3);
                                },
                                'filter' => false,
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'headerOptions' => ['style' => 'width:200px'],
                            ],
                            [
                                'label' => 'Pharmacy Status',
                                'filter' => false,
                                'value' => function($model) {
                                    $pharmacyOrderStatus = \app\models\PharmacyOrderStatus::find()
                                            ->join('LEFT JOIN', 'pharmacy_orders', 'shop_order_status.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                                            ->where(['pharmacy_orders.order_id' => $model->order_id])
                                            ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                            ->one();
                                    if (!empty($pharmacyOrderStatus)) {
                                        return $pharmacyOrderStatus->pharmacyStatus->name_en;
                                    } else {
                                        return "";
                                    }
                                },
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 2),
                                'filter' => Html::activeDropDownList($searchModel, 'pharmacy_status_id', app\helpers\AppHelper::getPharmaStatusList(), ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            ],
                            [
                                'label' => 'Status',
                                'filter' => false,
                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                'value' => function($model) {
                                    $orderStatus = app\models\OrderStatus::find()
                                            ->where(['order_id' => $model->order_id])
                                            ->orderBy(['order_status_id' => SORT_DESC])
                                            ->one();

                                    return (!empty($orderStatus)) ? $orderStatus->status->name_en : "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'status_id', app\helpers\AppHelper::getStatusList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                            ],
                            [
                                'label' => 'Contacted User',
                                'attribute' => 'is_contacted',
                                'format' => 'raw',
                                'visible' => (!empty($_GET['OrdersSearch']['status_id']) && $_GET['OrdersSearch']['status_id'] == 7 && \Yii::$app->session['_boutikeyAuth'] == 1) ? true : false,
                                'value' => function($model) {
                                    return '<div class="onoffswitch">'
                                            . Html::checkbox('onoffswitch', $model->is_contacted, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->order_id,
                                                'onclick' => 'order.changeOrderSwitch("order/contacted",this,' . $model->order_id . ')',
                                            ])
                                            . '<label class="onoffswitch-label" for="myonoffswitch' . $model->order_id . '"></label></div>';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_contacted', [1 => 'Yes', 0 => 'No'], ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $btnStr
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>
<div class="modal fade" id="bulkStatusChangeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Status</h4>
            </div>
            <div class="modal-body">
                <div id="bulkModelAlert" class="alert alert-danger" style="display: none">
                </div>

                <br class="clearfix"/>
                <?php
                $current_status_id = (!empty($searchModel->status_id)) ? $searchModel->status_id : '';
                ?>
                <div class="form-group required field-status">
                    <label class="control-label" for="bulk_status_id">Status</label>
                    <?php
                    echo Html::dropDownList('status', '', \app\helpers\AppHelper::getStatusList($current_status_id), ['prompt' => 'Select Status', 'class' => 'form-control', 'id' => 'bulk_status_id'])
                    ?>
                    <div class="help-block" style="display: none; color: #d62c1a;">Please select status.</div>
                </div>

                <div class="form-group">
                    <label class="control-label" for="bulk_status_comment">Comment</label>
                    <?php
                    echo Html::textarea('comment', '', ['rows' => 3, 'class' => 'form-control', 'id' => 'bulk_status_comment'])
                    ?>
                </div>

                <div class="form-group">
                    <?php
                    echo Html::checkbox('notify', '', ['class' => '', 'id' => 'notify']);
                    echo Html::label('Notify Customer', 'notify', ['style' => 'font-weight: normal; font-size: 12px; margin: 0;padding-left: 5px;']);
                    ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="order.bulkStatusChange()" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
