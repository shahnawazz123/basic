var product = {
    showProductDetailsForm: function ()
    {
        var type = $("#product-type").val();
        //var attSet = $("#product-attribute_set_id").val();
        //if ($.trim(type) != "" && $.trim(attSet) != ""));
        if ($.trim(type) != "")
        {
            $("#productDetailsForm").show();
            $("#firstScreen").hide();
            if (type == 'G')
            {
                $(".associated-product").show();
            } else {
                $(".associated-product").hide()
                $("#tab1").trigger("click");
            }
        } else {
            $("#productDetailsForm").hide();
            $("#firstScreen").show();
        }
    },
    showFirstScreen: function ()
    {
        $("#firstScreen").show();
        $("#productDetailsForm").hide();
    },
    showDetailsForm: function ()
    {
        $("#firstScreen").hide();
        $("#productDetailsForm").show();
    },
    getAttributeValues: function (attSet)
    {
        $(".global-loader").show();

        $.ajax({
            type: "GET",
            url: baseUrl + 'product/get-attribute',
            data: {
                'attset': attSet,
            },
            success: function (response)
            {
                $(".global-loader").hide();
                //$("#product-attribute_values").html(response);
                $("#attributes").html(response);
                $("select.select5").select2();
                var newSourceUrl = associatedProductUrl + "&att_set=" + attSet;
                //alert(newSourceUrl);
                var oTable = $('#associatedProduct').DataTable();
                oTable.ajax.url(newSourceUrl);
                oTable.draw();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })

        product.showProductDetailsForm();
    },
    removeCategoryProduct: function (cid)
    {
        var checkValues = $('input[class=checkboxlist]:checked').map(function ()
        {
            return $(this).val();
        }).get();

        if ($.trim(checkValues) != "")
        {
            var r = confirm('Are sure ? want to remove that product(s)');

            if (r == true)
            {
                $(".global-loader").show();
                $.ajax({
                    type: "GET",
                    url: baseUrl + 'product/remove-category-product',
                    data: {
                        'id': checkValues,
                        'cid': cid,
                    },
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        $('#example').DataTable().ajax.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        }
    },
    approveProduct: function ()
    {
        var keys = [];
        $('#approve-product tbody :checkbox:checked').each(function (i) {
            keys[i] = $(this).val();
        });
        var selected = keys.join();

        if ($.trim(selected) != "")
        {
            var r = confirm('Are sure ? want to approve that product');
            if (r == true)
            {
                $(".global-loader").show();

                $.ajax({
                    type: "GET",
                    url: baseUrl + 'product/approve-product',
                    data: {
                        'id': selected,
                    },
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        var obj = $.parseJSON(response);
                        if (obj.success == '1')
                        {
                            $.each(obj.data, function (i, v) {
                                $("#tr-" + v).remove();
                            })
                        }
                        alert(obj.msg);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        }
    },
    addMoreStock: function (pid)
    {
        var stock = $("#stkPop_" + pid).val();
        if ($.trim(pid) != "")
        {
            if ($.trim(stock) == "")
            {
                $("#error_" + pid).html("The field is required").css({
                    'color': "red"
                });
                setTimeout(function () {
                    $("#error_" + pid).html("&nbsp;");
                }, 3000)
            } 
            else if (isNaN(stock))
            {
                $("#error_" + pid).html("Stock must be a number.").css({
                    'color': "red"
                });
                setTimeout(function () {
                    $("#error_" + pid).html("&nbsp;");
                }, 3000)
            } 
//            else if(parseInt(stock) < 0){
//                $("#error_" + pid).html("Stock can't be negative.").css({
//                    'color': "red"
//                });
//                setTimeout(function () {
//                    $("#error_" + pid).html("&nbsp;");
//                }, 3000)
//            }
            else {
                $(".global-loader").show();
                $.ajax({
                    type: "GET",
                    url: baseUrl + 'product/add-product-stock',
                    data: {
                        'id': pid,
                        'stock': stock,
                    },
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        if (response == '1') {
                            $("#stkPop_" + pid).val("");
                            $("#error_" + pid).html("Stock updated successfully.").css({
                                'color': "green"
                            });
                            location.reload();
                        } else {
                            alert(response);
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
    },
    bulkDelete: function (url, msg)
    {
        var keys = [];
        $('.grid-view tbody :checkbox:checked').each(function (i) {
            keys[i] = $(this).val();
        });
        var selected = keys.join();
        if ($.trim(selected) != "")
        {
            var r = confirm("Are you sure you want to delete the selected " + msg + "? \n");
            if (r == true)
            {
                $(".global-loader").show();
                $.ajax({
                    type: "GET",
                    url: baseUrl + 'product/bulk-delete',
                    data: {
                        'id': selected,
                    },
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        var obj = $.parseJSON(response);
                        if (obj.success == '1')
                        {
                            location.reload();
                        }
                        alert(obj.msg);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        }
    },
    sendTargetedPush: function (url)
    {
        var pid = $("#pushItem").val();
        var msg = $("#pushMsg").val();
        var title = $("#pushTitle").val();
        //var r = confirm("Are you sure you want to send push for that product?");
        if ($.trim(pid) != "" && $.trim(msg) != "")
        {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + url,
                data: {
                    'id': pid,
                    'msg': msg,
                    'title': title,
                },
                success: function (response)
                {
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);
                    if (obj.success == '1') {
                        $("#pushItem").val("");
                        $("#pushMsg, #pushTitle").val("");
                        $("#pushResult").html("<div class=\"alert alert-success\">" + obj.msg + "</div>");

                        setTimeout(function () {
                            $("#pushResult").html('');
                        }, 3000);
                    }
                    //alert(obj.msg);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
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
    openPushPopup: function (id, msg)
    {
        $("#pushItem").val("");
        $("#pushMsg").val("");
        $("#pushModal").modal('show');
        $("#pushMsg").val(msg);
        $("#pushItem").val(id);
    },
    getProductStatusHistory: function (id)
    {

        $.ajax({
            type: "GET",
            url: baseUrl + 'product/get-history',
            data: {
                'id': id,
            },
            success: function (response)
            {
                $(".global-loader").hide();
                var obj = $.parseJSON(response);
                var htm = '';
                $.each(obj, function (i, v) {
                    htm += '<tr><td>' + v.status + '</td><td>' + v.date + '</td><td>' + v.comment + '</td><td>' + v.notify + '</td></tr>';
                })
                $("#statusHistoryResult").html(htm);
                $("#statusHistoryModal").modal('show');

                //alert(obj.msg);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })

    },
    addStatus: function () {
        $("#status-panel-body").find('.alert').remove();

        $.ajax({
            type: "POST",
            url: baseUrl + 'product/add-status',
            data: $("#product-status-form").serialize(),
            success: function (response)
            {
                $(".global-loader").hide();

                var result = JSON.parse(response);

                if (result.status == 200) {
                    $("#product-status-form")[0].reset()
                    $.pjax.reload({container: '#product-status-pjax'});

                    $("#status-panel-body").prepend("<div class='alert alert-warning' style='margin-bottom: 10px;'>Status succesfully updated.</div>");

                    setTimeout(function () {
                        $("#status-panel-body").find('.alert').fadeOut("slow", function () {
                        });
                        location.href = baseUrl + 'product/index';
                    }, 3000);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                //alert(jqXHR.responseText);
            }
        })
    },
    quickSimpleProductForm: function (product_id)
    {
        $("#quickSimpleForm").html("");
        $(".global-loader").show();
        var brand_id = $("#product-brand_id").val();
        var manufacturer_id = $("#product-manufacturer_id").val();
        var pharmacy_id = $("#product-pharmacy_id").val();
        var attSet = $("#product-attribute_set_id").val();
        $.ajax({
            type: "GET",
            url: baseUrl + 'product/quick-product-form',
            data: {
                'attset': attSet,
                'product_id': product_id,
                'brand_id': brand_id,
                'pharmacy_id': pharmacy_id,
                'manufacturer_id': manufacturer_id,
            },
            success: function (response)
            {
                $(".global-loader").hide();
                $("#quick-simple-product").modal('show');
                $("#quickSimpleForm").html(response);
                $("select.select6").select2();
                $("select.select7").select2();
                $("select.brand-select").select2("readonly", true);
                $("select.pharmacy-select").select2("readonly", true);
                $("select.manufacturer-select").select2("readonly", true);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    changeProductStatus: function (url, trigger, id)
    {
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
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
    changeFeatureStatus: function (url, trigger, id)
    {
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
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
    toggleInstallationServicePrice: function ()
    {
        if ($("#product-installation_service").is(':checked')) {
            $('.field-product-installation_service_price').addClass("required");
            jQuery('#w0').yiiActiveForm("add", {
                "id": 'product-installation_service_price',
                "name": 'Product[installation_service_price]',
                "container": '.field-product-installation_service_price',
                "input": '#product-installation_service_price',
                "validate": function (attribute, value, messages, deferred) {
                    yii.validation.required(value, messages, {"message": "Installation service charges can not be blank"});
                    yii.validation.number(value, messages, {"pattern": /^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/, "message": "Installation service charges must be a number.", "skipOnEmpty": 1})
                }
            });
            $("#isp").show();
        } else {
            $("#isp").hide();
            $('#w0').yiiActiveForm('remove', 'product-installation_service_price');
            $(".field-product-installation_service_price").removeClass("required");
            $('.field-product-installation_service_price').removeClass('has-error');
            $('.field-product-installation_service_price').addClass('has-success');
            $(".field-product-installation_service_price .help-block").html('');
        }
    },
    toggleDeliveryCharges: function ()
    {
        if ($("#product-free_delivery").is(':checked')) {
            $("#dc").hide();
        } else {
            $("#dc").show();
        }
    },
    getModelByMake: function (makeId, n)
    {
        //alert(n);
        $("select#part-model-i" + n).val(null).trigger("change");
        $("select#part-engine-i" + n).val(null).trigger("change");
        $("select#part-year-i" + n).val(null).trigger("change");
        $("select#part-engine-i" + n).html('<option value="">Please select</option>');
        $("select#part-year-i" + n).html('<option value="">Please select</option>');

        if (makeId != "")
        {
            $.ajax({
                type: "GET",
                url: baseUrl + 'engine/model-list',
                data: {
                    'mid': makeId
                },
                success: function (data)
                {
                    //alert(data);
                    $("select#part-model-i" + n).html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    getEngineByModel: function (modelId, n)
    {
        $("select#part-engine-i" + n).val(null).trigger("change");
        $("select#part-engine-i" + n).html('<option value="">Please select</option>');
        if (modelId != "")
        {
            $.ajax({
                type: "GET",
                url: baseUrl + 'engine/engine-list',
                data: {
                    'mid': modelId
                },
                success: function (data)
                {
                    //alert(data);
                    $("select#part-engine-i" + n).html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    getYearByModel: function (modelId, n)
    {
        $("select#part-year-i" + n).val(null).trigger("change");
        $("select#part-year-i" + n).html('<option value="">Please select</option>');
        if (modelId != "")
        {
            $.ajax({
                type: "GET",
                url: baseUrl + 'models/year-list',
                data: {
                    'mid': modelId
                },
                success: function (data)
                {
                    //alert(data);
                    $("select#part-year-i" + n).html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    getEngineByModelYear: function (yearId, n)
    {
        $("select#part-engine-i" + n).val(null).trigger("change");
        $("select#part-engine-i" + n).html('<option value="">Please select</option>');
        var modelId = $("#part-model-i" + n).val();
        if (modelId != "" && yearId != "")
        {
            $.ajax({
                type: "GET",
                url: baseUrl + 'engine/engine-list',
                data: {
                    'mid': modelId,
                    'yid': yearId,
                },
                success: function (data)
                {
                    //alert(data);
                    $("select#part-engine-i" + n).html(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    addMoreCarRow: function ()
    {
        var n = $("#part-cars .part-car-list").length;
        $.ajax({
            type: "GET",
            url: baseUrl + 'product/add-more-car',
            data: {
                'n': n
            },
            success: function (data)
            {
                //console.log(data);
                $("#part-cars").append(data);
                $("select.select5").select2({
                    placeholder: "Please Select",
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert(jqXHR.responseText);
            }
        })
    },
    removeMoreCarRow: function (id, engineid)
    {

        if ($.trim(engineid) != "")
        {
            var r = confirm('Are you sure you want to delete this item?');

            if (r == true)
            {
                $.ajax({
                    type: "GET",
                    url: baseUrl + 'product/remove-more-car',
                    data: {
                        'eid': engineid
                    },
                    success: function (data)
                    {
                        //console.log(data);
                        if (data == '1') {
                            $("#lst" + id).remove();
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert(jqXHR.responseText);
                    }
                })
            }
        } else {
            $("#lst" + id).remove();
        }
    },
    viewUploadedImg: function (id)
    {
        //alert(id);
        var src = $("#target" + id).attr("src");
        $.magnificPopup.open({
            items: {
                src: src,
                type: 'image',
                callbacks: {
                }
            }
        });
    },
    deleteUploadedImg: function (id, photo_bank_id)
    {
        if ($.trim(id) != "")
        {
            var src = $("#target" + id).attr('src');
            $.ajax({
                type: "GET",
                url: baseUrl + 'product/delete-image',
                data: {
                    'src': src,
                    'id': photo_bank_id
                },
                success: function (res)
                {
                    if (res == '1')
                    {
                        $("#preview-" + id).remove();
                    }
                    var l = $("#uploaded_img .uploadedImg").length;
                    if (l == 0)
                    {
                        $(".file-drop-zone-title").show();
                    }

                    if ($('#uploaded_img').is(':empty')) {
                        $('#uploaded_img').html('<div class="file-drop-zone-title">Drag &amp; drop files here …</div>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert(jqXHR.responseText);
                }
            })
        }
    },
    removePhotoGalleryImg: function (id)
    {
        if ($.trim(id) != "")
        {
            $("#preview-" + id).remove();
        }
    },
    loadPhotoBank: function (type)
    {
        $("#previewPane").html("");
        $("#tag_id").html("");
        $("#ajax-photobank").html("");

        $.ajax({
            type: "GET",
            url: baseUrl + 'photo-bank/photo-tags',
            data: {
                'type': type
            },
            success: function (res)
            {
                $('#myModal').modal('show');
                $("#ajax-photobank").html(res);
            }
        })

        product.loadTagListByType(type);

    },
    loadTagListByType: function (type) {
        $.ajax({
            type: "GET",
            url: baseUrl + 'photo-bank/get-tags',
            data: {
                'type': type
            },
            success: function (res)
            {
                $("#tag_id").html(res);
            }
        })
    },
    searchPhotoBank: function ()
    {
        $('#previewPan').hide();
        $('#previewPane').html("");
        var selectedValues = $('#tag_id').val();
        var str = '';
        $.each(selectedValues, function (i, v) {
            str += v + ',';
        })
        str = str.substr(0, str.length - 1);
        $.ajax({
            type: "GET",
            url: baseUrl + 'photo-bank/photo-tags',
            data: {
                'tagId': str
            },
            success: function (res)
            {
                $('#myModal').modal('show');
                $("#ajax-photobank").html(res);
            }
        })
    },
    selectImage: function (id)
    {
        $(".bg-label").css({
            "border": "2px solid #ddd",
            "background": "#F8F8F8",
        });

        $("#tag" + id).css({
            "border": "2px solid #367FA9",
            "background": "#F2F2F2",
        });
    },
    setImgPreview: function (img)
    {
        if ($.trim(img) != "")
        {
            var htm = '<img src="' + baseUrl + 'uploads/' + img + '" alt="img" class="img-responsive"/>';

            $('#previewPan').show();
            $('#previewPane').html(htm);
            $(".leftBorder").css({
                "border-left": "2px solid #ddd",
                "padding-left": "10px",
            });
        }
    },
    setPartImages: function ()
    {
        var chkArray = [];
        $(".chk:checked").each(function () {
            var selectedImg = $(this).val();
            var l = $("#uploaded_img .uploadedImg").length;
            var img = '<div class="file-preview-frame krajee-default  file-preview-initial file-sortable kv-preview-thumb" id="preview-' + l + '">\n\
<div class="kv-file-content">\n\
<img id="target' + l + '" class="magpop uploadedImg" src="' + baseUrl + 'uploads/' + $(this).val() + '" alt="img" style="height: 160px;">\n\
</div>\n\
<div class="file-thumbnail-footer">\n\
<span class="file-drag-handle drag-handle-init text-info" title="Move / Rearrange">\n\
<i class="glyphicon glyphicon-menu-hamburger"></i>\n\
</span>\n\
<div class="file-actions">\n\
<div class="file-footer-buttons">\n\
<button onclick="product.removePhotoGalleryImg(' + l + ')" type="button" class="kv-file-remove btn btn-xs btn-default" title="Remove file">\n\
<i class="glyphicon glyphicon-trash text-danger"></i>\n\
</button>\n\
<button onclick="product.viewUploadedImg(' + l + ')" type="button" class="kv-file-zoom btn btn-xs btn-default" title="View Details">\n\
<i class="glyphicon glyphicon-zoom-in"></i>\n\
</button>\n\
</div>\n\
</div>\n\
</div>\n\
<input type="hidden" name="Product[images][]" value="' + $(this).val() + '"/>\n\
</div>';
            var found = 0;
            $(".file-preview-frame input[type=hidden]").each(function (i, v) {
                //console.log($(this).val());
                var addedImg = $(this).val();
                if (selectedImg == addedImg)
                {
                    found = 1;
                }
            })
            if (found == 0) {
                $("#uploaded_img").append(img);
                $(".file-drop-zone-title").hide();
            }
        });
        $('#myModal').modal('hide');
    },
    showHideTreeNode: function (elm)
    {
        $(elm).nextAll('ul:first').toggle();
        $(elm).parent().nextAll('ul:first').toggle();
        $(elm).children('i').toggleClass('fa-plus-square-o fa-minus-square-o');
    },
    checkAllMakeModel: function (make_id)
    {
        var checked = $("#chk-make-" + make_id).is(":checked");
        if (checked)
        {
            $(".chk-model-" + make_id).each(function (i, v) {
                var modelIsChecked = $("#chk-model-" + v.value).is(":checked");
                if (modelIsChecked != true)
                {
                    $("#chk-model-" + v.value).trigger("click");
                }
            })
        } else {
            $(".chk-model-" + make_id).each(function (i, v) {
                var modelIsChecked = $("#chk-model-" + v.value).is(":checked");
                if (modelIsChecked)
                {
                    $("#chk-model-" + v.value).trigger("click");
                }
            })
        }
    },
    qfCheckAllMakeModel: function (make_id)
    {
        var checked = $("#qf-chk-make-" + make_id).is(":checked");
        if (checked)
        {
            $(".qf-chk-model-" + make_id).each(function (i, v) {
                var modelIsChecked = $("#qf-chk-model-" + v.value).is(":checked");
                if (modelIsChecked != true)
                {
                    $("#qf-chk-model-" + v.value).trigger("click");
                }
            })
        } else {
            $(".qf-chk-model-" + make_id).each(function (i, v) {
                var modelIsChecked = $("#qf-chk-model-" + v.value).is(":checked");
                if (modelIsChecked)
                {
                    $("#qf-chk-model-" + v.value).trigger("click");
                }
            })
        }
    },
    checkUncheckModelYear: function (id)
    {
        var checked = $("#chk-model-" + id).is(":checked");
        if (checked)
        {
            $(".model-year-chk-" + id).each(function (i, v) {
                var mychecked = $("#myr-" + v.value).is(":checked");
                if (mychecked != true)
                {
                    $("#myr-" + v.value).trigger("click");
                }
            })
        } else {
            $(".model-year-chk-" + id).each(function (i, v) {
                var mychecked = $("#myr-" + v.value).is(":checked");
                if (mychecked)
                {
                    $("#myr-" + v.value).trigger("click");
                }
            })
        }
        //taking make id
        var makeId = $("#chk-model-" + id).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#chk-make-" + makeId).prop('checked', true)
        } else {
            $("#chk-make-" + makeId).prop('checked', false)
        }
    },
    qfCheckUncheckModelYear: function (id)
    {
        var checked = $("#qf-chk-model-" + id).is(":checked");
        if (checked)
        {
            $(".qf-model-year-chk-" + id).each(function (i, v) {
                var mychecked = $("#qf-myr-" + v.value).is(":checked");
                if (mychecked != true)
                {
                    $("#qf-myr-" + v.value).trigger("click");
                }
            })
        } else {
            $(".qf-model-year-chk-" + id).each(function (i, v) {
                var mychecked = $("#qf-myr-" + v.value).is(":checked");
                if (mychecked)
                {
                    $("#qf-myr-" + v.value).trigger("click");
                }
            })
        }
        //taking make id
        var makeId = $("#qf-chk-model-" + id).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".qf-chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".qf-chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#qf-chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#qf-chk-make-" + makeId).prop('checked', true)
        } else {
            $("#qf-chk-make-" + makeId).prop('checked', false)
        }
    },
    checkUncheckEngine: function (id)
    {
        //alert($(elm).val());
        //$(elm).nextAll('ul:first').toggle();
        var checked = $("#myr-" + id).is(":checked");
        //alert(checked);
        if (checked)
        {
            $("#tree-engine-" + id + ' .chk input:checkbox').prop("checked", true);
        } else {
            $("#tree-engine-" + id + ' .chk input:checkbox').prop("checked", false);
        }

        //taking model id
        var modelId = $("#myr-" + id).data("id");
        //count all selected checbox
        var count = 0;
        $(".model-year-chk-" + modelId).each(function (i, v) {
            var mychecked = $("#myr-" + v.value).is(":checked");
            if (mychecked)
            {
                count = count + 1;
            }
        })
        //count all checkbox of model
        var countAllModelCheckbox = $(".model-year-chk-" + modelId).length;
        //if selected checkbox = checkbox count then make it selected
        if (countAllModelCheckbox == count)
        {
            $("#chk-model-" + modelId).prop('checked', true)
        } else {
            $("#chk-model-" + modelId).prop('checked', false)
        }
        //taking make id
        var makeId = $("#chk-model-" + modelId).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#chk-make-" + makeId).prop('checked', true)
        } else {
            $("#chk-make-" + makeId).prop('checked', false)
        }
    },
    qfCheckUncheckEngine: function (id)
    {
        var checked = $("#qf-myr-" + id).is(":checked");
        //alert(checked);
        if (checked)
        {
            $("#qf-tree-engine-" + id + ' .chk input:checkbox').prop("checked", true);
        } else {
            $("#qf-tree-engine-" + id + ' .chk input:checkbox').prop("checked", false);
        }

        //taking model id
        var modelId = $("#qf-myr-" + id).data("id");
        //count all selected checbox
        var count = 0;
        $(".qf-model-year-chk-" + modelId).each(function (i, v) {
            var mychecked = $("#qf-myr-" + v.value).is(":checked");
            if (mychecked)
            {
                count = count + 1;
            }
        })
        //count all checkbox of model
        var countAllModelCheckbox = $(".qf-model-year-chk-" + modelId).length;
        //if selected checkbox = checkbox count then make it selected
        if (countAllModelCheckbox == count)
        {
            $("#qf-chk-model-" + modelId).prop('checked', true)
        } else {
            $("#qf-chk-model-" + modelId).prop('checked', false)
        }
        //taking make id
        var makeId = $("#qf-chk-model-" + modelId).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".qf-chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".qf-chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#qf-chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#qf-chk-make-" + makeId).prop('checked', true)
        } else {
            $("#qf-chk-make-" + makeId).prop('checked', false)
        }
    },
    checkUncheckParentCheckbox: function (parent_id, id)
    {
        var allcheckboxLen = $('#tree-engine-' + parent_id + ' .chk input:checkbox').length;
        var checkedLen = $('#tree-engine-' + parent_id + ' .chk [name="Product[engines][]"]:checked').length;
        if (allcheckboxLen == checkedLen)
        {
            $("#myr-" + parent_id).prop('checked', true);
        } else {
            $("#myr-" + parent_id).prop('checked', false);
        }

        //taking model id
        var modelId = $("#myr-" + parent_id).data("id");
        //count all selected checbox
        var count = 0;
        $(".model-year-chk-" + modelId).each(function (i, v) {
            var mychecked = $("#myr-" + v.value).is(":checked");
            if (mychecked)
            {
                count = count + 1;
            }
        })
        //count all checkbox of model
        var countAllModelCheckbox = $(".model-year-chk-" + modelId).length;
        //if selected checkbox = checkbox count then make it selected
        if (countAllModelCheckbox == count)
        {
            $("#chk-model-" + modelId).prop('checked', true)
        } else {
            $("#chk-model-" + modelId).prop('checked', false)
        }
        //taking make id
        var makeId = $("#chk-model-" + modelId).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#chk-make-" + makeId).prop('checked', true)
        } else {
            $("#chk-make-" + makeId).prop('checked', false)
        }
    },
    quickFormCheckUncheckParentCheckbox: function (parent_id, id)
    {
        var allcheckboxLen = $('#qf-tree-engine-' + parent_id + ' .chk input:checkbox').length;
        var checkedLen = $('#qf-tree-engine-' + parent_id + ' .chk [name="Product[engines][]"]:checked').length;
        if (allcheckboxLen == checkedLen)
        {
            $("#qf-myr-" + parent_id).prop('checked', true);
        } else {
            $("#qf-myr-" + parent_id).prop('checked', false);
        }

        //taking model id
        var modelId = $("#qf-myr-" + parent_id).data("id");
        //count all selected checbox
        var count = 0;
        $(".qf-model-year-chk-" + modelId).each(function (i, v) {
            var mychecked = $("#qf-myr-" + v.value).is(":checked");
            if (mychecked)
            {
                count = count + 1;
            }
        })
        //count all checkbox of model
        var countAllModelCheckbox = $(".qf-model-year-chk-" + modelId).length;
        //if selected checkbox = checkbox count then make it selected
        if (countAllModelCheckbox == count)
        {
            $("#qf-chk-model-" + modelId).prop('checked', true)
        } else {
            $("#qf-chk-model-" + modelId).prop('checked', false)
        }
        //taking make id
        var makeId = $("#qf-chk-model-" + modelId).data("id");
        //count all checkbox of make
        var countAllMakeCheckbox = $(".qf-chk-model-" + makeId).length;
        //count all selected checbox
        var count1 = 0;
        $(".qf-chk-model-" + makeId).each(function (i, v) {
            var mychecked1 = $("#qf-chk-model-" + v.value).is(":checked");
            if (mychecked1)
            {
                count1 = count1 + 1;
            }
        })
        //if selected checkbox = checkbox count then make it selected
        if (countAllMakeCheckbox == count1)
        {
            $("#qf-chk-make-" + makeId).prop('checked', true)
        } else {
            $("#qf-chk-make-" + makeId).prop('checked', false)
        }
    },
    sendTargetedStock: function (url)
    {
        var pid = $("#stockItem").val();
        var quantity = $("#stockQuantity").val();
        var message = $("#stockMessage").val();
        var radio = $("input:radio[name=txtRadio]:checked").val();

        if (radio == "") {
            radio = "add";
        }

        if ($.trim(pid) != "" && $.trim(quantity) != "")
        {
            $(".global-loader").show();
            $.ajax({
                type: "GET",
                url: baseUrl + url,
                data: {
                    'id': pid,
                    'stock': quantity,
                    'radio': radio,
                    'message': message
                },
                success: function (response)
                {
                    $(".global-loader").hide();
                    var obj = $.parseJSON(response);

                    $("#stockItem").val("");
                    $("#stockQuantity").val("");
                    $("#stockMessage").val("");
                    $("input:radio[name=txtRadio]:checked").val("");

                    if (obj.success == '1') {
                        $("#stockResult").html("<div class=\"alert alert-success\">" + obj.msg + "</div>");
                    } else {
                        $("#stockResult").html("<div class=\"alert alert-danger\">" + obj.msg + "</div>");
                    }

                    setTimeout(function () {
                        $("#stockResult").html('');
                        window.location.reload();
                    }, 3000);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                    window.location.reload();
                }
            })
        } else {
            $("#stockResult").html('<div class="alert alert-danger">Stock cannot be blank.</div>');

            setTimeout(function () {
                $("#stockResult").html('');
            }, 3000);
        }
    },
    openStockPopup: function (id, qty)
    {
        $("#stockItem").val("");
        $("#stockQuantity").val("");
        $("#stockRadio").val("");
        $("#stockMessage").val("");
        $("#stockModal").modal('show');
        $("#stockQuantity").val('');
        //$("#stockRadio").val(radio);
        $("#stockItem").val(id);
        $("#remaining-quantity").text(qty);

    }
}

