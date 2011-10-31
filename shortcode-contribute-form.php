<?php
/**
 * Logic and template for the Sightings contribute form.
 * Outputs a form that let's your readers submit their own sightings right in the template.
 * A submitted form will end up as a drafted post in the database.
 */



add_shortcode('sightings-form', function() {
        $default_settings = get_option(SIGHTINGS_HANDLE);
        ?>
    <form action="">
        <table id="sightings_form_table">
            <tr>
                <td><label for="sightings_title"><?php _e('Title:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input id="sightings_title" type="text" size="50"/></td>
                <td></td>
            </tr>
            <tr>
                <td><label for="sightings_body"><?php _e('Description:',SIGHTINGS_HANDLE) ?></label></td>
                <td><textarea id="sightings_body" rows="5" cols="50"></textarea></td>
                <td class="marker"><strong><?php _e('Marker',SIGHTINGS_HANDLE) ?>:</strong><br/>
            <?php _e('Latitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lat"><?php echo isset($sighting['lat']) ? $sighting['lat'] : $default_settings['lat'] ?></span>
            <br />
            <?php _e('Longitude:',SIGHTINGS_HANDLE); ?> <span id="marker_lng"><?php echo isset($sighting['lng']) ? $sighting['lng'] : $default_settings['lng'] ?></span>
            <br />
            <?php _e('Zoom:',SIGHTINGS_HANDLE); ?> <span id="map_zoom"><?php echo isset($sighting['zoom']) ? $sighting['zoom'] : $default_settings['zoom'] ?></span><br/>
                <input name="save" type="submit" class="button-primary" value="<?php _e('Submit') ?>"/>
                </td>
            </tr>
        </table>
        <div id="map_canvas" style="width:100%; height:400px;"></div>
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
                    url : '<?php echo SIGHTINGS_PLUGIN_DIR ?>sightings-ajax.php',
                    data : 'lat='+jQuery('#marker_lat').html()+'&lng='+jQuery('#marker_lng').html()+'&zoom='+jQuery('#map_zoom').html()+'&post_id=<?php echo the_ID(); ?>&display='+jQuery('#sightings_display_toggle').is(':checked'),
                    type : 'POST',
                    beforeSend : function(html) {
                        jQuery('#sightings-status').hide().append('<img id="sightings_loader" src="<?php echo SIGHTINGS_PLUGIN_DIR ?>images/loading.gif" alt="loading" />').fadeIn();
                    },
                    success : function(html) {
                        animateBackground('#99ff99','slow',true);
                        postStatusMessage(html,2000);
                        jQuery('#sightings-status').find('img:last').fadeOut();
                    }
                });
            }
        </script>
    </form>
    <?
    });