<?php
/** Load WordPress Administration Bootstrap */
require_once('../../../../wp-load.php');

$manager = new Sightings_Manager();

$sighting = array();
$post_id = '';

if(isset($_POST['markers'])) {
    $sighting['markers'] = $_POST['markers'];
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
if(isset($_POST['delete_sight']) && $post_id != '') {
    if($manager->deleteSightingPostMeta($post_id))
        _e('No markers present, marker information deleted.',SIGHTINGS_HANDLE);
}
else if(!isset($_POST['delete_sight']) && $post_id != '') {
    if($manager->saveSightingPostMeta($post_id , $sighting))
        _e('Marker location saved!',SIGHTINGS_HANDLE);
}
else {
    echo 'Error: '.__('Post ID not found! Marker location not saved.',SIGHTINGS_HANDLE);
}