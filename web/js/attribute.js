var attribute = {
    togglePalette: function() {
        var showPalette = $("#attributes-has_color_palette").is(':checked');

        if(showPalette) {
            $('.color-palette').removeClass('hidden');
        }
        else {
            $('.color-palette').addClass('hidden');
        }
    },
    addMoreAttributeValue: function ()
    {
        $(".global-loader").show();
        var length = $(".values").length;
        $.ajax({
            type: "GET",
            url: baseUrl + 'attribute/add-value',
            data: {
                'count': length
            },
            success: function (response)
            {
                $(".global-loader").hide();
                $("#ajaxValue").append(response);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        })
    },
    deleteAttributeValueFormAjax: function (count, attribute_value_id)
    {
        $(".global-loader").show();
        if ($.trim(attribute_value_id) != "" && attribute_value_id !== undefined)
        {
            $.ajax({
                type: "GET",
                url: baseUrl + 'attribute/remove-value',
                data: {
                    'id': attribute_value_id
                },
                dataType: 'json',
                success: function (response)
                {
                    $(".global-loader").hide();
                    if(response.status == 200) {
                        $("#ajxVal" + count).remove();
                    } else{
                        swal('',response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $(".global-loader").hide();
                    alert(jqXHR.responseText);
                }
            })
        }
        else {
            $(".global-loader").hide();
            $("#ajxVal" + count).remove();
        }
    }
}


