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

        $default_settings = self::getSightingsSettings();

        $wp_post_array = array(
            'post_status'=>'draft', // Because we don't want contributor posts to be published without moderation
            'post_author'=>isset($default_settings['author']) ? $default_settings['author'] : 1,
            'post_category'=>array(isset($sightings_post_array['category']) ? $sightings_post_array['category'] : 1),
            'post_title'=>$sightings_post_array['title'],
            'post_content'=>$sightings_post_array['body'],
        );
        $new_post_id = wp_insert_post($wp_post_array);

        if($new_post_id  != 0) {
            $sightings_post_array['display'] = true;
            if(self::saveSightingPostMeta($new_post_id, $sightings_post_array))
                if(isset($default_settings['notify_user'])) {
                    self::notifyAuthorAboutNewContribution($default_settings['author'], $sightings_post_array, $new_post_id);
                }
                else{
                    throw new Exception('Could not save Sightings post meta for post ID: '.$new_post_id);
                }
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

    public function notifyAuthorAboutNewContribution($author_id, $sightings_post_array, $new_post_id) {

        $user = get_userdata($author_id);

        if(!$user) {
            throw new Exception('Could not get userdata for user ID: '.$author_id);
        }

        $to  = $user->user_email;

        // subject
        $subject = __('New Sightings contribution from ',SIGHTINGS_HANDLE). $sightings_post_array['name'];

        // message
        $message = '<html>
                    <head>
                      <title>'. $sightings_post_array['title'] .'</title>
                    </head>
                    <body>
                      <p>'.$sightings_post_array['body'].'</p>
                      <p><strong>lat:'.$sightings_post_array['lat'].'</strong></p>
                      <p><strong>lng:'.$sightings_post_array['lng'].'</strong></p>
                      <p><strong>zoom:'.$sightings_post_array['zoom'].'</strong></p>
                      <br/>
                      <p>'._e('Contributor e-mail: ',SIGHTINGS_HANDLE).'<a href="mailto:'.$sightings_post_array['email'].'">'.$sightings_post_array['email'].'</a></p>
                      <p>'._e('Click here to see the post:',SIGHTINGS_HANDLE).'<a href="'.get_post_permalink($new_post_id).'">'.get_post_permalink($new_post_id).'</a>
                    </body>
                    </html>
                    ';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        // Additional headers
        $headers .= 'To: '.$user->display_name.' <'.$user->user_email.'>' . "\r\n";
        $headers .= 'From: Sightings contributor <'.$sightings_post_array['email'].'>' . "\r\n";

        // Mail it
        mail($to, $subject, $message, $headers);
    }
}