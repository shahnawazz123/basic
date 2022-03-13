<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use kartik\detail\DetailView;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseUrl;

$newTimeZone = new \DateTimeZone('Asia/Kuwait');
$dateTime = new \DateTime($model->create_date, new \DateTimeZone('UTC'));
$dateTime->setTimezone($newTimeZone);

$model->create_date = $dateTime->format('Y-m-d H:i:s');

$this->title = 'Order #' . $model->order_number . " | " . date("M d, Y h:i A", strtotime($model->create_date));
/* $this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
  $this->params['breadcrumbs'][] = $this->title; */

$userModel = $model->user;
$shippingAddressModel = $model->shippingAddress;
$paymentModel = app\models\Payment::find()
        ->where(['type_id' => $model->order_id,'type' => 'O'])
        ->orderBy(['payment_id' => SORT_DESC])
        ->one();
$orderItems = \app\models\OrderItems::find()
        ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
        ->where(['pharmacy_orders.order_id' => $model->order_id]);

if (\Yii::$app->session['_eyadatAuth'] == 2) {
    $orderItems->andWhere(['pharmacy_orders.pharmacy_id' => Yii::$app->user->identity->pharmacy_id]);
}
?>

<table style="width: 100%; border: none;">
    <tr>
        <td align="center" colspan="2">
            <img style="max-width: 100px" src="<?php echo yii\helpers\Url::to("@web/images/header-logo.png", true) ?>"/>
            <p>&nbsp;</p>
            <h3>Invoice #<?php echo $model->order_number ?></h3>
            <hr/>
        </td>
    </tr>

    <tr>
        <td style="text-align: left; vertical-align: top;" >
            <div class="panel-heading">
                <h3>Account Information</h3>
                <div class="panel-body">
                    <p>&nbsp;</p>
                    <p style="border: none;"><b>Customer Name : </b><?= !empty($userModel) ? ($userModel->first_name == "Guest") ? $shippingAddressModel->first_name . ' '.$shippingAddressModel->last_name :($userModel->first_name . " " . $userModel->last_name) : $model->recipient_name; ?></p>
                    <p style="border: none;"><b>Email : </b><?= !empty($userModel) ? $userModel->email : $model->shipping_email; ?></p>
                    <p style="border: none;">
                        <?php
                        $address = "";
                        if ($model->user_id == null) {

                            $areaModel = app\models\Area::findOne($model->shipping_area_id);
                            $blockModel = app\models\Block::findOne($model->shipping_block_id);
                            $address .= "<span style='font-weight: bold'>Governorate</span> : " . ((isset($areaModel) && !empty($areaModel->state->name_en)) ? $areaModel->state->name_en : "");
                            $address .= "&nbsp;<span style='font-weight: bold;'>Area</span> : " . ((isset($areaModel) && !empty($areaModel->name_en)) ? $areaModel->name_en : "");
                            $address .= "<br><span style='font-weight: bold'>Block</span> : " . ((isset($blockModel) && !empty($blockModel->name_en)) ? $blockModel->name_en : "");
                            $address .= "&nbsp;<span style='font-weight: bold'>Street</span> : " . ((isset($model->shipping_street) && !empty($model->shipping_street)) ? $model->shipping_street : "");
                            $address .= "<br><span style='font-weight: bold'>Address</span> : " . ((!empty($model->addressline_1)) ? $model->addressline_1 : "");
                            $address .= "<br><span style='font-weight: bold'>Phone</span> : " . ((!empty($model->recipient_phone)) ? $model->recipient_phone : "");
                            $address .= "<br><span style='font-weight: bold'>Alternate Phone</span> : " . ((!empty($model->shipping_alt_phone_number)) ? $model->shipping_alt_phone_number : "");

                        } else {
                            $shipping_address = [];
                            if(!empty($shippingAddressModel->avenue))
                                $shipping_address [] = 'Avenue: '.$shippingAddressModel->avenue;

                            if(!empty($shippingAddressModel->landmark))
                                $shipping_address [] = 'Landmark: '.$shippingAddressModel->landmark;

                            if(!empty($shippingAddressModel->flat))
                                $shipping_address [] = 'Flat: '.$shippingAddressModel->flat;

                            if(!empty($shippingAddressModel->floor))
                                $shipping_address [] = 'Floor: '.$shippingAddressModel->floor;

                            if(!empty($shippingAddressModel->building))
                                $shipping_address [] = 'Building: '.$shippingAddressModel->building;

                            if(!empty($shippingAddressModel->id_number))
                                $shipping_address [] = 'ID No.: '.$shippingAddressModel->id_number;

//                            if(!empty($shippingAddressModel->notes))
//                                $shipping_address [] = 'Notes.: '.$shippingAddressModel->notes;

                            $shipping_address_str = '';
                            if(!empty($shipping_address)){
                                $shipping_address_str = implode(', ',$shipping_address);
                            }

                            $address .= "<span style='font-weight: bold'>Governorate</span> : " . ((isset($shippingAddressModel->area->state->name_en) && !empty($shippingAddressModel->area->state->name_en)) ? $shippingAddressModel->area->state->name_en : "");
                            $address .= "&nbsp;<span style='font-weight: bold;'>Area</span> : " . ((isset($shippingAddressModel->area->name_en) && !empty($shippingAddressModel->area->name_en)) ? $shippingAddressModel->area->name_en : "");
                            $address .= "<br><span style='font-weight: bold'>Block</span> : " . ((isset($shippingAddressModel->block->name_en) && !empty($shippingAddressModel->block->name_en)) ? $shippingAddressModel->block->name_en : "");
                            $address .= "&nbsp;<span style='font-weight: bold'>Street</span> : " . ((isset($shippingAddressModel->street) && !empty($shippingAddressModel->street)) ? $shippingAddressModel->street : "");
                            $address .= "<br><span style='font-weight: bold'>Address</span> : " . ((!empty($shippingAddressModel->addressline_1)) ? $shippingAddressModel->addressline_1 : "");
                            $address .= "<br><span style='font-weight: bold'>Phone</span> : " . ((!empty($shippingAddressModel->mobile_number)) ? $shippingAddressModel->mobile_number : "");
                            $address .= "<br><span style='font-weight: bold'>Alternate Phone</span> : " . ((!empty($shippingAddressModel->alt_phone_number)) ? $shippingAddressModel->alt_phone_number : "");
                            $address .= "<br><span style='font-weight: bold'>Location</span> : " . ((!empty($shipping_address_str)) ? $shipping_address_str : "");
                            $address .= "<br><span style='font-weight: bold'>Notes:</span> : " . ((!empty($shippingAddressModel->notes)) ? $shippingAddressModel->notes : "");
                        }
                        echo $address;
                        ?>
                    </p>
                </div>
            </div>
        </td>
        <td style="text-align: right;  vertical-align: top;">
            <div class="panel-heading">
                <h3>Order Information</h3>
                <div class="panel-body">
                    <p>&nbsp;</p>
                    <p style="border: none;"><b>Order Date: </b><?php echo date("M d, Y h:i A", strtotime($model->create_date)) ?></p>

                    <p style="border: none;"><b>Order Number: </b><?= $model->order_number; ?></p>

                    <p style="border: none;"><b>Payment Method: </b><?= ($paymentModel->paymode == "C") ? "Cash on Delivery" : (($paymentModel->paymode == "K") ? "K-Net" : "Credit Card") ?></p>

                    <p style="border: none;"><b>Delivery Method: </b><?= ($model->delivery_option_id == "2") ? "Express Delivery" : "Standard Delivery"; ?></p>
                    
                </div>

            </div>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <br/>
            <div class="panel-heading">
                <h3>Items Ordered </h3>
                <p>&nbsp;</p>
            </div>
            <div class="panel-body">
                <?php
                $itemList = $orderItems->all();
                ?>

                <div id="w0" class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Barcode</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <?php
                            $total = ($paymentModel->gross_amount == null) ? 0 : $paymentModel->gross_amount;
                            $shippingCost = $model->delivery_charge;
                            $discountAmt = $model->discount_price;
                            $currency = 'KD';
                            $codCost = $model->cod_charge;
                            $orderTotal = 0;
                            foreach ($model->pharmacyOrders as $pharmacyOrder) {
                                foreach ($pharmacyOrder->orderItems as $oi) {
                                    $orderTotal += $oi->price * $oi->quantity;
                                }
                            }
                            $orderTotal = number_format($orderTotal, 3);
                            $vatCharges = $model->vat_charges;
                            if ($codCost > 0 && $model->payment_mode == 'C') {
                                $subtotal = $orderTotal + $shippingCost + $codCost + $vatCharges;
                            } else {
                                $subtotal = $orderTotal + $shippingCost + $vatCharges;
                            }
                            $subtotal -= $discountAmt;

                            $subtotal = number_format($subtotal, 3);
                            $codCost = number_format($codCost, 3);
                            $shippingCost = number_format($shippingCost, 3);
                            $vatCharges = number_format($vatCharges, 3);
                            $discountAmt = number_format($discountAmt, 2);
                            ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Order Total</td>
                                <td>&nbsp;</td>
                                <td>
                                    <?php
                                    echo $currency . " " . $orderTotal;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Discount Amount</td>
                                <td>&nbsp;</td>
                                <td>
                                    <?php
                                    echo $currency . " " . $discountAmt;
                                    ?>
                                </td>
                            </tr>
                            <?php
                            if ($codCost > 0 && $model->payment_mode == 'C') {
                                ?>

                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>COD Cost</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <?php
                                        echo $currency . " " . $codCost;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($shippingCost > 0) {
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Shipping Cost</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <?php
                                        echo $currency . " " . $shippingCost;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($vatCharges > 0) {
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>VAT Charges</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        <?php
                                        echo $currency . " " . $vatCharges;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Grand Total</td>
                                <td>&nbsp;</td>
                                <td>
                                    <?php
                                    echo $currency . " " . $subtotal;
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($itemList as $il) {
                                $i++;
                                $product = $il->product;
                                ?>
                                <tr data-key="<?php echo $il->product_id ?>">
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <?php
                                        if (!empty($product)) {
                                            $attributeValues = "";

                                            $attributes = $model->getProductAttributeValues($product);
                                            foreach ($attributes as $atr) {
                                                foreach ($atr['attributes'] as $option) {
                                                    $attributeValues .= "<b>{$atr['type']}</b> : {$option['value']}<br>";
                                                }
                                            }
                                            echo $product->name_en .
                                            "<br>" . $attributeValues;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($product)) {
                                            echo $product->barcode;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $il->currency->code_en . " " . $il->price;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $il->quantity;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $il->currency->code_en . " ";
                                        echo number_format($il->quantity * $il->price, 3);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>
