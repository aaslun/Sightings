<?php

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
        if(is_single() || is_page()) {
            $manager = new Sightings_Manager();
            global $post;
            $sighting = get_post_meta($post->ID,SIGHTINGS_HANDLE,true);
            if(isset($sighting['display'])) {
                return $content . $manager->echoSightingsPostMap($sighting);
            }
        }
        return $content;
    });
