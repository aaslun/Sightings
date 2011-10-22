<?php
/**
 *  Logic and template for the [sightings-map] shortcode which produces a map containing sightings from posts
 *
 *  TODO: Support parameters
 *  Optional parameters:
 *  width   -   Map width in pixels or percent
 *  height  -   Map height in pixels or percent
 */

add_shortcode('sightings-map', function($parameters)
    {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;
        // Fetch all sightings from database
        $sightings = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value, post_id FROM $wpdb->postmeta WHERE meta_key = '".SIGHTINGS_HANDLE."'" ), ARRAY_A );

        $width = ''; // map width
        $height = ''; // map height

        extract(
            shortcode_atts(
                array(
                     'width' => '100%',
                     'height' => '500px',
                ), $parameters ) );

        // Map container
        ?>
    <div id="sightings_map" style="width:<?php echo $width ?>; height:<?php echo $height ?>;">
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            <?php
            // Calculate markers center
            $lat = '';
            $lng = '';
            if(count($sightings) > 0) {
                foreach($sightings as $sight)
                {
                    $sight = unserialize($sight['meta_value']);
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
                zoom: 4, // TODO: zoom level should be dynamic
                center: map_latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('sightings_map'),
                    myOptions);
            <?php
            // Render sightings markers on map, only from posts that are published
            if(count($sightings) > 0) {
                foreach($sightings as $sight)
                {
                    $s = unserialize($sight['meta_value']);
                    $post = get_post($sight['post_id']);
                    if($post != '' && $post->post_status == 'publish') {
                        $sight_info = '<p><strong><a href="'.get_post_permalink($post->ID).'">'.$post->post_title.'</strong></p>';
                        ?>
                        var latlng = new google.maps.LatLng(<?php echo $s['lat'] ?>,<?php echo $s['lng'] ?>);
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
            }
            ?>
        });
    </script>

    <?php
    });
?>