/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var appointment = {
  submitDoctorCalendarForm: function (value) {
    if ($.trim(value) != "") {
      $("#submit-btn").trigger("click");
    }
  },
  submitLabCalendarForm: function (value) {
    if ($.trim(value) != "") {
      $("#submit-btn").trigger("click");
    }
  },



}