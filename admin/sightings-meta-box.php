<?php
/**
 * Load default settings for map and marker
 */
$default_settings = get_option(SIGHTINGS_HANDLE);
$sighting = array();
$manager = new Sightings_Manager();

/**
 * Load marker position and map zoom level, if already set
 */
if(isset($_GET['post'])) {
    $sighting = get_post_meta($_GET['post'],SIGHTINGS_HANDLE,true);
}
/**
 * Default display map on new posts?
 * If 'markers' is set, post is not new
 */
if(empty($sighting['markers']) && $default_settings['display'] != 0) {
    $sighting['display'] = 1;
}

// Meta box template
?>
<div class="sightings wrapper">

    <div class="latlng_container status">
        <h4><?php _e('Marker',SIGHTINGS_HANDLE) ?></h4>
        <?php _e('Latitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lat"></span>
        <br />
        <?php _e('Longitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lng"></span>
        <br />
        <?php _e('Zoom:',SIGHTINGS_HANDLE); ?> <span id="map_zoom"><?php echo isset($sighting['zoom']) ? $sighting['zoom'] : $default_settings['zoom'] ?></span>
    </div>

    <div class="post_options_container status">
        <h4><?php _e('Options',SIGHTINGS_HANDLE) ?></h4>
        <label for="sightings_display_toggle"><?php _e('Display map on post',SIGHTINGS_HANDLE) ?>:</label>
        <input id="sightings_display_toggle" type="checkbox" <?php echo isset($sighting['display']) ? 'checked="checked"' : '' ?>/>
        <br/>
        <label for="sightings_top_map_toggle"><?php _e('Display map at top of post',SIGHTINGS_HANDLE) ?>:</label>
        <input id="sightings_top_map_toggle" type="checkbox" <?php echo isset($sighting['top_map']) ? 'checked="checked"' : '' ?>/>
    </div>
    <p>
        <a id="add_marker" href="#" onclick="return false;">[+] <?php _e('Add marker',SIGHTINGS_HANDLE) ?></a>
    </p>
    <div class="use">
        <a href="#" class="button" onclick="confirmMarkerLatLng(); return false;"><?php _e('Use',SIGHTINGS_HANDLE); ?></a>
    </div>
    <div id="sightings-status">
        <div class="message" style="display: none;"></div>
    </div>
    <div id="sightings_advanced">
            <input id="sightings_map_search" type="text" size="24" placeholder="<?php _e('Search address or place...',SIGHTINGS_HANDLE) ?>"/>
            <input id="sightings_map_search_submit" type="submit" value="Sök" />
    </div>
    <div id="map_canvas" style="width:100%; height:400px;"></div>
</div>

<script type="text/javascript">

    var markersArray = [];
    var loadedMarkers = [];
    <?php
    if(isset($sighting['markers'])) {
        $i = 0;
        foreach($sighting['markers'] as $marker_latlng){
            echo 'loadedMarkers['.$i.'] = ['.$marker_latlng[0].','.$marker_latlng[1].'];';
            $i++;
        }
    }
    ?>

    // Load the map
    jQuery(window).load(function(){
        // Variable declarations
        var latlng;

        // Calculate map center based on markers lat lng
        if(loadedMarkers.length > 0) {
            var lat = 0;
            var lng = 0;
            for(var i in loadedMarkers)
            {
                lat += loadedMarkers[i][0];
                lng += loadedMarkers[i][1];
            }
            lat = (lat / loadedMarkers.length);
            lng = (lng / loadedMarkers.length);
            latlng = new google.maps.LatLng(lat, lng);
        }
        else {
            latlng = new google.maps.LatLng(<?php echo $default_settings['lat'] ?>, <?php echo $default_settings['lng'] ?>);
        }
        var myOptions = {
            zoom: <?php echo isset($sighting['zoom']) ? $sighting['zoom'] : $default_settings['zoom'] ?>,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById('map_canvas'),
                myOptions);
        var geocoder = new google.maps.Geocoder();
        var infoWindow = new google.maps.InfoWindow();

        /**
         * Add listeners to map or marker for drag and dragstart events
         * @param mapOrMarker google.maps.Map | google.maps.Marker
         * @param elementName String
         */
        var addListeners = function(mapOrMarker, elementName) {
            // Reposition marker on mouse drag
            google.maps.event.addListener(mapOrMarker, 'drag', function() {
                jQuery('#'+elementName+'_lat').html(Math.round(mapOrMarker.getPosition().lat()*10000)/10000);
                jQuery('#'+elementName+'_lng').html(Math.round(mapOrMarker.getPosition().lng()*10000)/10000);
            });
            if(elementName == 'marker') {
                // Clear marker div color on mouse drag start
                google.maps.event.addListener(mapOrMarker, 'dragstart', function() {
                    animateBackground('#f8f8f8','fast',false);
                });
            }
        };

        /**
         * Adds a new marker on map
         * @param map google.maps.Map
         * @param latlng google.maps.LatLng
         */
        var addMarker = function(map, latlng) {
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                position: latlng
            });

            markersArray.push(marker);

            google.maps.event.addListener(marker, 'click', function(){
                var $html = jQuery('<p></p>');
                var $a = jQuery('<a href="#">[-] <?php _e('Remove',SIGHTINGS_HANDLE) ?></a>').click(function(){

                    marker.setMap(null);
                    for(var i in markersArray) {
                        if(markersArray[i] == marker) {
                            markersArray.splice(i, 1);
                            break;
                        }
                    }
                    return false;
                });
                $html.append($a);
                infoWindow.setContent($html.get(0));
                infoWindow.open(map, this);
            });

            return marker;
        };

        var codeAddress = function(address) {
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.panTo(results[0].geometry.location);
                    map.setZoom(14);
                    postStatusMessage('<?php _e('Zooming to',SIGHTINGS_HANDLE) ?> <em>"'+address+'"</em>',2000);
                }
                else {
                    animateBackground('red','fast', false);
                    postStatusMessage('<em>"'+address+'"</em> hittades inte! ('+status+')',4000);
                }
            });
        };

        // Add any pre-recorded markers
        if(loadedMarkers.length > 0) {
            for(i in loadedMarkers) {
                latlng = new google.maps.LatLng(loadedMarkers[i][0],loadedMarkers[i][1]);
                var marker = addMarker(map,latlng);
                addListeners(marker, 'marker');
            }
        }

        // Record map zoom level on zoom
        google.maps.event.addListener(map, 'zoom_changed', function() {
            jQuery('#map_zoom').html(map.getZoom());
        });

        jQuery('#add_marker').click(function(){

            var marker = addMarker(map, map.getCenter());
            addListeners(marker, 'marker');

            return false;
        });

        // Sightings map search
        jQuery('#sightings_map_search').keydown(function(e) {
        if(e.keyCode == '13') {
            e.preventDefault();
            jQuery('#sightings_map_search_submit').click();
            }
        });
        jQuery('#sightings_map_search_submit').click(function(){
            codeAddress(jQuery('#sightings_map_search').val());
            return false;
        });
    });

    /**
     * Check if ready to save
     */
    function confirmMarkerLatLng() {
        if(!markersArray.length > 0) {
            doAjax('delete');
        }
        else {
            doAjax('update');
        }
    }

    /**
     * Animate background color for marker info box
     * Useful for emphasizing message status
     * @param color
     * @param speed
     * @param pause
     */
    function animateBackground( color , speed , pause ) {
        jQuery('.status').animate({backgroundColor:color}, speed, 'linear', function() {
            if(!pause) {
                jQuery(this).animate({
                    backgroundColor: '#f8f8f8'
                }, speed, 'linear', function() {
                    jQuery(this).css('background', 'none');
                })
            }
        })
    }

    /**
     * Displays a text message in the Map meta box for a duration in milliseconds
     * @param message
     * @param duration
     */
    function postStatusMessage( message , duration ) {
        jQuery('#sightings-status .message').html(message).fadeIn('fast').delay(duration).fadeOut('slow');
    }

    /**
     * Save marker latitude, longitude and map zoom level to database
     */
    function doAjax( choice ) {
        if(choice == 'delete') {
            jQuery.ajax({
                url : '<?php echo plugin_dir_url(SIGHTINGS_PLUGIN_ADMIN_DIR) ?>/admin/sightings-ajax.php',
                data : {
                    delete_sight : 1,
                    post_id:    '<?php echo the_ID(); ?>'
                    },
                type : 'POST',
                beforeSend : function(html) {
                    jQuery('#sightings-status').hide().append('<img id="sightings_loader" src="<?php echo SIGHTINGS_PLUGIN_DIR_URL ?>images/loading.gif" alt="loading" />').fadeIn();
                },
                success : function(html) {
                    animateBackground('#f9f9f9','slow',true);
                    postStatusMessage(html,2000);
                    jQuery('#sightings-status').find('img:last').fadeOut(function(){jQuery(this).remove()});
                }
            });
        }
        else {
            var markers = [];
            for(i in markersArray) {
                markers.push([markersArray[i].position.Pa,markersArray[i].position.Qa]);
            }
            jQuery.ajax({
                url : '<?php echo plugin_dir_url(SIGHTINGS_PLUGIN_ADMIN_DIR) ?>/admin/sightings-ajax.php',
                data : {
                    markers:    markers,
                    zoom:       jQuery('#map_zoom').html(),
                    post_id:    '<?php echo the_ID(); ?>',
                    display:    jQuery('#sightings_display_toggle').is(':checked'),
                    top_map:   jQuery('#sightings_top_map_toggle').is(':checked')
                },
                type : 'POST',
                beforeSend : function(html) {
                    jQuery('#sightings-status').hide().append('<img id="sightings_loader" src="<?php echo SIGHTINGS_PLUGIN_DIR_URL ?>/images/loading.gif" alt="loading" />').fadeIn();
                },
                success : function(html) {
                    animateBackground('#99ff99','slow',true);
                    postStatusMessage(html,2000);
                    jQuery('#sightings-status').find('img:last').fadeOut();
                }
            });
        }

    }
</script>