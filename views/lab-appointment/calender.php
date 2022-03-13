<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;

\app\assets\SelectAsset::register($this);
\app\assets\FullCalendar::register($this);

$this->title = "Lab Appointment Calendar";
$this->params['breadcrumbs'][] = $this->title;
$allowCreate = true;
$allowIndex = true;
$get = Yii::$app->request->queryParams;
$lab_id = null;
if (isset($get['lab_id'])) {
    $lab_id = $get['lab_id'];
}

$this->registerJsFile(BaseUrl::home() . 'js/appointment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]);      ?>

                <p class="pull pull-left">
                    <?= ($allowIndex) ? Html::a('Lab Appointment', ['index'], ['class' => 'btn btn-info']) : "" ?>
                </p>

                <span class="clearfix"></span>

                <div>
                    <form id="form-calender" action="" method="GET">

                        <div class="form-group">
                            <label for="doctors">Labs</label>
                            <?php
                            echo Html::dropDownList('lab_id', $lab_id, \app\helpers\AppHelper::getLabsList(), [
                                'class' => 'form-control select2',
                                'prompt' => 'Please Select',
                                'id' => 'calender_lab_id',
                                'onchange' => 'appointment.submitLabCalendarForm(this.value)'
                            ])
                            ?>
                        </div>

                        <button id="submit-btn" style="display: none;" type="submit" class="btn btn-default">Search</button>

                    </form>
                </div>

                <span class="clearfix">&nbsp;</span>

                <div id="calendar">

                </div>
            </div>
        </div>

    </div>

</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
$this->registerJs("$(document).ready(function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      loading: function (bool) {
      },
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,timeGridDay,listWeek'
      },
      initialView:'dayGridWeek',
      navLinks: true, // can click day/week names to navigate views
      editable: true,
      dayMaxEventRows: true,
      events: {
        url: baseUrl + 'lab-appointment/calender-fetch-event',
        extraParams: {
            lab_id: $(\"#calender_lab_id\").val(),
        },
        failure: function() {
            alert('there was an error while fetching events!');
        },
      },
      dateClick: function (date, allDay, jsEvent, view) {
            calendar.changeView('timeGridDay');
            //console.log(date.dateStr);
            calendar.changeView('timeGridDay', date.dateStr);
      },
   });
   calendar.render();
});
jQuery('#calendar').on( 'click', '.fc-event', function(e){
    e.preventDefault();
    var href = jQuery(this).attr('href');
    //console.log(href);
    if(href){
        window.open(href, '_blank' );
    }
});
");
?>
