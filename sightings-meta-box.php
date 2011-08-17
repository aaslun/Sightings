<?php
$default_settings = get_option(SIGHTINGS_HANDLE);
$geotag = get_post_meta(the_ID(),SIGHTINGS_HANDLE);

if(isset($_REQUEST['debug'])) {
    $debug = true;
}
?>

<div class="wrapper">
<?php
    if(isset($debug)) {
    ?>
    <div class="latlng_container">
        <h4>Current view center</h4>
        <?php _e('Latitude:'); ?> <span id="map_lat"></span>
        <br />
        <?php _e('Longitude:'); ?> <span id="map_lng"></span>
    </div>
    <?php } ?>
    <div class="latlng_container marker">
        <h4>Marker</h4>
        <a href="#" class="button" onclick="confirmMarkerLatLng(); return false;"><?php _e('Use'); ?></a>
        <?php _e('Latitude:'); ?> <span id="marker_lat"><?php echo $geotag['lat'] ? $geotag['lat'] : $default_settings['lat'] ?></span>
        <br />
        <?php _e('Longitude:'); ?> <span id="marker_lng"><?php echo $geotag['lng'] ? $geotag['lng'] : $default_settings['lng'] ?></span>
        <br />
        <?php _e('Zoom:'); ?> <span id="map_zoom"><?php echo $geotag['zoom'] ? $geotag['zoom'] : $default_settings['zoom'] ?></span>
    </div>
    <div id="sightings-status">
        <span class="message" style="display: none;"></span>
    </div>
    <div id="map_canvas" style="width:100%; height:400px;"></div>
</div>

<script type="text/javascript">
    // Load the map
    jQuery(window).load(function(){
        var latlng = new google.maps.LatLng(<?php echo $default_settings['lat'] ? $default_settings['lat'] : '35' ?>, <?php echo $default_settings['lng'] ? $default_settings['lng'] : '10' ?>);
        var myOptions = {
            zoom: <?php echo $default_settings['zoom'] ? $default_settings['zoom'] : '2' ?>,
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

        // Record map zoom level on zoom
        google.maps.event.addListener(map, 'zoom_changed', function() {
            jQuery('#map_zoom').html(map.getZoom());
        });

        // Position marker on click
        google.maps.event.addListener(marker, 'drag', function() {
            jQuery('#marker_lat').html(Math.round(marker.getPosition().lat()*1000)/1000);
            jQuery('#marker_lng').html(Math.round(marker.getPosition().lng()*1000)/1000);
        });

        // Clear marker div color on drag
        google.maps.event.addListener(marker, 'dragstart', function() {
            animateBackground('#ffffff','fast',false);
        });
    });

    function confirmMarkerLatLng() {
        if(jQuery('#marker_lat').html() == '') {
            animateBackground('#feef87','fast',false);
            postStatusMessage('<?php _e('Please reposition the marker first') ?>',1000);
        }
        else {
            doAjax();
        }
    }

    function animateBackground( color , speed , pause ) {
        jQuery('.marker').animate({backgroundColor:color}, speed, 'linear', function() {
            if(!pause) {
                jQuery(this).animate({
                    backgroundColor: '#ffffff'
                }, speed, 'linear', function() {
                    jQuery(this).css('background', 'none');
                })
            }
        })
    }

    function postStatusMessage( message , duration ) {
        jQuery('#sightings-status .message').html(message).fadeIn('fast').delay(duration).fadeOut('slow');
    }

    function doAjax() {
        jQuery.ajax({
            url : '<?php echo SIGHTINGS_PLUGIN_DIR ?>sightings-ajax.php',
            data : 'lat='+jQuery('#marker_lat').html()+'&lng='+jQuery('#marker_lng').html()+'&zoom='+jQuery('#map_zoom').html()+'&post_id=<?php echo the_ID(); ?>',
            type : 'POST',
            beforeSend : function(html) {
                jQuery('#sightings-status').hide().append('<img src="<?php echo SIGHTINGS_PLUGIN_DIR ?>images/loading.gif" alt="loading" />').fadeIn();
            },
            success : function(html) {
                animateBackground('#99ff99','slow',true);
                postStatusMessage(html,2000);
                jQuery('#sightings-status').find('img:last').fadeOut();
            }
        });
    }

</script>