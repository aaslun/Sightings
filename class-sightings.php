<?php

class Sightings_Manager {

    private $settings;

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

}