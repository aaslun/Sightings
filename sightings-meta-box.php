<div class="wrapper">
    <div class="latlng_container">
        <h4>Current view center</h4>
        <?php _e('Latitude:'); ?> <span id="map_lat"></span>
        <br />
        <?php _e('Longitude:'); ?> <span id="map_lng"></span>
    </div>
    <div class="latlng_container">
        <h4>Marker</h4>
        <?php _e('Latitude:'); ?> <span id="marker_lat"></span>
        <br />
        <?php _e('Longitude:'); ?> <span id="marker_lng"></span>
    </div>
    <div id="map_canvas" style="width:100%; height:400px;"></div>
</div>

<script type="text/javascript">
    jQuery(window).load(function(){
        var latlng = new google.maps.LatLng(35, 10);
        var parliament = new google.maps.LatLng(59.327383, 18.06747);
        var myOptions = {
            zoom: 2,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById('map_canvas'),
                myOptions);

        var marker = new google.maps.Marker({
            map:map,
            draggable:true,
            animation: google.maps.Animation.DROP,
            position:latlng
        });

        // Record map center on drag
        google.maps.event.addListener(map, 'drag', function() {
            jQuery('#map_lat').html(Math.round(map.getCenter().lat()*1000)/1000);
            jQuery('#map_lng').html(Math.round(map.getCenter().lng()*1000)/1000);
        });

        // Position marker on click
        google.maps.event.addListener(marker, 'drag', function() {
            jQuery('#marker_lat').html(Math.round(marker.getPosition().lat()*1000)/1000);
            jQuery('#marker_lng').html(Math.round(marker.getPosition().lng()*1000)/1000);
        });


    });

</script>