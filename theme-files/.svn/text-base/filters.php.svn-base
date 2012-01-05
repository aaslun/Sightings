<?php

/*
 * Admin actions
 */

// Enqueue scripts and style
function sightings_init_style() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('google_maps_javascript','http://maps.googleapis.com/maps/api/js?sensor=true');
        wp_enqueue_style(SIGHTINGS_HANDLE.'_style', SIGHTINGS_PLUGIN_DIR_URL.'sightings.css');

        // Register shortcodes
        include __DIR__ . '/shortcode-sightings-map.php';
        include __DIR__ . '/shortcode-contribute-form.php';
    };
add_action('init', 'sightings_init_style');

// Post & page admin meta boxes
function sightings_add_meta_boxes() {
        add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'post', 'normal', 'default');
        add_meta_box(SIGHTINGS_HANDLE, __('Map', SIGHTINGS_HANDLE), 'sightings_meta_box', 'page', 'normal', 'default');
    }
add_action('add_meta_boxes', 'sightings_add_meta_boxes');

// Admin menu item
function sightings_admin_menu() {
        add_options_page('Sightings',__('Sightings', SIGHTINGS_HANDLE),'manage_options','sightings-settings','sightings_menu_page');
    };
add_action('admin_menu', 'sightings_admin_menu');

/**
 * Filter to echo the post map
 * @param $content
 * @return string
 */
function sightings_echo_post_map($content) {
        if(is_singular()) {
            $manager = new Sightings_Manager();
            global $post;
            $sighting = get_post_meta($post->ID,SIGHTINGS_HANDLE,true);
            if(isset($sighting['display'])) {
                return $manager->echoSightingsPostMap($sighting) . $content;
            }
        }
        return $content;
    };
add_filter('the_complete_title','sightings_echo_post_map');

