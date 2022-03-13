/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var location = {
    addLocationForm: function ()
    {
        var l = $(".location-listing").length;
        $.ajax({
            type: "GET",
            url: baseUrl + 'pharmacy-locations/add-more-location',
            data: {
                'num': l
            },
            success: function (res) {
                $("#ajax_location").append(res);
            }
        })
    },
    removeLocation: function (id) {
        $("#lid" + id).remove();
    },
}

