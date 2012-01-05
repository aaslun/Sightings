<?php
/**
 * Logic and template for the Sightings contribute form.
 * Outputs a form that let's your readers submit their own sightings right in the template.
 * A submitted form will end up as a drafted post in the database.
 */

add_shortcode('sightings-form', function() {

        $manager = new Sightings_Manager();

        $sightings_post_array = array();

        if(isset($_POST['sightings_title']) && $_POST['sightings_title'] != '') {
            $sightings_post_array['title'] = $_POST['sightings_title'];
        }

        if(isset($_POST['sightings_body']) && $_POST['sightings_body'] != '') {
            $sightings_post_array['body'] = $_POST['sightings_body'];
        }

        if(isset($_POST['marker_lat']) && $_POST['marker_lat'] != '') {
            $sightings_post_array['lat'] = $_POST['marker_lat'];
        }

        if(isset($_POST['marker_lng']) && $_POST['marker_lng'] != '') {
            $sightings_post_array['lng'] = $_POST['marker_lng'];
        }

        if(isset($_POST['map_zoom']) && $_POST['map_zoom'] != '') {
            $sightings_post_array['zoom'] = $_POST['map_zoom'];
        }

        if(isset($_POST['sightings_category']) && $_POST['sightings_category'] != '') {
            $sightings_post_array['category'] = $_POST['sightings_category'];
        }

        if(isset($_POST['contributor_name']) && $_POST['contributor_name'] != '') {
            $sightings_post_array['name'] = $_POST['contributor_name'];
        }

        if(isset($_POST['contributor_email']) && $_POST['contributor_email'] != '') {
            $sightings_post_array['email'] = $_POST['contributor_email'];
        }

        if(count($_POST) > 0) {
            ?>
        <div id="sightings_message">
            <?php
            // TODO: Perhaps some more distinct validation here
            if(empty($sightings_post_array['title']) || empty($sightings_post_array['body']) || empty($sightings_post_array['lat']) || empty($sightings_post_array['lng']) || empty($sightings_post_array['zoom']) || empty($sightings_post_array['name']) || empty($sightings_post_array['email'])) {
            echo '<p class="error">';
            _e('The contribution was not submitted! You need to fill out the form completely.',SIGHTINGS_HANDLE);
            echo '</p>';
        }
        else {
            $manager->createSightingsPost($sightings_post_array);

            echo '<p class="success">';
            _e('Thanks for your contribution! It has now been submitted for review.',SIGHTINGS_HANDLE);
            echo '</p>';
            return;
        }
            ?>
        </div>
            <?php
                        }

        $default_settings = $manager->getSightingsSettings();

        ?>
    <div id="message"></div>
    <form action="" method="post">
        <table id="sightings_form_table">
            <tr>
                <td><label for="sightings_title"><?php _e('Title:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input id="sightings_title" type="text" size="50" name="sightings_title"/></td>
                <td></td>
            </tr>
            <?php if(isset($default_settings['contributor_categories'])) : ?>
            <tr>
                <td><label for="sightings_category"><?php _e('Category:',SIGHTINGS_HANDLE) ?></label></td>
                <td>
                    <select id="sightings_category" name="sightings_category">
                        <?php
                        foreach($default_settings['contributor_categories'] as $cat) {
                            echo '<option value="'.$cat.'">'.get_cat_name($cat).'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><label for="sightings_body"><?php _e('Location description:',SIGHTINGS_HANDLE) ?></label></td>
                <td><textarea id="sightings_body" rows="5" cols="50" name="sightings_body"></textarea></td>
                <td class="marker"><strong><?php _e('Marker',SIGHTINGS_HANDLE) ?>:</strong><br/>
                    <?php _e('Latitude:',SIGHTINGS_HANDLE) ?> <span id="marker_lat"><?php echo $default_settings['lat'] ?></span>
                    <input type="hidden" id="marker_lat_hidden" name="marker_lat">
                    <br />
                    <?php _e('Longitude:',SIGHTINGS_HANDLE) ?> <span id="marker_lng"><?php echo $default_settings['lng'] ?></span>
                    <input type="hidden" id="marker_lng_hidden" name="marker_lng">
                    <br />
                    <?php _e('Zoom:',SIGHTINGS_HANDLE) ?> <span id="map_zoom"><?php echo $default_settings['zoom'] ?></span><br/>
                    <input type="hidden" id="map_zoom_hidden" name="map_zoom">
                </td>
            </tr>
            <tr>
                <td><label for="contributor_name"><?php _e('Your name:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input id="contributor_name" type="text" size="50" name="contributor_name"/></td>
                <td></td>
            </tr>
            <tr>
                <td><label for="contributor_email"><?php _e('Your e-mail:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input id="contributor_email" type="text" size="50" name="contributor_email"/></td>
                <td></td>
            </tr>
        </table>
        <p class="info"><?php _e('Drag-and-drop the marker to your desired position on the map',SIGHTINGS_HANDLE) ?></p>
        <table id="sightings_map_table">
            <tr>
                <td><label for="map_canvas"><?php _e('Map',SIGHTINGS_HANDLE) ?>:</label></td>
                <td id="map_canvas" style="width:100%; height:400px;"></td>
                <td>
                    <input name="save" type="submit" class="button-primary" value="<?php _e('Submit') ?>"/>
                </td>
            </tr>
        </table>

        <script type="text/javascript">
            // Load the map
            jQuery(document).ready(function(){
                var latlng = new google.maps.LatLng(<?php echo $default_settings['lat'] .', '. $default_settings['lng'] ?>);
                var myOptions = {
                    zoom: <?php echo $default_settings['zoom'] ?>,
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
                    jQuery('#map_zoom_hidden').val(map.getZoom());
                });

                // Reposition marker on mouse drag.
                google.maps.event.addListener(marker, 'drag', function() {
                    jQuery('#marker_lat').html(Math.round(marker.getPosition().lat()*10000)/10000);
                    jQuery('#marker_lat_hidden').val(Math.round(marker.getPosition().lat()*10000)/10000);
                    jQuery('#marker_lng').html(Math.round(marker.getPosition().lng()*10000)/10000);
                    jQuery('#marker_lng_hidden').val(Math.round(marker.getPosition().lng()*10000)/10000);
                });

                // Clear marker div color on mouse drag start
                google.maps.event.addListener(marker, 'dragstart', function() {
                    animateBackground('#ffffff','fast',false);
                });
            });

        </script>
    </form>
    <?
    });