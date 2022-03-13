
$(document).ready(function () {
    initAutocomplete();
    //google.maps.event.addDomListener(window, 'load', initMap);
});

function initAutocomplete() {
    var locationField = document.getElementById('google-latlon');
    let loc = locationField.value;
    let lat_lng = loc.split(',');
    let center = new google.maps.LatLng(29.373051178792142, 47.978375894425426);
    if (loc) {
        center = new google.maps.LatLng(lat_lng[0], lat_lng[1]);
    }
    mapOptions = {
        center: center,
        zoom: 16,
        mapTypeControl: true,
        mapTypeId: "roadmap",
        animation: google.maps.Animation.DROP,
    };
    const map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    var marker = new google.maps.Marker({
        position: center,
        map: map,
        // label: "5409 Madison St"
    });
    const input = document.getElementById("select_location");
    const searchBox = new google.maps.places.SearchBox(input);
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });
    map.addListener('center_changed', function () {
        locationField.value = map.getCenter().lat() + ',' + map.getCenter().lng();
    });
    let markers = [];


    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();
        if (places.length == 0) {
            return;
        }
        // Clear out the old markers.
        markers.forEach((marker) => {
            marker.setMap(null);
        });
        markers = [];
        // For each place, get the icon, name and location.
        const bounds = new google.maps.LatLngBounds();
        places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }
            // const icon = {
            //     url: place.icon,
            //     size: new google.maps.Size(71, 71),
            //     origin: new google.maps.Point(0, 0),
            //     anchor: new google.maps.Point(17, 34),
            //     scaledSize: new google.maps.Size(25, 25),
            // };
            // Create a marker for each place.
            markers.push(
                new google.maps.Marker({
                    map,
                    // icon,
                    draggable: false,
                    animation: google.maps.Animation.DROP,
                    title: place.name,
                    position: place.geometry.location,
                })
            );
            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
}