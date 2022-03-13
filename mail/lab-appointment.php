<!-- content -->
<table align="center" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">

    <tr>
        <td align="center">
            <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="display-width" width="680">

                <tr>
                    <td align="center">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                            <tr>
                                <td align="center" style="padding:0 30px;">
                                    <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="display-width" width="600">
                                        <tbody>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td align="left">
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%">
                                                    
                                                    <tr>
                                                        <td height="20"></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" class="MsoNormal" style="color:#000000;font-family:'Segoe UI',sans-serif,Arial,Helvetica,Lato;font-size:14px;line-height:24px;padding: 14px;">
                                                            Hello <?php echo $model->user->first_name; ?>,<br/>
                                                            Your appointment is booked successfully.
                                                            <br/> <br/>
                                                            
                                                            <strong>Appointment Details: <span style="color:#000000; "><a href="#" style="color:#000000;"> </a></span></strong>

                                                            <table class="table table-striped table-bordered" style="font-size: 10px;width: 100%;text-align:left">
                                                            <tbody>
                                                                <tr>
                                                                    <th width="50%"><strong>Appointment Date /Time</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php echo date('Y-m-d h:i A', strtotime($model->appointment_datetime));?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th width="50%"><strong>Lab Name</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php 
                                                                            echo ucwords($model->lab->name_en);
                                                                        ?>
                                                                    </td>
                                                                </tr>

                                                                    
                                                                <tr>
                                                                    <th width="50%"><strong>Lab Type</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php echo ($model->type == 'L') ? "Lab" : "Home";?>
                                                                    </td>
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <th width="50%"><strong>Appointment Booked For</strong></th>
                                                                    <td width="50%"> :
                                                                      <?=($model->kid) ? $model->kid->name_en : "Self";?>  
                                                                    </td>
                                                                </tr>  
                                                            </tbody>
                                                        </table>

                                                        <strong>Payment Details: <span style="color:#000000; "><a href="#" style="color:#000000;"> </a></span></strong>
                                                        <?php 
                                                        $payment = \app\models\Payment::find()
                                                        ->where(['type_id' => $model->lab_appointment_id, 'type' => 'LA'])
                                                        ->orderBy(['payment_id' => SORT_DESC])
                                                        ->one();
                                                        ?>
                                                            <table class="table table-striped table-bordered" style="font-size: 10px;width: 100%;text-align:left">
                                                            <tbody>
                                                                <tr>
                                                                    <th width="50%"><strong>Paymode </strong></th>
                                                                    <td width="50%"> :
                                                                        <?php 
                                                                            if($payment->paymode=='K') 
                                                                                echo "K-Net";
                                                                            else if($payment->paymode=='CC') 
                                                                                echo "Visa/Credit Card";
                                                                            else if($payment->paymode=='W') 
                                                                                echo "Discount";
                                                                            else if($payment->paymode=='C') 
                                                                                echo "Pay at clinic";

                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th width="50%"><strong>Gross Amount</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php 
                                                                            echo $payment->gross_amount.' KWD';
                                                                        ?>
                                                                    </td>
                                                                </tr>

                                                                    
                                                                <tr>
                                                                    <th width="50%"><strong>Result</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php 
                                                                            echo $payment->result;
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th width="25%"><strong>Payment Date</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php
                                                                            echo $payment->payment_date;
                                                                        
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th width="25%"><strong>Transcation Id</strong></th>
                                                                    <td width="50%"> :
                                                                        <?php 
                                                                            echo $payment->transaction_id;
                                                                        
                                                                        ?>
                                                                    </td>
                                                                </tr>  
                                                                <tr>
                                                                    <th width="25%"><strong>Track Id</strong></th>
                                                                    <td width="50%"> : 
                                                                        <?php 
                                                                            echo $payment->TrackID;
                                                                        
                                                                        ?>
                                                                    </td>
                                                                </tr>  
                                                            </tbody>
                                                        </table>


                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="20"></td>
                                                    </tr>

                                                    <tr>
                                                        <td align="center" class="MsoNormal" style="color:#ffffff;font-family:'Segoe UI',sans-serif,Arial,Helvetica,Lato;font-size:14px;line-height:24px;">
                                                            If you have any questions about your account or any other matter, please<br/>
                                                            feel free to contact us at <span style="color:#ffffff; "><a href="#" style="color:#ffffff;"><?php echo  Yii::$app->params['supportEmail'] ?></a></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                        </tr>
                                        </tbody>
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
