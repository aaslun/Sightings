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
 * If 'lat' is set, post is not new
 */
if(empty($sighting['lat']) && $default_settings['display'] != 0) {
    $sighting['display'] = 1;
}

/**
 * Check if debug mode is on, displays extra info about map view port
 */
if(isset($_REQUEST['debug'])) {
    $debug = true;
}

// Meta box template
?>
<div class="sightings wrapper">
<?php
    if(isset($debug)) {
    ?>
    <div class="latlng_container">
        <h4><?php _e('Current view center',SIGHTINGS_HANDLE) ?></h4>
        <?php _e('Latitude:',SIGHTINGS_HANDLE); ?> <span id="map_lat"></span>
        <br />
        <?php _e('Longitude:',SIGHTINGS_HANDLE); ?> <span id="map_lng"></span>
    </div>
    <?php } ?>
    <div class="latlng_container status">
        <h4><?php _e('Marker',SIGHTINGS_HANDLE) ?></h4>
        <?php _e('Latitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lat"><?php echo isset($sighting['lat']) ? $sighting['lat'] : $default_settings['lat'] ?></span>
        <br />
        <?php _e('Longitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lng"><?php echo isset($sighting['lng']) ? $sighting['lng'] : $default_settings['lng'] ?></span>
        <br />
        <?php _e('Zoom:',SIGHTINGS_HANDLE); ?> <span id="map_zoom"><?php echo isset($sighting['zoom']) ? $sighting['zoom'] : $default_settings['zoom'] ?></span>
    </div>

    <div class="post_options_container status">
        <h4><?php _e('Options',SIGHTINGS_HANDLE) ?></h4>
        <label for="sightings_display_toggle"><?php _e('Display map on post',SIGHTINGS_HANDLE) ?>:</label>
        <input id="sightings_display_toggle" type="checkbox" <?php echo isset($sighting['display']) ? 'checked="checked"' : '' ?>/>
    </div>
    <div class="use">
        <a href="#" class="button" onclick="confirmMarkerLatLng(); return false;"><?php _e('Use',SIGHTINGS_HANDLE); ?></a>
    </div>
    <div id="sightings-status">
        <div class="message" style="display: none;"></div>
    </div>
    <div id="sightings_advanced"><a href="#" onclick="jQuery('#sightings_advanced .advanced').toggle(); return false;"><?php _e('Advanced settings') ?></a>
    <?php // Optional Gravity Forms features
        if(isset($default_settings['gf_connect']) && $manager->isGravityFormsActive()) { ?>
        <div id="sightings-gravity-form" class="advanced">
            <div>
                <?php _e('Connect Gravity Form',SIGHTINGS_HANDLE) ?>:
                <select id="gf_forms">
                    <option><?php _e('Select form...') ?></option>
                    <?php
                    $form_obj_array = RGFormsModel::get_forms();
                    foreach($form_obj_array as $form_obj) {
                        echo '<option value="'.$form_obj->id.'" '.(isset($sighting['gf_id']) ? ($form_obj->id == $sighting['gf_id'] ? 'selected' : '') : '').'>'.$form_obj->title.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <?php } ?>
    <div id="map_canvas" style="width:100%; height:400px;"></div>
</div>

<script type="text/javascript">
    // Load the map
    jQuery(window).load(function(){
        var latlng = new google.maps.LatLng(<?php echo isset($sighting['lat']) ? $sighting['lat'] : $default_settings['lat'] ?>, <?php echo isset($sighting['lng']) ? $sighting['lng'] : $default_settings['lng'] ?>);
        var myOptions = {
            zoom: <?php echo isset($sighting['zoom']) ? $sighting['zoom'] : $default_settings['zoom'] ?>,
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
            jQuery('#map_lat').html(Math.round(map.getCenter().lat()*10000)/10000);
            jQuery('#map_lng').html(Math.round(map.getCenter().lng()*10000)/10000);
        });

        // Record map zoom level on zoom
        google.maps.event.addListener(map, 'zoom_changed', function() {
            jQuery('#map_zoom').html(map.getZoom());
        });

        // Reposition marker on mouse drag
        google.maps.event.addListener(marker, 'drag', function() {
            jQuery('#marker_lat').html(Math.round(marker.getPosition().lat()*10000)/10000);
            jQuery('#marker_lng').html(Math.round(marker.getPosition().lng()*10000)/10000);
        });

        // Clear marker div color on mouse drag start
        google.maps.event.addListener(marker, 'dragstart', function() {
            animateBackground('#ffffff','fast',false);
        });
    });

    /**
     * Check if ready to save
     */
    function confirmMarkerLatLng() {
        if(jQuery('#marker_lat').html() == '') {
            animateBackground('#feef87','fast',false);
            postStatusMessage('<?php _e('Please reposition the marker first',SIGHTINGS_HANDLE) ?>',1000);
        }
        else {
            doAjax();
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
                    backgroundColor: '#ffffff'
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
    function doAjax() {
        jQuery.ajax({
            url : '<?php echo plugin_dir_url(SIGHTINGS_PLUGIN_ADMIN_DIR) ?>/admin/sightings-ajax.php',
            data : 'lat='+jQuery('#marker_lat').html()+'&lng='+jQuery('#marker_lng').html()+'&zoom='+jQuery('#map_zoom').html()+'&post_id=<?php echo the_ID(); ?>&display='+jQuery('#sightings_display_toggle').is(':checked')+'&gf_id='+jQuery('#gf_forms option:selected').val(),
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
</script>