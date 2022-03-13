<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Orders */
$this->registerJsFile(BaseUrl::home() . 'js/order.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$timeZone = 'Asia/Kuwait';

//$newTimeZone = new \DateTimeZone(Yii::$app->session['timezone']);
$newTimeZone = new \DateTimeZone($timeZone);
$dateTime = new \DateTime($model->order->create_date, new \DateTimeZone('UTC'));
$dateTime->setTimezone($newTimeZone);

$model->order->create_date = $dateTime->format('Y-m-d H:i:s');

$this->title = 'Order #' . $model->order_number . " | " . date("M d, Y h:i A", strtotime($model->order->create_date));

$userModel = $model->order->user;
$shippingAddressModel = $model->order->shippingAddress;
$paymentModel = app\models\Payment::find()
        ->where(['type_id' => $model->order_id,'type' => 'O'])
        ->orderBy(['payment_id' => SORT_DESC])
        ->one();
$orderItems = \app\models\OrderItems::find()
        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
        ->where(['pharmacy_orders.pharmacy_order_id' => $model->pharmacy_order_id]);

if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
    $orderItems->andWhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
}

$orderStatus = \app\models\OrderStatus::find()
        ->where(['order_id' => $model->order_id])
        ->orderBy(['order_status_id' => SORT_DESC])
        ->one();

