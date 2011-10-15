<?php
if(isset($_POST['map_lat'])
   && isset($_POST['map_lng'])
   && isset($_POST['map_zoom']))
{
    $option['lat'] = $_POST['map_lat'];
    $option['lng'] = $_POST['map_lng'];
    $option['zoom'] = $_POST['map_zoom'];
    $option['display'] = isset($_POST['display']) ? 1 : 0;
    update_option(SIGHTINGS_HANDLE, $option);
    ?>
<div id="status_success"><?php _e('Settings saved successfully', SIGHTINGS_HANDLE) ?></div>
<?php
}
else {
    $option = get_option(SIGHTINGS_HANDLE);
}
?>

<div id="sightings_map_settings wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2 style="padding-top: 15px"><?php _e('Sightings settings',SIGHTINGS_HANDLE) ?></h2>
    <br />
    <form name="sightins_settings" action="" method="post">
        <h4><?php _e('Default map settings',SIGHTINGS_HANDLE) ?></h4>
        <table style="margin-bottom: 20px">
            <tr><td><label for="map_lat"><?php _e('Latitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lat" id="map_lat" type="text" size="10" value="<?php echo $option ? $option['lat'] : '' ?>"/></td></tr>
            <tr><td><label for="map_lng"><?php _e('Longitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lng" id="map_lng" type="text" size="10" value="<?php echo $option ? $option['lng'] : '' ?>"/></td></tr>
            <tr><td><label for="map_zoom"><?php _e('Zoom level:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_zoom" id="map_zoom" type="text" size="2" value="<?php echo $option ? $option['zoom'] : '' ?>"/></td></tr>
        </table>
        <h4><?php _e('Default display settings',SIGHTINGS_HANDLE); ?></h4>
        <table style="margin-bottom: 20px">
            <tr><td><label for="display"><?php _e('Display maps on posts:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="display" id="display" type="checkbox" <?php echo $option['display'] == 1 ? 'checked="checked"' : '' ?> value="<?php echo $option['display'] == 1 ? true : false ?>"/></td></tr>
        </table>
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#status_success').delay(3000).slideUp();
    });
</script>