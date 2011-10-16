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
    <div id="donate_container" style="display:none">
        <?php
            insert_donate_button();
        ?>
        <span class="howto">Enjoying the Sightings plugin? It's free! Please consider a small donation to support it's development. :)</span>
    </div>
    <h2 style="padding-top: 15px"><?php _e('Sightings settings',SIGHTINGS_HANDLE) ?></h2>
    <br />
    <form name="sightins_settings" action="" method="post">
        <h4><?php _e('Default map settings',SIGHTINGS_HANDLE) ?>:</h4>
        <span class="howto"><?php _e('The default position and zoom level for the maps on new posts.',SIGHTINGS_HANDLE) ?></span>
        <table style="margin-bottom: 20px">
            <tr><td><label for="map_lat"><?php _e('Latitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lat" id="map_lat" type="text" size="10" value="<?php echo $option ? $option['lat'] : '' ?>"/></td></tr>
            <tr><td><label for="map_lng"><?php _e('Longitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lng" id="map_lng" type="text" size="10" value="<?php echo $option ? $option['lng'] : '' ?>"/></td></tr>
            <tr><td><label for="map_zoom"><?php _e('Zoom level:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_zoom" id="map_zoom" type="text" size="2" value="<?php echo $option ? $option['zoom'] : '' ?>"/></td></tr>
        </table>
        <h4><?php _e('Default display settings',SIGHTINGS_HANDLE); ?>:</h4>
        <span class="howto"><?php _e('Toggles whether the map should be displayed or not by default on new posts.',SIGHTINGS_HANDLE) ?></span>
        <table style="margin-bottom: 20px">
            <tr><td><label for="display"><?php _e('Display sightings maps on posts:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="display" id="display" type="checkbox" <?php echo $option['display'] == 1 ? 'checked="checked"' : '' ?> value="<?php echo $option['display'] == 1 ? true : false ?>"/></td></tr>
        </table>
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#status_success').delay(3000).slideUp();
        jQuery('#donate_container').delay(5000).slideDown();
    });
</script>

<?php
    function insert_donate_button() {
    ?>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
        ">
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    <?
}
?>