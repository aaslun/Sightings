<?php
/** Load WordPress Administration Bootstrap */
require_once('../../../wp-load.php');

$geotag = array();
$post_id = '';

if(isset($_POST['lat'])) {
    $geotag['lat'] = $_POST['lat'];
}
if(isset($_POST['lng'])) {
    $geotag['lng'] = $_POST['lng'];
}
if(isset($_POST['zoom'])) {
    $geotag['zoom'] = $_POST['zoom'];
}
if(isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
}
if(isset($_POST['display'])) {
    if($_POST['display'] != 'false')
    $geotag['display'] = true;
}

if($post_id != '') {
    update_post_meta($post_id,SIGHTINGS_HANDLE,$geotag);
    _e('Marker location saved!');
}
else {
    echo 'Error: '.__('Post ID not found! Marker location not saved.');
}