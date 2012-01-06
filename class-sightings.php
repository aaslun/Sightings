<?php

class Sightings_Manager {

    function __construct() {

    }

    /**
     * Returns all Sightings from the database
     * @return array
     */
    public function getAllSightings() {

        $posts_array = $posts_array = get_posts(array(
                        'post_status'   =>  'publish',
                        'post_type' =>  'post',
                        'meta_key'  =>  SIGHTINGS_HANDLE
                ));

        return $posts_array ? $posts_array : array();
    }

    /**
     * Returns all Sightings for a specific category
     * @param $cat Either a category ID or a category slug
     * @return array
     */
    public function getSightingsByCategory($cat) {
        $category = is_numeric($cat) ? get_category($cat) : get_category_by_slug($cat);

        if($category == null)
            trigger_error('No such category! Please check your sightings-map category parameters.', E_USER_ERROR);

        // Get all posts with this category
        $posts_array = get_posts(array(
                'category'  =>  $category->term_id,
                'post_status'   =>  'publish',
                'post_type' =>  'post',
                'meta_key'  =>  SIGHTINGS_HANDLE
        ));

        return $posts_array ? $posts_array : array();
    }

    public function isGravityFormsActive() {
        return is_plugin_active('gravityforms/gravityforms.php');
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

            // Create image attachment for new Sighting
            if(isset($sightings_post_array['image_file'])) {

                $file = $sightings_post_array['image_file'];

                $new_attachment_id = self::handleUpload($file, $new_post_id);

                if(!$new_attachment_id) {
                    throw new Exception('Could not handle upload for Sighting id:'.$new_post_id);
                }
                else {
                    // Set new attachment as new post thumbnail
                    if(!set_post_thumbnail($new_post_id, $new_attachment_id))
                        throw new Exception ('Could not set the new attachment as post thumbnail for post '.$new_post_id);
                }
            }

            if(self::saveSightingPostMeta($new_post_id, $sightings_post_array)) {
                if(isset($default_settings['notify_user'])) {
                    self::notifyAuthorAboutNewContribution($default_settings['author'], $sightings_post_array, $new_post_id);
                }
            }
            else throw new Exception('Could not save Sightings post meta for post ID: '.$new_post_id);
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

    /**
     * Shorten a string if too long
     * @static
     * @param string $str
     * @param int $max_length
     * @param string $ending[optional='...']
     * @param string $append_to[optional=StringUtil::APPEND_TO_END]
     * @return string
     */
    public static function shorten($str, $max_length, $ending = '...', $append_to = 'end') {
        return self::shortener($str, $max_length, $ending, $append_to);
    }

    /**
     * @ignore
     * @return string
     */
    private static function shortener($str, $max_length, $ending, $append_to, $trim_to_last_word = false) {
        if (strlen($str) > $max_length) {
            $str = trim(mb_substr($str, 0, $max_length, 'UTF-8'));
            if ($trim_to_last_word) {
                $last_space_pos = strrpos($str, ' ');
                if ($last_space_pos !== false) {
                    $str = mb_substr($str, 0, $last_space_pos, 'UTF-8');
                }
            }
            if ($append_to == 'end') {
                return $str . $ending;
            }
            else {
                return $ending . $str;
            }
        }
        return $str;
    }

    /**
     * Uploads a file as an attachment to a specific post ID
     * @param $file
     * @param $post_id
     * @return int attachment ID
     * @throws Exception
     */
    public function handleUpload($file, $post_id) {
        include_once( ABSPATH . '/wp-admin/includes/file.php');

        $upload = wp_handle_upload($file, array('test_form' => FALSE));
        if(!isset($upload['error']) && isset($upload['file'])) {
            $filetype   = wp_check_filetype(basename($upload['file']), null);
            $title      = $file['name'];
            $ext        = strrchr($title, '.');
            $title      = ($ext !== false) ? substr($title, 0, -strlen($ext)) : $title;
            $attachment = array(
                'post_mime_type'    => $filetype['type'],
                'post_title'        => addslashes($title),
                'post_content'      => '',
                'post_status'       => 'inherit',
                'post_parent'       => $post_id
            );
            return wp_insert_attachment($attachment, $upload['file'], $post_id);
        }
        else {
            throw new Exception('Could not handle upload');
        }
    }

    /**
     * Sends an e-mail to the current selected contributor author about new contribution
     * Requires PHP mail to be activated
     * @param $author_id
     * @param $sightings_post_array
     * @param $new_post_id
     * @throws Exception
     */
    public function notifyAuthorAboutNewContribution($author_id, $sightings_post_array, $new_post_id) {

        $user = get_userdata($author_id);

        if(!$user) {
            throw new Exception('Could not get userdata for user ID: '.$author_id);
        }

        $to  = $user->user_email;

        // subject
        $subject = __('New Sightings contribution from ',SIGHTINGS_HANDLE). (isset($sightings_post_array['name']) ? $sightings_post_array : __('Anonymous', SIGHTINGS_HANDLE));

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
                      <br/>';
        if(isset($sightings_post_array['email']))
            $message .=  '<p>'.__('Contributor e-mail: ',SIGHTINGS_HANDLE).'<a href="mailto:'.$sightings_post_array['email'].'">'.$sightings_post_array['email'].'</a></p>';
        $message .=  '<p>'.__('Click here to see the post:',SIGHTINGS_HANDLE).'<a href="'.get_post_permalink($new_post_id).'">'.get_post_permalink($new_post_id).'</a>
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