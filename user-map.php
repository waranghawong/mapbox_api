<?php
include_once 'header.php';
include 'classes/db.php';
include 'classes/locations.classes.php';
include 'classes/locationscntrl.classes.php';
$saved_locations = new locationsCntrl();
?>
    <style>

        input[type=text], select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=submit] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }

        .container {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
            margin-left: 20%;
            width:50%
        }
        #map { position:relative;left: 350px; top:350px; bottom:0px;height:550px ;width:1000px; }
        .geocoder {
            position:relative;left: 350px; top:290px;
        }
    </style>

    <h3></h3>

    <div class="container">
        <form method="POST" action="includes/locations.inc.php" id="signupForm">
            <label for="lat">lat</label>
            <input type="text" id="lat" name="lat" placeholder="Your lat..">

            <label for="lng">lng</label>
            <input type="text" id="lng" name="lng" placeholder="Your lng..">

            <select id="region"></select>
            <input type="hidden" name="region_text" id="region-text">

            <select id="province"></select>
            <input type="hidden" name="province_text" id="province-text">

            <select id="city"></select>
            <input type="hidden" name="city_text" id="city-text">

            <select id="barangay"></select>
            <input type="hidden" name="barangay_text" id="barangay-text">

            <input type="submit" name="add_location" value="Submit" >
        </form>
    </div>

    <div class="geocoder">
        <div id="geocoder" ></div>
    </div>

    <div id="map"></div>



    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.48.0/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.48.0/mapbox-gl.css' rel='stylesheet' />
    <style>
    </style>

    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.3.0/mapbox-gl-geocoder.min.js'></script>
    <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.3.0/mapbox-gl-geocoder.css' type='text/css' />
    <script src="https://f001.backblazeb2.com/file/buonzz-assets/jquery.ph-locations-v1.0.0.js"></script>
    <script src="ph-address-selector.js"></script>

    <script>
        $('#my-city-dropdown').ph_locations({'location_type': 'cities'});       
        $('#my-city-dropdown').ph_locations( 'fetch_list', [{"province_code": "1339"}]);
        var saved_markers = <?= $saved_locations->get_locations() ?>;
        var user_location = [123.7390371,13.1411192];
        let dropdown = $('#region');
                // load provinces

 
        mapboxgl.accessToken = 'pk.eyJ1IjoiZmFraHJhd3kiLCJhIjoiY2pscWs4OTNrMmd5ZTNra21iZmRvdTFkOCJ9.15TZ2NtGk_AtUvLd27-8xA';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v9',
            center: user_location,
            zoom: 12
        });
        //  geocoder here
        var geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            // limit results to Australia
            //country: 'IN',
        });

        var marker ;

        // After the map style has loaded on the page, add a source layer and default
        // styling for a single point.
        map.on('load', function() {
            addMarker(user_location,'load');
            add_markers(saved_markers);

            // Listen for the `result` event from the MapboxGeocoder that is triggered when a user
            // makes a selection and add a symbol that matches the result.
            geocoder.on('result', function(ev) {
                alert("aaaaa");
                console.log(ev.result.center);

            });
        });
        map.on('click', function (e) {
            marker.remove();
            addMarker(e.lngLat,'click');
            //console.log(e.lngLat.lat);
            document.getElementById("lat").value = e.lngLat.lat;
            document.getElementById("lng").value = e.lngLat.lng;

        });

        function addMarker(ltlng,event) {

            if(event === 'click'){
                user_location = ltlng;
            }
            marker = new mapboxgl.Marker({draggable: true,color:"#d02922"})
                .setLngLat(user_location)
                .addTo(map)
                .on('dragend', onDragEnd);
        }
        function add_markers(coordinates) {

            var geojson = (saved_markers == coordinates ? saved_markers : '');

            console.log(geojson);
            // add markers to map
            geojson.forEach(function (marker) {
                console.log(marker);
                // make a marker for each feature and add to the map
                new mapboxgl.Marker()
                    .setLngLat(marker)
                    .addTo(map);
            });

        }

        function onDragEnd() {
            var lngLat = marker.getLngLat();
            document.getElementById("lat").value = lngLat.lat;
            document.getElementById("lng").value = lngLat.lng;
            console.log('lng: ' + lngLat.lng + '<br />lat: ' + lngLat.lat);
        }

        // $('#signupForm').submit(function(event){
        //     event.preventDefault();
        //     var lat = $('#lat').val();
        //     var lng = $('#lng').val();
        //     var url = 'includes/locations.inc.php';
        //     var form = $(this).serialize();
            
        //     $.ajax({
        //         url: url,
        //         method: 'POST',
        //         data: form,
        //         success: function(data){
        //             alert(data);
        //             location.reload();
        //         }
        //     });
        // });

        document.getElementById('geocoder').appendChild(geocoder.onAdd(map));

     
        dropdown.empty();
        dropdown.append('<option selected="true" disabled>Choose Region</option>');
        dropdown.prop('selectedIndex', 0);
        const url = 'ph-json/region.json';
        // Populate dropdown with list of regions
        $.getJSON(url, function (data) {
            $.each(data, function (key, entry) {
                dropdown.append($('<option></option>').attr('value', entry.region_code).text(entry.region_name));
            })
        });

        $('#region').on('change', my_handlers.fill_provinces);


    </script>



<?php
include_once 'footer.php';

?>