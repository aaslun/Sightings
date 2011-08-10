<?php
/*
Plugin Name: Sightings
Plugin URI: http://webdez.se/
Description: Sightings is a plugin for geo-tagging your posts with a placemark. It was created since the other available geotagging plugins did not fulfill my needs and expectations. It utilizes Google Maps Javascript API V3.
Version: 1.0
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
 * Plug-in constants
 */
define ('SIGHTINGS_HANDLE','sightings');


/*
 * Add map meta box to post and page in admin
 */
add_action('add_meta_boxes', function() {
    add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'post', 'normal', 'high');
    add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'page', 'normal', 'high');
});

/*
 * Enqueue Google Maps API JavaScript
 */
add_action('init', function() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('google_maps_javascript','http://maps.googleapis.com/maps/api/js?sensor=true');
        wp_enqueue_style(SIGHTINGS_HANDLE.'_style', plugin_dir_url(__FILE__).'sightings.css');
    });


function sightings_meta_box() {
    // Using nonce for verification that this meta box is posted
    wp_nonce_field(plugin_basename(__FILE__), SIGHTINGS_HANDLE);

    // include template
    include __DIR__ . '/sightings-meta-box.php';
}