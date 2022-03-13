var isProcessing = 0;
var order = {
    updateStatus: function (order_id, status_id, refresh)
    {
        if ($.trim(order_id) != "" && $.trim(status_id) != "")
        {
            $(".global-loader").show();

            $.ajax({
                type: "GET",
                url: baseUrl + 'order/update-status',
                data: {
                    'order_id': order_id,
                    'status_id': status_id,
                },
                success: function (response)
                {
                    $(".global-loader").hide();
                    alert(response);
                    if (refresh == 1)
                    {
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    bulkStatus: function () {
        //debugger;
        var keys = $('#w1').yiiGridView('getSelectedRows');
        var selected = keys.join();

        if(selected != "") {
            $("#bulkStatusChangeModal").modal("show");
        } else {
            swal("Please select at lease one order!");
        }
    },
    bulkStatusChange: function () {
        var orders = $('#w1').yiiGridView('getSelectedRows');
        var statusId = $('#bulk_status_id').val();
        var comment = $('#bulk_status_comment').val();
        var notify = $("#notify").is(":checked");
        notify = (notify === true) ? 1 : 0;

        if(!statusId){
            $('.field-status > .help-block').show();
            return false;
        } else {
            $('.field-status > .help-block').hide();
        }
        if(orders.length > 0) {
            $(".global-loader").show();
            $.ajax({
                type: "POST",
                url: baseUrl + 'order/change-bulk-status',
                data: {'status': statusId, 'order_id': orders.join(), 'comment': comment, "notify": notify},
                success: function (response)
                {
                    $(".global-loader").hide();
                    var result = JSON.parse(response);

                    if (result.status == 200) {
                        $("#bulkModelAlert").removeClass("alert-danger").addClass("alert-success")
                            .html("<strong>"+result.msg+"</strong>").show();
                        // $.pjax.reload({container: '#order-list-pjax'});
                        $("#bulk_status_id").val("");
                        $("#bulk_status_comment").val("");
                        $("#notify").val("");
                        window.location.reload();
                        //swal success




                    } else{
                        $("#bulkModelAlert").removeClass("alert-success").addClass("alert-danger")
                            .html("<strong>"+result.msg+"</strong>").show();
                    }
                    setTimeout(function (){$("#bulkModelAlert").hide(); $("#bulkStatusChangeModal").modal("hide");}, 5000);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    bulkOrderAssign: function () {
        //debugger;
        var keys = $('#w0').yiiGridView('getSelectedRows');
        var selected = keys.join();

        if(selected != "") {
            $("#bulkDriverUpdate").modal("show");
        } else {
            swal("Please select at lease one order!");
        }
    },
    bulkOrderAssignUpdate: function () {
        var orders = $('#w0').yiiGridView('getSelectedRows');
        var driver_id = $("#bulk_driver_update").val();
        if(driver_id == "") {
            swal("Please select driver!");
        }else
        {
            if(orders.length > 0) 
            {
                $(".global-loader").show();
                    $.ajax({
                        type: "POST",
                        url: baseUrl + 'order/bulk-pharmacy-driver-assign',
                        data: {'driver_id': driver_id, 'order_id': orders.join()},
                        success: function (response)
                        {
                            $(".global-loader").hide();
                            var result = JSON.parse(response);

                            if (result.status == 200) {
                                $("#bulkModelAlert1").removeClass("alert-danger").addClass("alert-success")
                                    .html("<strong>"+result.msg+"</strong>").show();
                                //$.pjax.reload({container: '#order-list-pjax'});
                                $("#bulk_status_id").val("");
                                setTimeout(function (){
                                        $("#bulkModelAlert1").hide(); 
                                        $("#bulk_driver_update").modal("hide");
                                        
                                location.reload();
                                    }, 3000);
                            } else{
                                $("#bulkModelAlert").removeClass("alert-success").addClass("alert-danger")
                                    .html("<strong>"+result.msg+"</strong>").show();
                            }

                            setTimeout(function (){$("#bulkModelAlert1").hide(); $("#bulk_driver_update").modal("hide");}, 5000);
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            $(".global-loader").hide();
                            alert(jqXHR.responseText);
                        }
                    });
            }
        }
    },
    addStatus1: function () {
        swal({
            title: "Are you sure you want to change the status for this order?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }, function (isConfirm) {
            if (isConfirm) {
                $(".global-loader").show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + 'order/add-status',
                    data: $("#order-status-form").serialize(),
                    success: function (response)
                    {
                        $(".global-loader").hide();

                        var result = JSON.parse(response);

                        if (result.status == 201) {
                            $("#response").html('<div class="alert alert-danger">' + result.msg + '</div>');
                        }

                        if (result.status == 200) {
                            $("#response").html('<div class="alert alert-success">' + result.msg + '</div>');
                            $.pjax.reload({container: '#order-status-pjax'});
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        });
    },
    addStatus: function () {
                var statusid = $(".statusid").val();
                if(statusid!='')
                {
                    $(".global-loader").show();
                    var st_id = $("#order-status-form :selected").val();
                    var form_auth_id = $(".form_auth_id").val();
                    
                    $.ajax({
                        type: "POST",
                        url: baseUrl + 'order/add-status',
                        data: $("#order-status-form").serialize(),
                        success: function (response)
                        {
                            $(".global-loader").hide();

                            var result = JSON.parse(response);

                            if (result.status == 201) {
                                $("#response").html('<div class="alert alert-danger">' + result.msg + '</div>');
                            }

                            if (result.status == 200) {
                                $("#response").html('<div class="alert alert-success">' + result.msg + '</div>');
                                $.pjax.reload({container: '#order-status-pjax'});
                                if(st_id==7)
                                {
                                    $("#driverpop").modal('show');
                                }
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            $(".global-loader").hide();
                            alert(jqXHR.responseText);
                        }
                    });
                }else{
                    $("#response").html('<div class="alert alert-danger">Please select order status</div>');
                }
            /*}
        });*/
    },
    assignPickupDriver: function (driver_id, pharmacy_order_id) {
        swal({
            title: "Are you sure you want to assign this order to the selected driver?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }, function (isConfirm) {
            if (isConfirm) {
                $(".global-loader").show();
                $.ajax({ 
                    type: "POST",
                    url: baseUrl + 'order/assign-pickup-order',
                    data: $("#assign-pickup-driver"+pharmacy_order_id).serialize(),
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        console.write(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        });
    },
    assignDriver: function () {
        swal({
            title: "Are you sure you want to assign this order to the selected driver?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }, function (isConfirm) {
            if (isConfirm) {
                $(".global-loader").show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + 'order/assign-order',
                    data: $("#assign-driver-form").serialize(),
                    success: function (response)
                    {
                        //alert();
                        $(".global-loader").hide();

                        var result = JSON.parse(response);
                       
                        if (result.status == 500) {
                            $("#assign_response").html('<div class="alert alert-danger">' + result.msg + '</div>');
                        }

                        if (result.status == 200) {
                            $("#assign_response").html('<div class="alert alert-success">' + result.msg + '</div>');
                            
                            $("#assign-driver-form").show();
                            $.pjax.reload({container: '#assign-driver-pjax'});
                            setTimeout(function ()
                            {
                               $("#driverpop").modal('hide');
                               location.reload();
                            }, 3000);
                            

                            $("#assign-driver-form").css('display','block');
                        }else{
                            //.reload();
                            setTimeout(function ()
                            {
                               location.reload();
                            }, 3000);
                            $("#assign-driver-form").css('display','block');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        });
    },
    unassignDriver: function (order,driver_order_id) {
        swal({
            title: "Are you sure you want to unassign this order from the selected driver?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }, function (isConfirm) {
            if (isConfirm) {
                $(".global-loader").show();
                $.ajax({
                    type: "GET",
                    url: baseUrl + "order/unassign-order?id=" + order+"&driver_order_id="+driver_order_id,
                    success: function (response)
                    {
                        $(".global-loader").hide();

                        var result = JSON.parse(response);

                        if (result.status == 500) {
                            $("#assign_response").html('<div class="alert alert-danger">' + result.msg + '</div>');
                        }

                        if (result.status == 200) {
                            $("#assign_response").html('<div class="alert alert-success">' + result.msg + '</div>');
                            $.pjax.reload({container: '#assign-driver-pjax'});
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        });
    },
    addBoutiqueStatus: function () {
        $.ajax({
            type: "POST",
            url: baseUrl + 'order/add-boutique-status',
            data: $("#order-status-boutique-form").serialize(),
            success: function (response)
            {
                $(".global-loader").hide();

                var result = JSON.parse(response);

                if (result.status == 200) {
                    $.pjax.reload({container: '#order-status-boutique-pjax'});
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                //alert(jqXHR.responseText);
            }
        })
    },
    updateShippingInfo: function () {
        $.ajax({
            type: "POST",
            url: baseUrl + 'order/update-shipping-info',
            data: $("#shipping-information").serialize(),
            success: function (response)
            {
                $(".global-loader").hide();
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                //alert(jqXHR.responseText);
            }
        });

        return false;
    },
    changeOrderSwitch: function (url, trigger, id)
    {
        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
        }

        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
                $(".global-loader").hide();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },

    openOrderEmailPopup: function (id, qty)
    {
        $("#orderEmailId").val("");
        $("#orderDetailEmailModal").modal('show');
    },

    sendInvoiceEmail: function () {
        $(".global-loader").show();

        $('#formEmailInvoice').preventDefault();

        var email = $("#orderEmailId").val();
        var order_id = $("#orderID").val();

        $.ajax({
            type: "get",
            url: baseUrl + 'order/send-invoice-email',
            data: {
                "order_id": order_id,
                "email": email
            },
            dataType: 'json',
            success: function (res) {
                $(".global-loader").hide();
                console.log(res);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });

        return false;
    },
    openDispatchModal: function ()
    {
        $("#orderDispatchModal").modal("show");
    },
    getBarcodeItem: function (oid)
    {
        var barcode = $("#barcode").val();
        //console.log(barcode);
        if ($.trim(barcode) != "")
        {
            $.ajax({
                type: "get",
                url: baseUrl + 'order/get-item-info',
                data: {
                    "order_id": oid,
                    "barcode": barcode
                },
                //dataType: 'json',
                success: function (res) {
                    $(".global-loader").hide();
                    var obj = $.parseJSON(res);
                    if (obj.status == 200)
                    {
                        $("#qty").val(obj.data.qty);
                        var htm = '<tr><td>' + obj.data.name + '</td><td>' + obj.data.sku + '</td><td>' + obj.data.price + '</td><td>' + obj.data.total + '</td></tr>';
                        $("#item-listing").append(htm);
                        if (obj.completed == 1)
                        {
                            $("#confirmBtn").removeAttr("disabled");
                            $("#confirmBtn").attr("onclick", "order.confirmOrder(" + oid + ")")
                        } else {
                            $("#confirmBtn").attr("disabled", "disabled");
                            $("#confirmBtn").removeAttr("onclick");
                        }
                        $("#barcode").val("");
                        $("#qty").val(0);
                    } else if (obj.status == 202)
                    {
                        $("#qty").val(obj.data.qty);
                    } else {
                        swal("", obj.msg, 'warning');
                        $("#barcode").val("");
                        $("#qty").val(0);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            });
        }
    },
    modifyQtyBarCodeItem: function (id, term)
    {
        var barcode = $("#barcode").val();
        if ($.trim(barcode)) {
            var strqty = $("#qty").val();
            var qty = parseInt(strqty);
            var sendReq = 0;
            if (term == 'A')
            {
                qty += 1;
                $("#qty").val(qty);
                sendReq = 1;
            } else {
                if (qty > 0)
                {
                    qty -= 1;
                    $("#qty").val(qty);
                    sendReq = 1;
                } else {
                    qty = 0;
                    $("#qty").val(qty);
                    sendReq = 0;
                }
            }
            if (sendReq == 1) {
                var oid = id;
                if (isProcessing == 0) {
                    isProcessing = 1;
                    $.ajax({
                        type: "get",
                        url: baseUrl + 'order/get-item-info',
                        data: {
                            "order_id": oid,
                            "barcode": barcode,
                            "term": term,
                        },
                        //dataType: 'json',
                        success: function (res) {
                            isProcessing = 0;
                            $(".global-loader").hide();
                            var obj = $.parseJSON(res);
                            if (obj.status == 200)
                            {
                                $("#qty").val(obj.data.qty);
                                var htm = '<tr><td>' + obj.data.name + '</td><td>' + obj.data.sku + '</td><td>' + obj.data.price + '</td><td>' + obj.data.total + '</td></tr>';
                                $("#item-listing").append(htm);
                                if (obj.completed == 1)
                                {
                                    $("#confirmBtn").removeAttr("disabled");
                                    $("#confirmBtn").attr("onclick", "order.confirmOrder(" + oid + ")")
                                    $("#decrease-btn").attr("onclick", "order.modifyQtyBarCodeItem(" + oid + ",'S')");
                                    $("#increase-btn").attr("onclick", "order.modifyQtyBarCodeItem(" + oid + ",'A')");
                                } else {
                                    $("#confirmBtn").attr("disabled", "disabled");
                                    $("#confirmBtn").removeAttr("onclick");
                                    $("#decrease-btn").removeAttr("onclick");
                                    $("#increase-btn").removeAttr("onclick");
                                }
                                $("#barcode").val("");
                                $("#qty").val(0);
                                sendReq = 0;
                            } else if (obj.status == 202)
                            {
                                $("#qty").val(obj.data.qty);
                            } else {
                                swal("", obj.msg, 'warning');
                                $("#barcode").val("");
                                $("#qty").val(0);
                                sendReq = 0;
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            $(".global-loader").hide();
                            alert(jqXHR.responseText);
                        }
                    });
                }
            }
        }
    },
    confirmOrder: function (id)
    {
        $.ajax({
            type: "POST",
            url: baseUrl + 'order/add-status',
            data: {
                'comment': "",
                'id': id,
                'order_id': id,
                'status': 8,
            },
            success: function (response)
            {
                $(".global-loader").hide();
                var result = JSON.parse(response);
                if (result.status == 201) {
                    $("#response").html('<div class="alert alert-danger">' + result.msg + '</div>');
                    swal("", result.msg, 'warning');
                }
                if (result.status == 200) {
                    $("#response").html('<div class="alert alert-success">' + result.msg + '</div>');
                    swal("", result.msg, 'success');
                    $("#confirmBtn").attr("disabled", "disabled");
                    $("#confirmBtn").removeAttr("onclick");
                    $.pjax.reload({container: '#order-status-pjax'});
                }
                $("#orderDispatchModal").modal("hide");
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    changeShopOrderStatus: function (status_id, shop_order_id)
    {
        if ($.trim(status_id) != "" && $.trim(shop_order_id) != "")
        {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'order/add-shop-order-status',
                data: {
                    'status_id': status_id,
                    'pharmacy_order_id': shop_order_id,
                },
                success: function (response)
                {
                    // console.log(response);
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);
                    alert(obj.msg);
                    if (obj.status == 200)
                    {
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    }
}


