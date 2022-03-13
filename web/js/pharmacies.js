/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Supplier {
    showHideDeliveryCharge() {
        if ($("#pharmacies-is_free_delivery").is(":checked")) {
            $("#deliveryCharge").hide();
        } else {
            $("#deliveryCharge").show();
        }
    }
    showHideSupportLogin() {
        if ($("#pharmacies-enable_login").is(":checked")) {
            $("#login-section").show();
            jQuery('#w0').yiiActiveForm("add", {
                "id": 'pharmacies-email',
                "name": "pharmacies[email]",
                "container": ".field-pharmacies-email",
                "input": '#pharmacies-email',
                "validate": function (attribute, value, messages, deferred, $form) {
                    if ($("#pharmacies-enable_login").is(":checked") == true) {
                        yii.validation.required(value, messages, { "message": "Email can\'t be blank" });
                        yii.validation.email(value, messages, {
                            "pattern": /^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,
                            "fullPattern": /^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,
                            "allowName": false,
                            "message": "Email is not a valid email address.",
                            "enableIDN": false,
                            "skipOnEmpty": 1
                        });
                    }

                }
            });
            jQuery('#w0').yiiActiveForm("add", {
                "id": 'pharmacies-password_hash',
                "name": "pharmacies[password_hash]",
                "container": ".field-pharmacies-password_hash",
                "input": '#pharmacies-password_hash',
                "validate": function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, { "message": "Password can\'t be blank" });
                }
            });
            jQuery('#w0').yiiActiveForm("add", {
                "id": 'pharmacies-confirm_password',
                "name": "pharmacies[confirm_password]",
                "container": ".field-pharmacies-confirm_password",
                "input": '#pharmacies-confirm_password',
                "validate": function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, { "message": "Confirm Password can\'t be blank" });
                    yii.validation.compare(value, messages, {
                        "operator": "==",
                        "type": "string",
                        "compareAttribute": "pharmacies-password_hash",
                        "skipOnEmpty": 1,
                        "message": "Password and Confirm password must match"
                    }, $form);
                }
            });
        } else {
            $("#login-section").hide();
            $("#pharmacies-email").val("");
            $("#pharmacies-password_hash").val("");
            $("#pharmacies-confirm_password").val("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-email');
            $(".field-pharmacies-email").removeClass("has-error");
            $(".field-pharmacies-email").addClass("has-success");
            $(".field-pharmacies-email .help-block").html("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-password_hash');
            $(".field-pharmacies-password_hash").removeClass("has-error");
            $(".field-pharmacies-password_hash").addClass("has-success");
            $(".field-pharmacies-password_hash .help-block").html("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-confirm_password');
            $(".field-pharmacies-confirm_password").removeClass("has-error");
            $(".field-pharmacies-confirm_password").addClass("has-success");
            $(".field-pharmacies-confirm_password .help-block").html("");
            //
        }
    }
    callGotap(shop_id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'shop/gotapapi',
            data: {
                'shop_id': shop_id,
            },
            success: function (response) {
                $(".global-loader").hide();
                //alert(response);
                console.log(response);
                var obj = $.parseJSON(response);
                if (obj.status == 'success') {

                    $(".btncall").text('View Go Tap Details');
                    $("#gotappop").modal('show');
                    $("#gotappop .modal-body").css('color', 'black');
                    $("#gotappop .modal-body").html(obj.message);
                } else {
                    $("#gotappop").modal('show');
                    $("#gotappop .modal-body").css('color', 'red');
                    $("#gotappop .modal-body").html('<center>' + obj.message + '</center>');
                }

                //console.log(response);
                /*$("#attributes").html(response);
                $("select.select5").select2();
                var newSourceUrl = associatedProductUrl + "&att_set=" + attSet;
                //alert(newSourceUrl);
                var oTable = $('#associatedProduct').DataTable();
                oTable.ajax.url(newSourceUrl);
                oTable.draw();*/
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    }



}

var pharmacies = new Supplier;