<?php

class Sightings_Manager {

    function __construct() {

    }

    /**
     * Returns all Sightings from the database
     * @return array
     */
    public function getAllSightings() {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;
        $sightings = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value, post_id FROM $wpdb->postmeta WHERE meta_key = '".SIGHTINGS_HANDLE."'" ), ARRAY_A );
        return $sightings ? $sightings : array();
    }

    /**
     * Echoes the presentation of a map for a Sighting
     * @param $sighting array
     * @return void
     */
    public function echoSightingsPostMap($sighting) {
    ?>
        <div id="map_canvas" style="width:100%; height:200px;"></div>
        <script type="text/javascript">
            // Load the map
            jQuery(window).load(function(){
                var latlng = new google.maps.LatLng(<?php echo isset($sighting['lat']) ? $sighting['lat'] : '' ?>, <?php echo isset($sighting['lng']) ? $sighting['lng'] : '' ?>);
                var myOptions = {
                    zoom: <?php echo isset($sighting['zoom']) ? $sighting['zoom'] : 5 ?>,
                    center: latlng,
                    draggable: false,
                    zoomControl: false,
                    scrollwheel: false,
                    streetViewControl: false,
                    panControl: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map(document.getElementById('map_canvas'),
                        myOptions);

                var marker = new google.maps.Marker({
                    map:map,
                    draggable:false,
                    animation: google.maps.Animation.DROP,
                    position:latlng
                });
            });
        </script>
    <?php
    }

    public function createSightingsPost ($sightings_post_array) {
        $wp_post_array = array(
            'post_status'=>'draft',
            'post_author'=>1, // TODO: Set this ID to something better, let this be a part of the settings
            'post_category'=>array(1), // TODO: Set to other than Uncategorized, let this be a part of the settings
            'post_title'=>$sightings_post_array['title'],
            'post_content'=>$sightings_post_array['body'],
        );
        $new_post_id = wp_insert_post($wp_post_array);

        if($new_post_id  != 0) {
            $sightings_post_array['display'] = true;
            self::saveSightingPostMeta($new_post_id, $sightings_post_array);
        }
        else {
            throw new Exception('Could not create new Sighting');
        }
    }
    /**
     * Updates Sighting for a post
     * @param $post_id
     * @param $sighting
     * @return bool true|false was updated
     */
    public function saveSightingPostMeta($post_id, $sighting) {
        return update_post_meta($post_id,SIGHTINGS_HANDLE,$sighting);
    }

    /**
     * Updates Sightings settings
     * @param $settings array
     * @return bool true|false was updated
     */
    public function saveSightingsSettings($settings) {
        return update_option(SIGHTINGS_HANDLE, $settings);
    }

    /**
     * Returns the Sightings settings array from database
     * @return array
     */
    public function getSightingsSettings() {
        return get_option(SIGHTINGS_HANDLE);
    }
}