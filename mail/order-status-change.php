<table align="center" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center">
            <table align="center" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="display-width" width="680">
                <tr>
                    <td align="center" bgcolor="#f5f5f5"; style="border-bottom:1px solid #eeeeee;padding:0 30px;">
                        <table align="center" bgcolor="#f5f5f5"  border="0" cellpadding="20" cellspacing="0" class="display-width" width="600">
                            <tr>
                                <td align="center">
                                    <table align="center"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:18px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                Dear <?php echo $name; ?>,
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" class="MsoNormal" style="color: #333333;font-family: 'Overpass', sans-serif;font-size:13px;line-height:24px;">
                                                The status of your order <b style="color: #000000">#<?php echo $order_number ?></b> has been changed to <b style="color: #000000"><?php echo strtoupper($status) ?></b><br/>
                                            </td>
                                        </tr>
                                        <?php
                                        if ($notify_customer == 1) {
                                            ?>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                            <tr>
                                                <td align="left">
                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="85%">
                                                        <tr>
                                                            <td align="left" class="MsoNormal" style="color: #333333;font-family: 'Overpass', sans-serif;font-size:14px;line-height:24px;">
                                                                <?php echo $comment ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="30"></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <td align="left" class="MsoNormal" style="color:#333;font-family: 'Overpass', sans-serif;font-size:18px;letter-spacing:1px;line-height:18px;text-align: center;">
                                                Order Summary
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                    <tr>
                                                        <td width="600">
                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;border-top: 2px solid #555;border-bottom: 2px solid #555;">
                                                                <tr>
                                                                    <td bgcolor="#f6f6f6">
                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                            <?php
                                                                            $total = 0;
                                                                            $code = '';
                                                                            foreach ($model->pharmacyOrders as $shopOrder) {
                                                                                foreach ($shopOrder->orderItems as $items) {
                                                                                    $singleItemConvertPrice = \app\helpers\AppHelper::convertPriceV2($items->price, $items->product->base_currency_id, $store->currency_id);
                                                                                    $singleItemPrice = cleanPrice($singleItemConvertPrice) * $items->quantity;
                                                                                    $total = $total + $singleItemPrice;
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="20"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td width="20"></td>
                                                                                        <td align="left" width="300" class="title-width">
                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                                <tr>
                                                                                                    <td align="left" valign="middle" width="100">
                                                                                                        <?php
                                                                                                        $img = app\helpers\ProductHelper::getProductDefaultImage($items->product_id);
                                                                                                        ?>
                                                                                                        <img src="<?php echo $img; ?>" width="90"/>
                                                                                                    </td>
                                                                                                    <td class="hide">
                                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                                            <tr>
                                                                                                                <td align="left" class="MsoNormal" style="color:#333333;font-family: 'Overpass', sans-serif;font-size:15px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                                                                                    <?php echo ($items->product_id != null) ? $items->product->name_en : $items->bundle->name_en ?> X <?php echo $items->quantity; ?>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td height="5"></td>
                                                                                                            </tr>
                                                                                                        </table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                        <td width="20"></td>
                                                                                        <td align="left" width="90" class="MsoNormal" style="color:#333333;font-family: 'Overpass', sans-serif;font-size:14px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;text-align:center">
                                                                                            <?php
                                                                                            $code = $store->currency->code_en;
                                                                                            $itemTotalConvertPrice = \app\helpers\AppHelper::convertPriceV2($items->price, $items->product->base_currency_id, $store->currency_id);
                                                                                            $itemTotal = cleanPrice($itemTotalConvertPrice) * $items->quantity;
                                                                                            echo app\helpers\AppHelper::formatPrice($itemTotal, $code) . '&nbsp;' . $code
                                                                                            ?>
                                                                                        </td>
                                                                                        <td width="20"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td height="20"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td colspan="10" style="border-bottom:1px solid #dddddd;"></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!-- SUB TOTAL -->

                                                <table align="right" border="0" cellpadding="0" cellspacing="0" class="display-width" width="48%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto;">
                                                    <tbody>
                                                        <tr>
                                                            <td align="right">
                                                                <table align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; width:auto !important;">
                                                                    <tr>
                                                                        <td align="left" valign="top">
                                                                            <table align="left"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                <tr>
                                                                                    <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                        Item Subtotal :
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                                $delivery_charges = \app\helpers\AppHelper::convertPriceV2($model->delivery_charge, 82, $store->currency_id);
                                                                                if ($delivery_charges > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            Shipping &amp; Handling :
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php } ?>
                                                                                <?php
                                                                                $cod_cost = \app\helpers\AppHelper::convertPriceV2($model->cod_charge, 82, $store->currency_id);
                                                                                if ($model->payment_mode == 'C' && cleanPrice($cod_cost) > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            COD cost :
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                if (isset($model->vat_charges) && $model->vat_charges > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            VAT Charges :
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </table>
                                                                        </td>
                                                                        <td width="26">&nbsp;</td>
                                                                        <td align="left" valign="top">
                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                <tr>
                                                                                    <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                        <?php echo $code . " " . app\helpers\AppHelper::formatPrice($total, $code); ?>
                                                                                    </td>
                                                                                </tr>

                                                                                <?php
                                                                                if ($delivery_charges > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $code . " " . app\helpers\AppHelper::formatPrice(cleanPrice($delivery_charges), $code); ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php } if ($model->payment_mode == 'C' && cleanPrice($cod_cost) > 0) { ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $code . " " . app\helpers\AppHelper::formatPrice(cleanPrice($cod_cost), $code); ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                $vatCharges = 0;
                                                                                if (isset($model->vat_charges) && $model->vat_charges > 0) {
                                                                                    $vatCharges = \app\helpers\AppHelper::convertPriceV2($model->vat_charges, 82, $store->currency_id);
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $code . " " . app\helpers\AppHelper::formatPrice(cleanPrice($vatCharges), $code); ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php } ?>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border-bottom: 2px solid #555555;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right">
                                                                <table align="right" border="0" cellpadding="0" cellspacing="0" width="85%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto;">
                                                                    <tr>
                                                                        <td align="right">
                                                                            <table align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto !important;">
                                                                                <tr>
                                                                                    <td align="left" valign="top">
                                                                                        <table align="left"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                            <tr>
                                                                                                <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    Total :
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td height="10"></td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    Amount Paid :
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td width="26">&nbsp;</td>
                                                                                    <td align="left" valign="top">
                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                            <tr>
                                                                                                <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    <?php
                                                                                                    $finalAmt = (cleanPrice($total) + cleanPrice($delivery_charges) + cleanPrice($cod_cost)) + cleanPrice($vatCharges);
                                                                                                    echo $code . " " . app\helpers\AppHelper::formatPrice(cleanPrice($finalAmt), $code);
                                                                                                    ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td height="10"></td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td align="right" class="MsoNormal" style="color: #000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    <?php
                                                                                                    $finalAmt = (cleanPrice($total) + cleanPrice($delivery_charges) + cleanPrice($cod_cost)) + cleanPrice($vatCharges);
                                                                                                    echo $code . " " . app\helpers\AppHelper::formatPrice(cleanPrice($finalAmt), $code);
                                                                                                    ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="40"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- SHIPMENT TRACKING ENDS -->
