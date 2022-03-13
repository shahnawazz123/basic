var common = {
    addvalidation: function (form_id, id, name, container, errorMessage) {
        $(container).addClass("required");
        jQuery('#' + form_id).yiiActiveForm("add", {
            "id": id,
            "name": name,
            "container": container,
            "input": '#' + id,
            "validate": function (attribute, value, messages, deferred) {
                yii.validation.required(value, messages, { "message": errorMessage });
            }
        });
    },
    triggerClinicInfo: function () {
        console.log("focus unchanged")
        $('#doctors-clinic_id').trigger("focus")
        setTimeout(() => {
            $('#doctors-name_en').focus()
        }, 1000);

        console.log("focus changed")
    },
    checkDoctorWorkingTime: function (id) {
        var start_time = $(".start_time_picker_" + id).val()
        var end_time = $(".end_time_picker_" + id).val()
        var startDate = Date.parse('01/01/2001 ' + start_time);
        var endDate = Date.parse('01/01/2001 ' + end_time);
        $(".end_time_picker_" + id).on('focusout', (event) => {
            if (startDate > endDate) {
                setTimeout(() => {
                }, 500);
                $(".end_time_picker_" + id)
                    .closest(".bootstrap-timepicker")
                    .addClass('has-error')
                    .attr("aria-required", "true").attr("aria-invalid", "true")
                $('.btn[type=submit').hide();
            }
            else {
                $(".end_time_picker_" + id)
                    .closest(".bootstrap-timepicker")
                    .removeClass('has-error')
                    .attr("aria-required", "false").attr("aria-invalid", "false")
                $('.btn[type=submit').show();
            }
        });

    },
    addImageFileValidation: function (form_id, id, name, container, errorMessage) {
        $(container).addClass("required");
        jQuery('#' + form_id).yiiActiveForm("add", {
            "id": id,
            "name": name,
            "container": container,
            "input": '#' + id,
            "validate": function (attribute, value, messages, deferred) {
                yii.validation.required(value, messages, { "message": errorMessage });
                yii.validation.file(attribute, messages, {
                    "message": "File upload failed.",
                    "skipOnEmpty": true,
                    "mimeTypes": [],
                    "wrongMimeType": "Only files with these MIME types are allowed: .",
                    "extensions": ["png", "jpeg", "jpg"],
                    "wrongExtension": "Only files with these extensions are allowed: png, jpeg, jpg.",
                    "maxFiles": 1,
                    "tooMany": "You can upload at most 1 file."
                });
            }
        });
    },
    removeValidation: function (form_id, field_id, field_class) {
        $('#' + form_id).yiiActiveForm('remove', field_id);
        $(field_class).removeClass('has-error');
        $(field_class).removeClass("required");
        $(field_class).addClass('has-success');
        $(field_class + " .help-block").html('');
    },
    getState: function (country_id, id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'area/get-states',
            data: {
                'country_id': country_id,
            },
            success: function (response) {
                $(".global-loader").hide();
                // $("#" + id).html(response);
                $("#" + id).html(response).trigger("change");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    getCity: function (state, id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'area/get-area',
            data: {
                'state': state,
            },
            success: function (response) {
                $(".global-loader").hide();
                $("#" + id).html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    getCityAjax: function (state, id, selected_id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'area/get-area-ajax',
            data: {
                'state': state,
                'selected_id': selected_id,
            },
            success: function (response) {
                $(".global-loader").hide();
                $("#" + id).html(response);

                $('#' + id).select2().select2('val', selected_id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    getBlock: function (city, id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'area/get-block',
            data: {
                'city': city,
            },
            success: function (response) {
                $(".global-loader").hide();
                $("#" + id).html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    search: function (value) {
        $(".global-loader").show();

        /*$.ajax({
            type: "POST",
            url: baseUrl + 'site/search',
            data: {
                'q': value,
            },
            success: function (response)
            {
                $(".global-loader").hide();
     
                var data = jQuery.parseJSON(response);
                var html = '';
     
                $.each(data.products, function(index, product) {
                    html += '<div class="col-md-3 col-sm-4 col-xs-6 product-item">'+
                            '<p class="product-img">'+
                            '<a href="'+product.details_url+'">'+
                            '<img class="img-responsive" src="'+product.image+'">'+
                            '</a>'+
                            '</p>'+
                            '<p class="product-title">'+
                            '<a href="'+product.details_url+'" data-cid="">'+product.name+'</a>'+
                            '</p>'+
                            '<div class="product-price">'+
                            '<span class="discounted-price">'+product.currency+' '+product.final_price+'</span>';
     
                    if(product.regular_price != null && product.regular_price != product.final_price)
                        html += '<span class="original-price">'+product.currency+' '+product.regular_price+'</span>';
     
                    html += '</div></div>';
                });
                $.pjax.reload({container:'#product-list'});
     
                $("#product-listing").html(html);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                console.log(jqXHR.responseText);
            }
        })*/
    },
    publishUnpublish: function (url, msg) {
        var r = confirm(msg);
        if (r == true) {
            $.ajax({
                type: "GET",
                url: baseUrl + url,
                success: function (res) {
                    if (res == '1') {
                        location.reload()
                    }
                    else {
                        alert('Something went wrong please try again');
                    }
                }
            })
        }
    },
    status: function (url, trigger, id) {
        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
        }
        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
    getListByType: function (type, field, elem) {
        var label = '';
        if ($.trim(type) != '') {
            if (type == 'C') {
                label = 'Category list';
            }
            else if (type == 'P') {
                label = 'Product list';
            }
            else if (type == 'BR') {
                label = 'Brand list';
            }

            $("." + field + " label").html(label);
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'banner/get-list',
                data: {
                    'type': type,
                },
                success: function (response) {
                    $(".global-loader").hide();
                    $("#" + elem).html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            });
        }
        else {
            $("#" + elem).html("");
        }
    },

    sendUserPush: function () {
        var uid = $("#pushItem").val();
        var msg = $("#pushMsg").val();
        //var r = confirm("Are you sure you want to send push for that product?");
        if ($.trim(uid) != "" && $.trim(msg) != "") {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'user/send-push',
                data: {
                    'id': uid,
                    'msg': msg
                },
                success: function (response) {
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);
                    if (obj.success == '1') {
                        $("#pushItem").val("");
                        $("#pushMsg, #pushTitle").val("");
                        $("#pushResult").html("<div class=\"alert alert-success\">" + obj.msg + "</div>");
                    } else {
                        alert(obj.msg);
                    }
                    //alert(obj.msg);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    },

    openPushPopup: function (id, msg) {
        $("#pushItem").val("");
        $("#pushMsg").val("");
        $("#pushModal").modal('show');
        $("#pushMsg").val(msg);
        $("#pushItem").val(id);
    },

    getListByPushTitle: function (type) {
        $("#notifications-id").val(null).trigger("change");
        var label = 'Link Name';
        if ($.trim(type) != '') {
            if (type == 'CL') {
                label = 'Category list';
            }
            else if (type == 'P') {
                label = 'Product list';
            }
            else if (type == 'BR') {
                label = 'Brand list';
            }

            $(".field-notifications-id label").html(label);

            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'banner/get-list',
                data: {
                    'type': type,
                },
                success: function (response) {
                    $(".global-loader").hide();
                    $("#notifications-id").html(response);
                    $(".push-value").slideDown();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
        else {
            $("#notifications-id").html("");
        }
    },
    getBrand: function (shop_id, id) {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'brand/get-brands',
            data: {
                'shop_id': shop_id,
            },
            success: function (response) {
                $(".global-loader").hide();
                $("#" + id).html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    changeStatus: function (url, trigger, id) {
        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
        }
        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },

    changeAppoitmentStatus: function (url, trigger, id) {

        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
            $("#modal_doctor_appointment_id").val(id);
        }

        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
                if (res == 1) {
                    $("#myonoffswitch" + id).attr('disabled', true);
                    $("#return" + id).text('Completed');
                    $("#UploadReport").modal('show');
                }

                if (status == 1)
                    $("#UploadReport").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
    changeLabStatus: function (url, trigger, id) {

        var status = 0;
        if ($('#' + trigger.id).is(":checked")) {
            status = 1;
            $("#modal_lab_appointment_id").val(id);
        }
        $.ajax({
            type: "GET",
            url: baseUrl + url,
            data: {
                "id": id
            },
            success: function (res) {
                if (res == 1) {
                    $("#myonoffswitch" + id).attr('disabled', true);
                    $("#return" + id).text('Completed');
                }
                if (status == 1)
                    $("#LabReport").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
    addLabReport: function () {
        $(".global-loader").show();
        var pdf_file = $("#labs-uploaded_report").val();
        var lab_appointment_id = $("#modal_lab_appointment_id").val();
        var lab_report_title_en = $("#modal_report_title_en").val();
        var lab_report_title_ar = $("#modal_report_title_ar").val();
        if (pdf_file == '' || lab_report_title_en == '' || lab_report_title_ar == "") {
            $(".global-loader").hide();
            //$(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
            $(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
                .html("<strong>Please fill all the * marked fields</strong>").show();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'lab-appointment/add-lab-report',
                data: {
                    'pdf_file': pdf_file,
                    'lab_appointment_id': lab_appointment_id,
                    'report_title_en': lab_report_title_en,
                    'report_title_ar': lab_report_title_ar
                },
                success: function (response) {
                    $(".global-loader").hide();
                    var result = JSON.parse(response);

                    if (result.status == 200) {
                        $("#w1").hide();
                        $(".msg").removeClass("alert alert-danger").addClass("alert alert-success")
                            .html("<strong>" + result.msg + "</strong>").show();
                        $("#labs-uploaded_report").val('');
                        $("#modal_lab_appointment_id").val('');
                        setTimeout(function () {
                            $("#LabReport").modal('hide');
                            location.reload();
                        }, 2000);
                    } else {
                        $("#w1").show();
                        $(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
                            .html("<strong>" + result.msg + "</strong>").show();
                    }
                }
            });
        }
    },
    addDoctorReport: function () {
        $(".global-loader").show();
        var pdf_file = $("#doctors-uploaded_report").val();
        var doctor_appointment_id = $("#modal_doctor_appointment_id").val();
        var doctor_report_title_en = $("#modal_report_title_en").val();
        var doctor_report_title_ar = $("#modal_report_title_ar").val();
        if (pdf_file == '' || doctor_report_title_en == '' || doctor_report_title_ar == "") {
            $(".global-loader").hide();
            //$(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
            $(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
                .html("<strong>Please fill all the * marked fields</strong>").show();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'doctor-appointment/add-doctor-report',
                data: {
                    'pdf_file': pdf_file,
                    'doctor_appointment_id': doctor_appointment_id,
                    'report_title_en': doctor_report_title_en,
                    'report_title_ar': doctor_report_title_ar
                },
                success: function (response) {
                    $(".global-loader").hide();
                    var result = JSON.parse(response);

                    if (result.status == 200) {
                        $("#w1").hide();
                        $(".msg").removeClass("alert alert-danger").addClass("alert alert-success")
                            .html("<strong>" + result.msg + "</strong>").show();
                        $("#doctor-uploaded_report").val('');
                        $("#modal_doctor_appointment_id").val('');
                        setTimeout(function () {
                            $("#UploadReport").modal('hide');
                            location.reload();
                        }, 2000);

                    } else {
                        $("#w1").show();
                        $(".msg").removeClass("alert alert-success").addClass("alert alert-danger")
                            .html("<strong>" + result.msg + "</strong>").show();
                    }
                }
            });
        }
    },
    getArea: function (state, id) {
        $(".global-loader").show();
        $.ajax({
            type: "GET",
            url: baseUrl + 'area/get-area',
            data: {
                'state': state,
            },
            success: function (response) {
                $(".global-loader").hide();
                // $("#" + id + " option").prop("selected", false)
                $("#" + id).html(response).trigger("change");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },

    showHideSupportLogin() {

        if ($("#parmacies-enable_login").is(":checked")) {
            $("#login-section").show();
            jQuery('#w0').yiiActiveForm("add", {
                "id": 'parmacies-email',
                "name": "parmacies[email]",
                "container": ".field-pharmacies-email",
                "input": '#parmacies-email',
                "validate": function (attribute, value, messages, deferred, $form) {
                    if ($("#parmacies-enable_login").is(":checked") == true) {
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
                "id": 'pharmacies-password',
                "name": "pharmacies[password]",
                "container": ".field-pharmacies-password",
                "input": '#pharmacies-password',
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
                        "compareAttribute": "pharmacies-password",
                        "skipOnEmpty": 1,
                        "message": "Password and Confirm password must match"
                    }, $form);
                }
            });
        } else {
            $("#login-section").hide();
            $("#pharmacies-email").val("");
            $("#pharmacies-password").val("");
            $("#pharmacies-confirm_password").val("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-email');
            $(".field-pharmacies-email").removeClass("has-error");
            $(".field-pharmacies-email").addClass("has-success");
            $(".field-pharmacies-email .help-block").html("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-password');
            $(".field-pharmacies-password").removeClass("has-error");
            $(".field-pharmacies-password").addClass("has-success");
            $(".field-pharmacies-password .help-block").html("");
            //
            $('#w0').yiiActiveForm('remove', 'pharmacies-confirm_password');
            $(".field-pharmacies-confirm_password").removeClass("has-error");
            $(".field-pharmacies-confirm_password").addClass("has-success");
            $(".field-pharmacies-confirm_password .help-block").html("");
            //
        }
    },
    sendTargetedPush: function (url) {
        var pid = $("#pushItem").val();
        var msg = $("#pushMsg").val();
        var title = $("#pushTitle").val();
        //var r = confirm("Are you sure you want to send push for that product?");
        if ($.trim(pid) != "" && $.trim(msg) != "") {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + url,
                data: {
                    'id': pid,
                    'msg': msg,
                    'title': title,
                },
                success: function (response) {
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);
                    if (obj.success == '1') {
                        $("#pushItem").val("");
                        $("#pushMsg, #pushTitle").val("");
                        $("#pushResult").html("<div class=\"alert alert-success\">" + obj.msg + "</div>");

                        setTimeout(function () {

                            window.location.reload();
                            $("#pushResult").html('');
                        }, 3000);
                    }
                    //alert(obj.msg);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        } else {
            $("#pushResult").html('<div class="alert alert-danger">Message cannot be blank.</div>');

            setTimeout(function () {
                $("#pushResult").html('');
            }, 3000);
        }
    },
    openPushPopup: function (id, msg) {
        $("#pushItem").val("");
        $("#pushMsg").val("");
        $("#pushModal").modal('show');
        $("#pushTitle").val(msg);
        $("#pushItem").val(id);

    },

    changeTranslatorInAppointment: function (translator_id, appointement_id) {
        if ($.trim(appointement_id) != "" && $.trim(translator_id) != "") {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + 'doctor-appointment/add-translator-to-appointment',
                data: {
                    'appointement_id': appointement_id,
                    'translator_id': translator_id,
                },
                success: function (response) {
                    // console.log(response);
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);
                    alert(obj.msg);
                    if (obj.status == 200) {
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    getClinicDays: function (clinic_id, id) {
        if (id != "") {
            $(".global-loader").show();
            $('.chkbox').attr('disabled', true);
            $.ajax({
                type: "GET",
                url: baseUrl + 'doctor/get-clinic-work-days',
                data: {
                    'clinic_id': clinic_id,
                    'id': id,
                },
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    $("#" + id).html("");
                    $("#" + id).select2("val", "");
                    $('.clinicmsg').text('Clinic Off');
                    if (data == '') {
                        $('.trDays input').attr('disabled', true);
                    }
                    $(".global-loader").hide();

                    $("#" + id).html(data.insurance);
                    var splitString = data.days.split(',');
                    for (var i = 0; i < splitString.length; i++) {
                        var stringPart = splitString[i];
                        //alert(stringPart);
                        //if (stringPart != 'apple') continue;
                        if ($(".checkboxday0").val() == stringPart) {
                            $(".tr0 input").attr('disabled', false);

                            $('.msg0').text('');
                        }

                        if ($(".checkboxday1").val() == stringPart) {
                            $(".tr1 input").attr('disabled', false);

                            $('.msg1').text('');
                        }

                        if ($(".checkboxday2").val() == stringPart) {
                            $(".tr2 input").attr('disabled', false);

                            $('.msg2').text('');
                        }
                        if ($(".checkboxday3").val() == stringPart) {
                            $(".tr3 input").attr('disabled', false);

                            $('.msg3').text('');
                        }
                        if ($(".checkboxday4").val() == stringPart) {
                            $(".tr4 input").attr('disabled', false);

                            $('.msg4').text('');
                        }
                        if ($(".checkboxday5").val() == stringPart) {
                            $(".tr5 input").attr('disabled', false);

                            $('.msg5').text('');
                        }

                        if ($(".checkboxday6").val() == stringPart) {
                            $(".tr6 input").attr('disabled', false);

                            $('.msg6').text('');
                        }
                    }
                    /*$(".checkboxday").each(function(i=0)
                    {
                        //alert($(this).val() +'-'+ splitString[i]);
                        if($(this).val() == splitString[i])
                        {
                            alert();
                        }
                        i++;
                    });*/


                    //$('#example').DataTable().ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        } else {
            alert("Select clinic");
        }
    }
}
