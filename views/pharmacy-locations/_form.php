<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $model app\models\PharmacyLocations */
/* @var $form yii\widgets\ActiveForm */


\app\assets\SelectAsset::register($this);

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['googleMapKey'] . '&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);
//$this->registerJsFile(BaseUrl::home() . 'js/location.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$num = 1;
$areas = [];
if(!$model->isNewRecord)
{
    $stateId = $model->governorate->state_id;
    $model->governorate_id = $stateId;
    $areas = AppHelper::getAreaByState($stateId);
}
?>

<div class="pharmacy-locations-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        

        <div class="col-md-6">
            <?= $form->field($model, 'pharmacy_id')->dropDownList(AppHelper::getPharmacyList(), ['prompt' => 'Please Select','class' => 'form-control select2']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_ar')->textInput(['maxlength' => true,'dir'=>'rtl']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?php 
                $arr_gov = AppHelper::getStateList();
                $arr = [];
            ?>
            <?= $form->field($model, 'governorate_id')->dropDownList(AppHelper::getStateList(),[
                'prompt' => 'Please select',
                'class' => 'form-control select2',
                'onchange' => "common.getArea(this.value, 'pharmacylocations-area_id')"
            ]) ?>
            
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'area_id')->dropDownList($areas, ['prompt' => 'Please Select','class' => 'form-control select2']) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'block')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'building')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'street')->textInput(['maxlength' => true]) ?>
        </div>
        
        <div class="col-md-12">
            <div class="form-group field-select_location">
                <label class="control-label" for="select_lication">Select Location</label>
                <input type="text" id="select_location" class="form-control" name="select_location">
            </div>
            <!--Google Maps-->
            <div id="map-canvas" style="height: 300px; position: relative; overflow: hidden;"></div>

        </div>

        <div class="col-md-12">
            <?php echo $form->field($model, 'latlon')->textInput(['maxlength' => true, 'readonly'=> true,'id'=>'google-latlon','class'=>'google-latlon form-control'])->label('Geo Coordinates') ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
    ?>
<?php


$this->registerJsFile('@web/js/google_maps.js', ['depends' => 'yii\web\JqueryAsset']);

if ($model->isNewRecord) {
    $js1 = "$(document).ready(function(){
        location.addLocationForm(); 
    })";
    $this->registerJs($js1, yii\web\View::POS_END);
}
$userLocation = '';

$js1 = "
    var markerCenterIcon = '" . \yii\helpers\Url::to(['images/map-pin.png'], true) . "';
   var map$num = new google.maps.Map(document.getElementById('map$num'), {
        zoom: 11,
        center: new google.maps.LatLng(" . $userLocation . "),
        mapTypeId: 'roadmap',
        mapTypeControlOptions: {
          mapTypeIds: ['roadmap','satellite','hybrid','terrain'],
        },
        mapTypeControl: true,
        streetViewControl: false,
        //panControl: false,
        zoomControlOptions: {
            position: google.maps.ControlPosition.LEFT_BOTTOM
        },
    });
    
    var icon$num = {
        url: markerCenterIcon, // url
        scaledSize: new google.maps.Size(50, 50), // scaled size
    };
    
    var markerCenter$num = new google.maps.Marker({
        position: new google.maps.LatLng(map$num.center.lat(),map$num.center.lng()),
        map: map$num,
        icon: icon$num,
    })
    
     //fill address to 
    var i = 0;
    google.maps.event.addListener(markerCenter$num, 'click', (function(markerCenter$num, i) {
        return function() {
             app.getAddressFromLocation( markerCenter$num.position.lat()+','+markerCenter$num.position.lng(), 1, $num);
        }
    })(markerCenter$num, i));

    function changeMarkerPosition$num(marker, lat, lng) { 
        var latlng = new google.maps.LatLng(lat,lng);
        marker.setPosition(latlng);
    }

    //changing marker position to center of the map
    map$num.addListener('center_changed', function(e) {   
        changeMarkerPosition$num(markerCenter$num, map$num.center.lat(),map$num.center.lng() );
    });

    //changing marker position at dragend
    map$num.addListener('dragend', function(e) {
         app.getAddressFromLocation( map$num.center.lat()+','+map$num.center.lng(), 1, $num);
    });

    //fill address to search box
    map$num.addListener('idle', function(e) {   
         app.getAddressFromLocation( map$num.center.lat()+','+map$num.center.lng(), 1, $num);
    });

    var searchAddress = document.getElementById('searchAddress$num');
    google.maps.event.addDomListener(searchAddress, 'keydown', function(event) { 
        if (event.keyCode === 13) { 
            event.preventDefault(); 
        }
    });

    var searchAutocomplete$num = new google.maps.places.Autocomplete(searchAddress);
    // Set initial restrict to the greater list of countries.
    searchAutocomplete$num.setComponentRestrictions({'country': ['kw']});
    searchAutocomplete$num.addListener('place_changed', function() {
        var place = searchAutocomplete$num.getPlace();
        if(place && place.geometry){
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            if(latitude && longitude ){
                changeMarkerPosition$num(markerCenter$num, latitude, longitude );
                map$num.setCenter(markerCenter$num.getPosition());
            }
        }
    }); 
";

     $this->registerJs($js1, yii\web\View::POS_END);

    $this->registerJs("$('#up-location-img-lc$num').fileupload({
        dropZone: $('#locDropzone$num')
    });", \yii\web\View::POS_END);
?>

