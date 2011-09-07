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
        var latlng = new google.maps.LatLng(65,18);
        var myOptions = {
            zoom: 8,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById('sightings_map'),
                myOptions);
    });
    </script>
        
    <?
    });
?>