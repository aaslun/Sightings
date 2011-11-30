<?php
/*
Plugin Name: Sightings
Plugin URI: http://webdez.se/2011/10/sightings/
Description: Sightings is an easy to use plugin for geo-tagging your posts with placemarks. Or why don't let your visitors contribute with their own sightings through a form? You can display all placemarks on a large map.  It utilizes Google Maps Javascript API V3.
Version: 1.2
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
 * Filters and actions
 */
require_once __DIR__ . '/filters.php';

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
