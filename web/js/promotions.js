var promotions = {
    getListByType: function (type)
    {
        $("#banner-link_id").val(null).trigger("change");
        var label = 'Link Name';
        if ($.trim(type) != '')
        {
            if (type == 'C')
            {
                label = 'Clinic list';
            }else if (type == 'L')
            {
                label = 'Labs list';
            }
            else if (type == 'F')
            {
                label = 'Pharmacy list';
            }
            
            else if (type == 'D')
            {
                label = 'Doctors';
            }
           /* else if (type == 'BS')
            {
                $('#url').hide();
                $('#linkContainer').hide();
                $("#banner-link_id").html("");
                return false;
            }*/
            else if (type == 'S')
            {
                label = 'Vendor';
            }
            $(".field-promotions-link_id label").html(label);
            if (type == 'Ls') {
                /*$('#url').show();
                $('#linkContainer').hide();
                $("#banner-link_id").html("");*/
                $('#url').hide();
                $('#linkContainer').show();
                $(".global-loader").show();
            }
            else {
                $('#url').hide();
                $('#linkContainer').show();
                $(".global-loader").show();
                $.ajax({
                    type: "GET",
                    url: baseUrl + 'banner/get-list',
                    data: {
                        'type': type,
                    },
                    success: function (response)
                    {
                        $(".global-loader").hide();
                        $("#promotions-link_id").html(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        $(".global-loader").hide();
                        alert(jqXHR.responseText);
                    }
                })
            }
        }
        else{
            $('#url').hide();
            $("#banner-link_id").html("");
        }
    },
    changeStatus:function(url,trigger,id)
    {
        var status=0;
        if($('#'+trigger.id).is(":checked")){
            status=1;
        }
        $.ajax({
            type: "GET",
            url: baseUrl+url,
            data:{
                "id":id
            },
            success: function(res){
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    },
}

