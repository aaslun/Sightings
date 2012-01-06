<?php
/**
 *  Logic and template for the [sightings-map] shortcode which produces a map containing sightings from posts
 *
 *  TODO: Support attributes
 *  Optional attributes:
 *  width    -  Set map width in pixels or percent
 *  height   -  Set map height in pixels or percent
 *  zoom     -  Set zoom level
 *  cat_id -  Set category ID for posts to be displayed on map (will inherit sub categories). Will display all if not used.
 *  cat_slug -  Set category slug for posts to be displayed on map (will inherit sub categories). Will display all if not used.
 *  allow_contributors - If contributor link should be visible or not. Visible by default.
 */

function sightings_map_function($parameters) {
    // Extracting attributes as variables
    $height = '';
    $width = '';
    $zoom = '';
    $cat_id = '';
    $cat_slug = '';
    $draggable = '';
    $scrollwheel = '';
    $allow_contributors = '';
    $allow_contributor_image = '';

    extract(
        shortcode_atts(
            array( // Default attribute values for the sightings-map
                'width' =>  '100%',
                'height'=>  '400px',
                'zoom'  =>  '6',
                'draggable' => 'true',
                'scrollwheel' => 'false',
                'allow_contributors' => 'true',
                'allow_contributor_image' => 'true',
                'cat_id' => '',
                'cat_slug' => ''
            ), $parameters ) );

    $manager = new Sightings_Manager();

    $sightings_post_array = array();

    // If contribution form was posted, create new post
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

    if(isset($_FILES['sightings_image_file']) && $_FILES['sightings_image_file'] != '') {
        $sightings_post_array['image_file'] = $_FILES['sightings_image_file'];
    }

    if(isset($_POST['sightings_category']) && $_POST['sightings_category'] != '') {
        $sightings_post_array['category'] = $_POST['sightings_category'];
    }

    if(isset($_POST['sightings_contributor_name']) && $_POST['sightings_contributor_name'] != '') {
        $sightings_post_array['name'] = $_POST['sightings_contributor_name'];
    }

    if(isset($_POST['sightings_contributor_email']) && $_POST['sightings_contributor_email'] != '') {
        $sightings_post_array['email'] = $_POST['sightings_contributor_email'];
    }

    if(count($_POST) > 0) {
        ?>
    <div id="sightings_message">
        <?php
        // TODO: Perhaps some more distinct validation here
        if(empty($sightings_post_array['title']) || empty($sightings_post_array['body']) || empty($sightings_post_array['lat']) || empty($sightings_post_array['lng']) || empty($sightings_post_array['zoom'])) {
        echo '<p class="error">';
        _e('The contribution was not submitted! You need to fill out the form completely.',SIGHTINGS_HANDLE);
        echo '</p>';
    }
    else {
        $manager->createSightingsPost($sightings_post_array);

        echo '<p class="success">';
        _e('Thanks for your contribution! It has now been submitted for review.',SIGHTINGS_HANDLE);
        echo '</p>';
    }
        ?>
    </div>
        <?php
                    }

    $default_settings = $manager->getSightingsSettings();

    if((empty($cat_id)) && (empty($cat_slug)))
        $sightings = $manager->getAllSightings();
    else
        $sightings = $manager->getSightingsByCategory($cat_slug ? $cat_slug : $cat_id); // cat_slug dominates cat_id

    // Map container
    ?>
    <div id="sightings_map" style="width:<?php echo $width ?>; height:<?php echo $height ?>;">
    </div>
    <?php if($allow_contributors) : ?>
        <div class="sightings_contributor_panel">
            <a href="#">[+] <?php _e('Contribute with a location',SIGHTINGS_HANDLE) ?></a>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            <?php
            // Calculate markers center
            $lat = '';
            $lng = '';
            if(count($sightings) > 0) {
                foreach($sightings as $sight)
                {
                    $sight = get_post_meta($sight->ID, SIGHTINGS_HANDLE, ARRAY_A);
                    $lat += $sight['lat'];
                    $lng += $sight['lng'];
                }
                $lat = ($lat / count($sightings));
                $lng = ($lng / count($sightings));
            }
            else {
                $lat = 65;
                $lng = 13;
            }
            ?>
            var map_latlng = new google.maps.LatLng(<?php echo $lat ?>,<?php echo $lng ?>);
            var myOptions = {
                zoom: <?php echo $zoom ?>,
                center: map_latlng,
                draggable: <?php echo $draggable ?>,
                scrollwheel: <?php echo $scrollwheel ?>,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE
                  },
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('sightings_map'),
                    myOptions);
            <?php
            // Render sighting markers on map
            if(count($sightings) > 0) {
                foreach($sightings as $sight)
                {

                    $sight_meta = get_post_meta($sight->ID, SIGHTINGS_HANDLE, ARRAY_A);

                    if($sight != '' && $sight->post_status == 'publish') {
                        $sight_info = '<div class="sight_info">';
                        has_post_thumbnail($sight->ID) ? $sight_info .= '<p>'.get_the_post_thumbnail($sight->ID).'</p>' : '';
                        $sight_info .= '<p><strong><a href="'.get_post_permalink($sight->ID).'">'.$sight->post_title.'</a></strong></p>';
                        $sight_info .= '<p class="excerpt">'.($sight->post_excerpt != '' ? $manager->shorten($sight->post_excerpt,100) : $manager->shorten($sight->post_content,100)).'</p>';
                        $sight_categories = wp_get_post_categories($sight->ID);
                        $sight_info .= '<p><strong>'.get_cat_name($sight_categories[0]).'</strong></p>'; // Will only fetch the first category
                        if(isset($sight_meta['gf_id']))
                        { // START Gravity Forms optional features
                            $num_submits = RGFormsModel::get_form_counts($sight_meta['gf_id']);
                            $sight_info .= '<p>'.__('Responses received',SIGHTINGS_HANDLE).': '.$num_submits['total'].'</p>';
                        } // END Gravity Forms optional features
                        $sight_info .= '</div>';
                        ?>
                        var latlng = new google.maps.LatLng(<?php echo $sight_meta['lat'] ?>,<?php echo $sight_meta['lng'] ?>);
                        var infoWindow = new google.maps.InfoWindow ();
                        var marker = new google.maps.Marker({
                            map: map,
                            draggable: false,
                            animation: google.maps.Animation.DROP,
                            position: latlng
                        });
                        google.maps.event.addListener(marker, 'click', function(){
                            infoWindow.setContent('<?php echo $sight_info ?>');
                            infoWindow.open(map, this);
                        });
                        <?php
                    }
                }
            } ?>
            jQuery('.sightings_contributor_panel a').one('click', function() {
                var markerImage = new google.maps.MarkerImage('<?php echo SIGHTINGS_PLUGIN_DIR_URL ?>/images/blue-marker.png',
                      new google.maps.Size(32,32),
                      new google.maps.Point(0,0),
                      new google.maps.Point(16,32)
                );
                var markerShadow = new google.maps.MarkerImage('<?php echo SIGHTINGS_PLUGIN_DIR_URL ?>/images/marker-shadow.png',
                      new google.maps.Size(52,32),
                      new google.maps.Point(0,0),
                      new google.maps.Point(16,32)
                );

                var infoWindow = new google.maps.InfoWindow ();
                <?php // Setup the contributor form
                $sight_form = '<form action="#" method="post" enctype="multipart/form-data"><div class="contributor_form">';
                $sight_form .='<div><label for="sightings_title">'.__('Title',SIGHTINGS_HANDLE).':</label><input id="sightings_title" type="text" name="sightings_title"/></div>';
                $sight_form .='<div><label for="sightings_body">'.__('Description',SIGHTINGS_HANDLE).':</label><textarea id="sightings_body" cols rows name="sightings_body"></textarea></div>';
                if($allow_contributor_image != 'false')
                    $sight_form .='<div><label for="sightings_image">'.__('Attach an image (optional)',SIGHTINGS_HANDLE).':<input type="file" name="sightings_image_file" id="sightings_image_file" value="'.__('Browse',SIGHTINGS_HANDLE).'" /></label><p id="browsed_image"><em>'.__('No image file selected',SIGHTINGS_HANDLE).'</em></p></div>';
                $sight_form .='<div><label for="sightings_contributor_name">'.__('Your name',SIGHTINGS_HANDLE).':</label><input id="sightings_contributor_name" type="text" name="sightings_contributor_name"/></div>';
                $sight_form .='<div><label for="sightings_contributor_email">'.__('Your e-mail',SIGHTINGS_HANDLE).':</label><input id="sightings_contributor_email" type="text" name="sightings_contributor_email"/></div>';

                // Hidden fields containing marker lat, lng and map zoom level
                $sight_form .='<input type="hidden" id="marker_lat_hidden" name="marker_lat" >';
                $sight_form .='<input type="hidden" id="marker_lng_hidden" name="marker_lng" >';
                $sight_form .='<input type="hidden" id="map_zoom_hidden" name="map_zoom" value="9"></form>';

                // Contributor categories
                if(isset($default_settings['contributor_categories']) && count($default_settings['contributor_categories']) > 0) {
                    $sight_form .= '<label for="sightings_category">'.__('Category',SIGHTINGS_HANDLE).':</label><select id="sightings_category" name="sightings_category">';
                    foreach($default_settings['contributor_categories'] as $cat) {
                        $sight_form .= '<option value="'.$cat.'">'.get_cat_name($cat).'</option>';
                    }
                    $sight_form .= '</select>';
                }

                $sight_form .='<div><input type="submit" value="'.__('Submit',SIGHTINGS_HANDLE).'"></div></div></form>';
                ?>

                var marker = new google.maps.Marker({
                        map: map,
                        draggable: true,
                        animation: google.maps.Animation.DROP,
                        position: map.getCenter(),
                        icon: markerImage,
                        shadow: markerShadow
                    });
                
                // Initial marker information
                var greet = function () {
                    var stopAnimation = function() {
                        marker.setAnimation(null);
                    };
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    infoWindow.setContent('<?php _e('<p>Drag me and klick me!</p>',SIGHTINGS_HANDLE) ?>');
                    infoWindow.open(map, marker);
                    setTimeout(stopAnimation,500);
                };
                setTimeout(greet,1000);


                google.maps.event.addListener(marker, 'click', function(){
                    infoWindow.setContent('<?php echo $sight_form ?>');
                    infoWindow.open(map, this);
                    var updateSightForm = function () {
                        jQuery('#map_zoom_hidden').val(map.getZoom());
                        jQuery('#marker_lat_hidden').val(Math.round(marker.getPosition().lat()*10000)/10000);
                        jQuery('#marker_lng_hidden').val(Math.round(marker.getPosition().lng()*10000)/10000);

                        jQuery('#sightings_image_file').change(function() {
                            jQuery('#browsed_image').html(jQuery(this).val().replace('C:\\fakepath\\', ''));
                        });
                    };
                    setTimeout(updateSightForm,500); // Since sight_form does not always exist before this
                });

                // Close infoWindow on dragstart
                google.maps.event.addListener(marker, 'dragstart', function() {
                    infoWindow.close();
                });

                jQuery(this).parent().slideUp().unbind('click');

                return false;
            });
        });
    </script>

    <?php
    };

add_shortcode('sightings-map', 'sightings_map_function');
?>