if (\Yii::$app->session['_eyadatAuth'] == 2 || \Yii::$app->session['_eyadatAuth'] == 5) {
    $shopOrderStatus = app\models\PharmacyOrderStatus::find()
            ->join('LEFT JOIN', 'pharmacy_orders', 'pharmacy_order_status.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
            ->where(['pharmacy_orders.pharmacy_order_id' => $model->pharmacy_order_id])
            ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
            ->one();
}
$allowSendInvoice = false;
$allowPrint = $allowChangeStatus = true;
?>
<style>
    .kv-panel-before, .kv-panel-after, .panel-footer {display: none;}
    #order-status .kv-panel-before, #order-status-boutique .kv-panel-before { display: block;}
    #order-status .pull-right, #order-status-boutique .pull-right { float: none !important;}
    .panel-primary {
        border-color: #d4d4d4;
    }
    .panel-primary > .panel-heading {
        color: #333;
        background-color: #d4d4d4;
        border-color: #d4d4d4;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="hpanel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'buttons1' => '',
                            'panel' => [
                                'heading' => 'Order # ' . $model->order_number,
                                'type' => DetailView::TYPE_PRIMARY,
                            ],
                            'attributes' => [
                                [
                                    'label' => 'Order Date',
                                    'value' => date("M d, Y h:i A", strtotime($model->order->create_date))
                                ],
                                [
                                    'label' => 'Order status',
                                    'value' => !empty($model->order->getOrderStatuses()->orderBy(['order_status_id' => SORT_DESC])->one()->status) ? $model->order->getOrderStatuses()->orderBy(['order_status_id' => SORT_DESC])->one()->status->name_en : ""
                                ],
                                [
                                    'label' => 'Placed From IP',
                                    'value' => $model->order->user_ip
                                ]
                            ],
                        ])
                        ?>
                    </div>

                    <?php
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                        ?>
                        <div class="col-md-6">
                            <?=
                            DetailView::widget([
                                'model' => $model,
                                'buttons1' => '',
                                'panel' => [
                                    'heading' => 'Account Information',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'attributes' => [
                                    [
                                        'label' => 'Customer Name',
                                        'value' => !empty($userModel) ? $userModel->first_name . " " . $userModel->last_name : $model->recipient_name,
                                        'labelColOptions' => ['style' => 'width:25%;text-align:right'],
                                    ],
                                    [
                                        'label' => 'Email',
                                        'value' => !empty($userModel) ? $userModel->email : $model->shipping_email
                                    ]
                                ],
                            ])
                            ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php
                    }
                    ?>

                    <?php
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                        ?>
                        <div class="col-md-6">
                            <?php
                            if ($model->order->user_id === null) {
                                $blockModel = app\models\Block::findOne($model->order->shipping_block_id);
                                $areaModel = app\models\Area::findOne($model->order->shipping_area_id);
                                echo DetailView::widget([
                                    'model' => $model,
                                    'buttons1' => '',
                                    'formOptions' => ['id' => 'shipping-information'],
                                    'panel' => [
                                        'heading' => 'Shipping Address',
                                        'type' => DetailView::TYPE_PRIMARY,
                                    ],
                                    'saveOptions' => [
                                    ],
                                    'attributes' => [
                                        [
                                            'label' => 'Recipient name',
                                            'value' => $model->recipient_name
                                        ],
                                        [
                                            'label' => 'Address',
                                            'value' => $model->order->shipping_street . ", " . $model->order->shipping_addressline_1 . "<br>" .
                                            $blockModel->name_en . ", " . $areaModel->name_en . ", " . $areaModel->state->name_en,
                                            'format' => 'raw'
                                        ],
                                        [
                                            'label' => 'Mobile Phone #',
                                            'value' => $model->order->recipient_phone
                                        ],
                                        [
                                            'label' => 'Extra Direction',
                                            'value' => $model->order->shipping_notes
                                        ],
                                    ],
                                ]);
                            } else if (!empty($shippingAddressModel)) {
                                echo DetailView::widget([
                                    'model' => $shippingAddressModel,
                                    'buttons1' => '',
                                    'formOptions' => ['id' => 'shipping-information'],
                                    'panel' => [
                                        'heading' => 'Shipping Address',
                                        'type' => DetailView::TYPE_PRIMARY,
                                    ],
                                    'saveOptions' => [
                                    ],
                                    'attributes' => [
                                        [
                                            'label' => 'Recipient name',
                                            'value' => $shippingAddressModel->first_name . " " . $shippingAddressModel->last_name
                                        ],
                                        [
                                            'label' => 'Country',
                                            'value' => (!empty($shippingAddressModel) && !empty($shippingAddressModel->country)) ? $shippingAddressModel->country->name_en : ''
                                        ],
                                        [
                                            'label' => 'Address',
                                            'value' => $model->order->formatAddress($model->order->shippingAddress),
                                            'format' => 'raw'
                                        ],
                                        [
                                            'label' => 'Mobile Phone #',
                                            'value' => $shippingAddressModel->mobile_number
                                        ],
                                        [
                                            'label' => 'Extra Direction',
                                            'value' => $shippingAddressModel->notes
                                        ],
                                    ],
                                ]);
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                     <div class="col-md-6">
                        <?php
                        if (!empty($paymentModel)) {
                            echo DetailView::widget([
                                'model' => $paymentModel,
                                'buttons1' => '',
                                'panel' => [
                                    'heading' => 'Payment Information',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'attributes' => [
                                    [
                                        'label' => 'Payment method',
                                        'value' => ($paymentModel->paymode == "C") ? "Cash on Delivery" : (($paymentModel->paymode == "K") ? "K-Net" : "Credit Card")
                                    ],
                                    [
                                        'label' => 'Order placed using',
                                        'value' => $paymentModel->currency_code,
                                    ],
                                    [
                                        'label' => 'Payment Id',
                                        'value' => $paymentModel->PaymentID,
                                        'visible' => ($paymentModel->paymode == "K") ? true : false,
                                    ],
                                    [
                                        'label' => 'Result',
                                        'value' => $paymentModel->result,
                                        'visible' => ($paymentModel->paymode == "K") ? true : false,
                                    ],
                                    [
                                        'label' => 'Transaction ID',
                                        'value' => $paymentModel->transaction_id,
                                        'visible' => ($paymentModel->paymode == "K") ? true : false,
                                    ],
                                    [
                                        'label' => 'Ref ID',
                                        'value' => $paymentModel->ref,
                                        'visible' => ($paymentModel->paymode == "K") ? true : false,
                                    ],
                                    [
                                        'label' => 'Track Id',
                                        'value' => $paymentModel->TrackID,
                                        'visible' => ($paymentModel->paymode == "K") ? true : false,
                                    ],
                                ],
                            ]);
                        }
                        ?>
                    </div>

                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new ActiveDataProvider([
                            'query' => $orderItems,
                            'pagination' => [
                                'pageSize' => 20,
                            ],
                        ]);

                        echo GridView::widget([
                            'dataProvider' => $dataProvider1,
                            //'filterModel' => $searchModel,
                            'formatter' => [
                                'class' => 'yii\i18n\Formatter',
                                'nullDisplay' => ''
                            ],
                            'panel' => [
                                'heading' => 'Items Ordered',
                                'type' => DetailView::TYPE_PRIMARY,
                            ],
                            'export' => false,
                            'toggleData' => false,
                            'summary' => '',
                            'columns' => [
                                [
                                    'label' => 'Product',
                                    'value' => function ($data) use ($model) {
                                        if ($data->product_id != null) {
                                            $product = $data->product;
                                            $attributeValues = "";

                                            $attributes = $model->order->getProductAttributeValues($product);
                                            foreach ($attributes as $atr) {
                                                foreach ($atr['attributes'] as $option) {
                                                    $attributeValues .= "<b>{$atr['type']}</b> : {$option['value']}<br>";
                                                }
                                            }

                                            $html = (!empty($product->width) ? "<b>Width</b> : {$product->width}<br>" : "") .
                                                    (!empty($product->height) ? "<b>Height</b> : {$product->height}<br>" : "") .
                                                    (!empty($product->length) ? "<b>Length</b> : {$product->length}<br>" : "") .
                                                    (!empty($product->weight) ? "<b>Weight</b> : {$product->weight}<br>" : "");

                                            return $product->name_en . "<br>" . $attributeValues;
                                        } else {
                                            return "";
                                        }
                                    },
                                    'format' => 'raw'
                                ],
                                [
                                    'label' => 'Image',
                                    'value' => function ($data) {
                                        $product = $data->product;
                                        $image = $product->getProductImage($product->product_id);

                                        if (!empty($image)) {
                                            return '<a class="fancybox"  href="' . $image . '">
                                                <img  src="' . $image . '" style="max-height: 100px;" />
                                            </a>';
                                        } else {
                                            return '';
                                        }
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'label' => 'SKU',
                                    'value' => function ($data) {
                                        if ($data->product_id != null) {
                                            return $data->product->SKU;
                                        } else {
                                            return $data->bundle->SKU;
                                        }
                                    }
                                ],
                                [
                                    'label' => 'Price',
                                    'value' => function ($data) {
                                        return $data->currency->code_en . " " . $data->price;
                                    }
                                ],
                                [
                                    'label' => 'Quantity',
                                    'value' => function ($data) {
                                        return $data->quantity;
                                    }
                                ],
                                [
                                    'label' => 'Total',
                                    'value' => function ($data) {
                                        return $data->currency->code_en . " " . number_format(($data->quantity * $data->price), 2);
                                    }
                                ],
                            ],
                        ]);
                        ?>
                    </div>

                    <?php
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                        ?>
                        <div class="col-md-12">
                            <?php
                            $shopOrderQuery = $model->order->getPharmacyOrders()
                                    ->join('INNER JOIN', 'order_items', 'pharmacy_orders.pharmacy_order_id = order_items.pharmacy_order_id')
                                    ->where('pharmacy_id IS NOT NULL')
                                    ->andwhere(['order_items.pharmacy_order_id'=>$model->pharmacy_order_id]);

                            $dataProviderBo = new ActiveDataProvider([
                                'query' => $shopOrderQuery,
                                'pagination' => [
                                    'pageSize' => 20,
                                ],
                            ]);

                            echo GridView::widget([
                                'dataProvider' => $dataProviderBo,
                                'panel' => [
                                    'heading' => 'Pharmacy Order',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    //['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'label' => 'Pharmacy',
                                        'attribute' => 'pharmacy_id',
                                        'value' => function($data) {
                                            return !empty($data->pharmacy) ? $data->pharmacy->name_en : "";
                                        }
                                    ],
                                    'order_number',
                                    [
                                        'attribute' => 'pharmacy_commission',
                                        'label' => 'Commission%',
                                        'value' => function($data) {
                                            return $data->pharmacy_commission . '%';
                                        }
                                    ],
                                    [
                                        'label' => 'Admin Commission',
                                        'value' => function($data) {
                                            $adminCommision = app\helpers\AppHelper::calculateAdminCommision($data->pharmacy_id, $data->order_id);
                                            return 'KWD ' . number_format($adminCommision['admin_commision'], 3);
                                        }
                                    ],
                                    [
                                        'label' => 'Pharmacy Earning',
                                        'value' => function($data) {
                                            $adminCommision = app\helpers\AppHelper::calculateAdminCommision($data->pharmacy_id, $data->order_id);
                                            return 'KWD ' . number_format($adminCommision['pharma_earning'], 3);
                                        }
                                    ],
                                    [
                                        'label' => 'Status',
                                        'value' => function($data) {
                                            $pharmaOrderStaus = \app\models\PharmacyOrderStatus::find()
                                                    ->where(['pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                                    ->one();
                                            if (!empty($pharmaOrderStaus)) {
                                                return $pharmaOrderStaus->pharmacyStatus->name_en;
                                            } else {
                                                return "";
                                            }
                                        }
                                    ],
                                   
                                    [
                                        'label' => 'Date',
                                        'value' => function($data) {
                                            $pharmaOrderStaus = \app\models\PharmacyOrderStatus::find()
                                                    ->where(['pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                                    ->one();
                                            if (!empty($pharmaOrderStaus)) {
                                                return $pharmaOrderStaus->status_date;
                                            } else {
                                                return "";
                                            }
                                        }
                                    ],
                                    [
                                        'label' => 'comment',
                                        'value' => function($data) {
                                            $pharmaOrderStaus = \app\models\PharmacyOrderStatus::find()
                                                    ->where(['pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                                    ->one();
                                            if (!empty($pharmaOrderStaus)) {
                                                return $pharmaOrderStaus->comment;
                                            } else {
                                                return "";
                                            }
                                        }
                                    ],
                                    [
                                        'label' => 'Change Status',
                                        'value' => function($data) {
                                            $pharmaOrderStaus = \app\models\PharmacyOrderStatus::find()
                                                    ->where(['pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                                    ->one();
                                            $shopStatus = \app\helpers\AppHelper::getPharmaStatusList();
                                            $status_id = null;
                                            if (!empty($pharmaOrderStaus)) {
                                                $status_id = $pharmaOrderStaus->pharmacy_status_id;
                                            }
                                            return Html::dropDownList('status', $status_id, $shopStatus, ['prompt' => 'Select Status', 'class' => 'form-control', 'onchange' => 'order.changeShopOrderStatus(this.value,' . $data->pharmacy_order_id . ')']);
                                        },
                                        'format' => 'raw',
                                    ],
                                    [
                                        'label' => 'Driver Assign',
                                        'value' => function ($data) {
                                            $shopOrderStaus = \app\models\PharmacyOrderStatus::find()
                                                    ->where(['pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->orderBy(['pharmacy_order_status_id' => SORT_DESC])
                                                    ->one();

                                            $pharmacyStatus = \app\helpers\AppHelper::getPharmaStatusList();

                                            $status_id = null;
                                            if (!empty($shopOrderStaus)) {
                                                $status_id = $shopOrderStaus->pharmacy_status_id;
                                            }

                                            $pharmacyOrderStaus1 = \app\models\DriverSuborders::find()
                                                    ->where(['order_id' => $data->order_id, 'pharmacy_order_id' => $data->pharmacy_order_id])
                                                    ->one();

                                            $driverid = '';
                                            if (!empty($pharmacyOrderStaus1)){
                                                $driverid = $pharmacyOrderStaus1->driver_id;
                                            }
                                            $orderStatus1 = \app\models\OrderStatus::find()
                                                    ->where(['order_id' => $data->order_id])
                                                    ->orderBy(['order_status_id' => SORT_DESC])
                                                    ->one();

                                            if ($status_id == 2 || $status_id == 4 || $status_id == 6) {
                                                $disabled = (!empty($orderStatus1) && $orderStatus1->status_id == 5 || $status_id == 4) ? true : false;
                                                $content_driver = Html::beginForm('', 'get', ['id' => 'assign-pickup-driver' . $data->pharmacy_order_id]) .
                                                        Html::dropDownList('driver_id', $driverid, \app\helpers\AppHelper::getDriverList(), ['prompt' => 'Select Driver', 'class' => 'form-control select2', 'disabled' => false, 'onchange' => 'order.assignPickupDriver(this.value,' . $data->pharmacy_order_id . ')']) . '<br>' .
                                                        Html::hiddenInput('order_id', $data->order_id) .
                                                        Html::hiddenInput('pharmacy_order_id', $data->pharmacy_order_id) .
                                                        Html::hiddenInput('pharmacy_id', $data->pharmacy_id) .
                                                        Html::endForm();
                                                return $content_driver;
                                            } else {
                                                return '-';
                                            }
                                        },
                                        'format' => 'raw',
                                    ],
                                ],
                            ]);
                            ?>
                        </div>
                        <?php
                    }
                    ?>


                    <div class="col-md-6" id="order-status">
                        <?php
                        if (\Yii::$app->session['_eyadatAuth'] == 1) {
                            $pharmacyOrder = \app\models\PharmacyOrders::find()
                                    ->where(['pharmacy_order_id' => $model->pharmacy_order_id])
                                    ->one();
                            $dataProvider1 = new ActiveDataProvider([
                                'query' => $pharmacyOrder->getPharmacyOrderStatuses()->orderBy(['pharmacy_order_status_id' => SORT_DESC]),
                                'pagination' => [
                                    'pageSize' => 20,
                                ],
                            ]);
                        }

                        if (\Yii::$app->session['_eyadatAuth'] == 1) {
                           
                            $orderStatusList = \app\helpers\AppHelper::getPharmaStatusList();
                        }

                        echo GridView::widget([
                            'dataProvider' => $dataProvider1,
                            'panel' => [
                                'heading' => 'Status History',
                                'type' => DetailView::TYPE_PRIMARY,
                            ],
                            'headerRowOptions' => ['style' => 'display: none;'],
                            'export' => false,
                            'toggleData' => false,
                            'summary' => '',
                            'columns' => [
                                [
                                    'label' => false,
                                    'value' => function ($data) {
                                        $newTimeZone = new \DateTimeZone('Asia/Kuwait');
                                        $dateTime = new \DateTime($data->status_date, new \DateTimeZone('UTC'));
                                        $dateTime->setTimezone($newTimeZone);
                                        $data->status_date = $dateTime->format('Y-m-d H:i:s');
                                        if (\Yii::$app->session['_eyadatAuth'] == 1) 
                                        {
                                            $content = "<b>" . date("M d, Y", strtotime($data->status_date)) . "</b> " . date("h:i A", strtotime($data->status_date)) . " | <b>" . $data->pharmacyStatus->name_en . "</b>";
                                            if (!empty($data->comment)) {
                                                $content .= "<br><h5>{$data->comment}</h5>";
                                            }
                                        }

                                        return $content;
                                    },
                                    'format' => 'raw'
                                ]
                            ],
                            /*'toolbar' => [
                                [
                                    'content' => ((empty($shopOrderStatus) || (!empty($shopOrderStatus) && ($shopOrderStatus->pharmacy_status_id != 3 && $shopOrderStatus->pharmacy_status_id != 4))) ? '<div id="response" hidden></div>' .
                                            '<p>Add Order Status</p>' .
                                            Html::beginForm('', 'get', ['id' => 'order-status-form']) .
                                            Html::dropDownList('status', '', $orderStatusList, ['prompt' => 'Select Status', 'class' => 'form-control', 'disabled' => (!empty($shopOrderStatus) && ($shopOrderStatus->pharmacy_status_id == 3 || $shopOrderStatus->pharmacy_status_id == 4)) ? 'disabled' : false]) . '<br>' .
                                            Html::textarea('comment', '', ['class' => 'form-control', 'style' => 'height: 100px; resize: none;']) .
                                            Html::hiddenInput('order_id', $model->order_id) .
                                            Html::button('Submit Comment', ['type' => 'button', 'style' => 'float: right; margin-top: 10px;', 'onclick' => 'order.addStatus()']) .
                                            Html::endForm() : ""),
                                    'options' => ['class' => 'col-md-6'],
                                    'visible'=>false,
                                ],
                            ],*/
                            'pjax' => true,
                            'pjaxSettings' => [
                                'options' => [
                                    'id' => 'order-status-pjax'
                                ]
                            ]
                        ]);
                        ?>
                    </div>




                </div>
            </div>

        </div>

    </div>

</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');

$this->registerJs("
    $(\"#formEmailInvoice\").submit(function(e) {
        $(\".global-loader\").show();
        var email  = $(\"#orderEmailId\").val();
        var order_id  = $(\"#orderID\").val();
        $.ajax({
            type: \"get\",
            url: baseUrl + 'order/send-invoice-email',
            data: {
                \"order_id\": order_id,
                \"email\": email
            },
            dataType: 'json',
            success: function (res) {
                $(\".global-loader\").hide();
                $('#response-message').text(res.message);
                if(res.status == 200){
                    $('#formAlert').removeClass('alert-danger').addClass('alert-success').show();
                    $('#orderEmailId').val('');
                } else{
                    $('#formAlert').removeClass('alert-success').addClass('alert-danger').show();
                }
                
                setTimeout(function(){ $('#formAlert').hide(); }, 5000);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(\".global-loader\").hide();
                alert(jqXHR.responseText);
            }
        });
        
        e.preventDefault();
    });
", \yii\web\View::POS_END);
?>