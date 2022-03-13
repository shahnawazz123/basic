<?php

use app\helpers\AppHelper;

$decimalPrecision = 2;
if ($baseCurrencyName == 'BHD' || $baseCurrencyName == 'KWD') {
    $decimalPrecision = 3;
}
?>

<table align="center" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center">
            <table align="center" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="display-width" width="680">
                <tr>
                    <td align="center" bgcolor="#f5f5f5" style="border-bottom:1px solid #eeeeee;padding:0 30px;">
                        <table align="center" bgcolor="#f5f5f5"  border="0" cellpadding="20" cellspacing="0" class="display-width" width="600">
                            <tr>
                                <td align="center">
                                    <table align="center"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                        <tr>
                                            <td height="40"></td>
                                        </tr>
                                        <tr>
                                            <td align="right" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:20px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                Order Confirmation
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                        <tr>
                                            <td align="right" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:11px;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                Order no: <span style="color:#000000;">#<?php echo $order_number; ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:18px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                Hello <?php echo $name ?>,
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-bottom:2px solid #333333;"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#f5f5f5">
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                    <tr>
                                                        <td height="30"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:0 30px;">
                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="48%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                <tr>
                                                                    <td align="left">
                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="width:auto !important;">
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:15px;">
                                                                                    Order date is:
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;text-transform:uppercase;">
                                                                                    <?php
                                                                                    $newTimeZone = new \DateTimeZone(\Yii::$app->params['timezone']);
                                                                                    $dateTime = new \DateTime($order->create_date, new \DateTimeZone('UTC'));
                                                                                    $dateTime->setTimezone($newTimeZone);
                                                                                    echo $dateTime->format('M d, Y h:i A');
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" width="1" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="line-height:40px;" height="40" width="1"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <table align="right" border="0" cellpadding="0" cellspacing="0" class="display-width" width="48%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                <tr>
                                                                    <td align="left">
                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="width:auto !important;">
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:15px;">
                                                                                    Your Package will be sent to:
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:24px;text-transform:uppercase;">
                                                                                    <?php echo $shippingAddress->first_name . " " . $shippingAddress->last_name ?><br/>
                                                                                    <?php echo $shippingAddress->mobile_number ?><br/>
                                                                                    <?php
                                                                                    echo $shippingAddress->street . ", " . $shippingAddress->addressline_1 . "<br>" .
                                                                                    ((!empty($shippingAddress->block)) ? $shippingAddress->block->name_en . ", " : '') . ((!empty($shippingAddress->area)) ? $shippingAddress->area->name_en . ", " : '') . ((!empty($shippingAddress->state)) ? $shippingAddress->state->name_en : '')
                                                                                    ?>
                                                                                    <?php echo $shippingAddress->country->name_en ?>
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
                                                        <td style="padding:0 30px;">
                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="48%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                <tr>
                                                                    <td align="left">
                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="width:auto !important;">
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:15px;">
                                                                                    Payment Method:
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="15"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="left" class="MsoNormal" style="color:#000000;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;text-transform:uppercase;">
                                                                                    <?php
                                                                                    echo AppHelper::$payment_mode[$payment_mode];
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="15"></td>
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
                                        <tr>
                                            <td height="30" style="border-bottom:2px solid #333333;"></td>
                                        </tr>
                                        <tr>
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" class="MsoNormal" style="color:#333;font-family: 'Overpass', sans-serif;font-size:18px;letter-spacing:1px;line-height:18px;text-align: center;">
                                                Order Summary
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20"></td>
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
                                                                            foreach ($cartDetails['items'] as $item) {
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
                                                                                                    <img src="<?php echo $item['image']; ?>" alt="60x60x1" width="90"/>
                                                                                                </td>
                                                                                                <td class="hide">
                                                                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                                        <tr>
                                                                                                            <td align="left" class="MsoNormal" style="color:#333333;font-family: 'Overpass', sans-serif;font-size:15px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;">
                                                                                                                <?php echo $item['name']; ?> X <?php echo $item['quantity'] ?>
                                                                                                                <?php
                                                                                                                $attrList = '<ul style="list-style: none">';
                                                                                                                if (!empty($item['configurable_option'])) {
                                                                                                                    foreach ($item['configurable_option'] as $row) {
                                                                                                                        foreach ($row['attributes'] as $att) {
                                                                                                                            $attrList .= '<li style="width:100px;">' . $att['value'] . '</li>';
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                $attrList .= '</ul>';
                                                                                                                echo $attrList;
                                                                                                                ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td width="20"></td>
                                                                                    <td align="left" width="90" class="MsoNormal" style="color:#333333;font-family: 'Overpass', sans-serif;font-size:14px;font-weight:600;letter-spacing:1px;line-height:18px;text-transform:capitalize;text-align:center">
                                                                                        <?php
                                                                                        $itemAmount = (cleanPrice($item['final_price']) * $item['quantity']);
                                                                                        echo $baseCurrencyName . " " . number_format(cleanPrice($itemAmount), $decimalPrecision);
                                                                                        ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td height="20"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="10" style="border-bottom:1px solid #dddddd;"></td>
                                                                                </tr>
                                                                            <?php } ?>
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
                                                <table align="right" border="0" cellpadding="0" cellspacing="0" width="48%" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto;">
                                                    <tbody>
                                                        <tr>
                                                            <td align="right">
                                                                <table align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; width:auto !important;">
                                                                    <tr>
                                                                        <td align="left" valign="top">
                                                                            <table align="left"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                <tr>
                                                                                    <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                        Item Subtotal :
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                                if ($discount_price > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            Discount :
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>

                                                                                <?php
                                                                                if ($delivery_charges > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            Shipping &amp; Handling :
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                if ($payment_mode == 'C' && cleanPrice($cod_cost) > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            COD cost :
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                $vatCharges = 0;
                                                                                if (isset($vat_pct) && $vat_pct > 0) {
                                                                                    $vatCharges = $vat_charges;
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
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
                                                                                    <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                        <?php echo $baseCurrencyName . " " . number_format(cleanPrice($sub_total), $decimalPrecision); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                                if ($discount_price > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $baseCurrencyName . " " . number_format($discount_price, $decimalPrecision); ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>

                                                                                <?php
                                                                                if ($delivery_charges > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $baseCurrencyName . " " . number_format(cleanPrice($delivery_charges), $decimalPrecision); ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                                <?php
                                                                                if ($payment_mode == 'C' && cleanPrice($cod_cost) > 0) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $baseCurrencyName . " " . number_format(cleanPrice($cod_cost), $decimalPrecision) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                $vatCharges = 0;
                                                                                if (isset($vat_pct) && $vat_pct > 0) {
                                                                                    $vatCharges = $vat_charges;
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td height="15"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;letter-spacing:1px;line-height:20px;">
                                                                                            <?php echo $baseCurrencyName . " " . $vat_charges; ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>
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
                                                            <td style="border-bottom:2px solid #333333;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right">
                                                                <table align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto !important;">
                                                                    <tr>
                                                                        <td align="right">
                                                                            <table align="right" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:auto !important;">
                                                                                <tr>
                                                                                    <td align="left" valign="top">
                                                                                        <table align="left"  border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                            <tr>
                                                                                                <td align="right" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    Grand Total :
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td width="26">&nbsp;</td>
                                                                                    <td align="left" valign="top">
                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                                                            <tr>
                                                                                                <td align="left" class="MsoNormal" style="color:#666666;font-family: 'Overpass', sans-serif;font-size:13px;font-weight:600;letter-spacing:1px;line-height:20px;">
                                                                                                    <?php
                                                                                                    $finalAmt = ((cleanPrice($sub_total) - $discount_price) + cleanPrice($delivery_charges) + cleanPrice($cod_cost) + cleanPrice($vat_charges) - cleanPrice($wallet_amount_applied));
                                                                                                    echo $baseCurrencyName . " " . number_format($finalAmt, $decimalPrecision);
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
<!-- SHIPMENT CONFIRMATION ENDS -->
