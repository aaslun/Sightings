<?php
/** Load WordPress Administration Bootstrap */
require_once('../../../wp-load.php');

$manager = new Sightings_Manager();

$sighting = array();
$post_id = '';

if(isset($_POST['lat'])) {
    $sighting['lat'] = $_POST['lat'];
}
if(isset($_POST['lng'])) {
    $sighting['lng'] = $_POST['lng'];
}
if(isset($_POST['zoom'])) {
    $sighting['zoom'] = $_POST['zoom'];
}
if(isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
}
if(isset($_POST['display'])) {
    if($_POST['display'] != 'false')
    $sighting['display'] = true;
}

if($post_id != '') {
    $manager->saveSightingPostMeta($post_id , $sighting);
    _e('Marker location saved!');
}
else {
    echo 'Error: '.__('Post ID not found! Marker location not saved.');
}