<?php
/**
 *  Logic and template for the [sightings-map] shortcode which produces a map containing sightings from posts
 *
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
        $sightings = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '".SIGHTINGS_HANDLE."'" ) );

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
    <div id="sightings_map" style="width:<?= $width ?>; height:<?= $height ?>;">
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            var map_latlng = new google.maps.LatLng(65,13);
            var myOptions = {
                zoom: 4,
                center: map_latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('sightings_map'),
                    myOptions);
            <?
            if(count($sightings) > 0) {
                foreach($sightings as $sight)
                {
                    $sight = unserialize($sight);
                    ?>
                    var latlng = new google.maps.LatLng(<?= $sight['lat'] ?>,<?= $sight['lng'] ?>);
                    var marker = new google.maps.Marker({
                        map:map,
                        draggable:false,
                        animation: google.maps.Animation.DROP,
                        position:latlng
                    });
                    <?
                }
            }
            ?>
        });
    </script>

    <?
    });
?>