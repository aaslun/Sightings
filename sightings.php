<?php
/*
Plugin Name: Sightings
Plugin URI: http://webdez.se/2011/10/sightings/
Description: Sightings is an easy to use plugin for geo-tagging your posts with placemarks. You can display all placemarks on a large map. It utilizes Google Maps Javascript API V3.
Version: 1.1
Author: Andreas Lundgren
Author URI: http://webdez.se/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
 * Plugin constants
 */
define ('SIGHTINGS_HANDLE','sightings');
define ('SIGHTINGS_PLUGIN_DIR', plugin_dir_url(__FILE__));

/*
 * Plugin textdomain
 */
load_plugin_textdomain(SIGHTINGS_HANDLE, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

/*
 * Load Sightings libraries
 */
require_once __DIR__ . '/class-sightings.php';

/*
 * Admin actions
 */

// Enqueue scripts and style
add_action('init', function() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('google_maps_javascript','http://maps.googleapis.com/maps/api/js?sensor=true');
        wp_enqueue_style(SIGHTINGS_HANDLE.'_style', plugin_dir_url(__FILE__).'sightings.css');

        // Register shortcodes
        include __DIR__ . '/shortcode-sightings-map.php';
        include __DIR__ . '/shortcode-contribute-form.php';
    });

// Post & page admin meta boxes
add_action('add_meta_boxes', function() {
        add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'post', 'normal', 'high');
        add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'page', 'normal', 'high');
    });

// Admin menu item
add_action('admin_menu', function() {
        add_options_page('Sightings',__('Sightings', SIGHTINGS_HANDLE),'manage_options','sightings-settings','sightings_menu_page');
    });

/*
 * Filter to echo the post map
 */
add_filter('comments_template',function($content) {
        if(is_single()) {
            global $post;
            $sighting = get_post_meta($post->ID,SIGHTINGS_HANDLE,true);
            if(isset($sighting['display'])) {
                return $content . echo_sightings_post_map($sighting);
            }
        }
        return $content;
    });

/**
 * Function for creating the sightings meta box in admin
 * @return void
 */
function sightings_meta_box() {
    // Using nonce for verification that this meta box is posted
    wp_nonce_field(plugin_basename(__FILE__), SIGHTINGS_HANDLE);

    // include meta box template file
    require_once __DIR__ . '/sightings-meta-box.php';
}

/**
 * Sightings plugin settings page
 * @return void
 */
function sightings_menu_page() {
    // include sightings settings template file
    require_once __DIR__ . '/sightings-settings.php';
}

/**
 * Echo the Sightings post map
 * @param $sighting
 * @return void
 */
function echo_sightings_post_map($sighting) {
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
