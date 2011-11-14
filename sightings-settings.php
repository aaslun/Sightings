<?php

$manager = new Sightings_Manager();

if(count($_POST) > 0)
{
    $settings['lat'] = $_POST['map_lat'];
    $settings['lng'] = $_POST['map_lng'];
    $settings['zoom'] = $_POST['map_zoom'];
    $settings['display'] = isset($_POST['display']) ? 1 : 0;
    _log($_POST['contributor_categories']);

    if(count($settings) > 0) {
        $manager->saveSightingsSettings($settings);
    }
    ?>
<div id="status_success"><?php _e('Settings saved successfully', SIGHTINGS_HANDLE) ?></div>
<?php
}
else {
    $settings = $manager->getSightingsSettings();
}
?>

<div id="status_fail" style="display:none;"><?php _e('Settings are invalid! Only numeric values and dots are allowed. (no commas)', SIGHTINGS_HANDLE) ?></div>

<div id="sightings_map_settings wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <div id="donate_container" style="display:none">
        <span class="howto">Enjoying the Sightings plugin? Please consider <a href="http://wordpress.org/extend/plugins/sightings/">rating it</a> to support it's development. :)<br/>
        Or give a small <a href="http://webdez.se/donate">donation</a>?</span>
    </div>
    <h2 style="padding-top: 15px"><?php _e('Sightings settings',SIGHTINGS_HANDLE) ?></h2>
    <br />
    <hr />
    <form name="sightins_settings" action="" onsubmit="return validateSettings();" method="post">
        <h3><?php _e('Map settings',SIGHTINGS_HANDLE) ?></h3>
        <h4><?php _e('Default map settings',SIGHTINGS_HANDLE) ?>:</h4>
        <span class="howto"><?php _e('The default position and zoom level for the maps on new posts.',SIGHTINGS_HANDLE) ?></span>
        <table id="settings_table" style="margin-bottom: 20px">
            <tr><td><label for="map_lat"><?php _e('Latitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lat" id="map_lat" type="text" size="10" value="<?php echo $settings ? $settings['lat'] : '' ?>"/></td></tr>
            <tr><td><label for="map_lng"><?php _e('Longitude:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_lng" id="map_lng" type="text" size="10" value="<?php echo $settings ? $settings['lng'] : '' ?>"/></td></tr>
            <tr><td><label for="map_zoom"><?php _e('Zoom level:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="map_zoom" id="map_zoom" type="text" size="2" value="<?php echo $settings ? $settings['zoom'] : '' ?>"/></td></tr>
        </table>

        <h4><?php _e('Default display settings',SIGHTINGS_HANDLE); ?>:</h4>
        <span class="howto"><?php _e('Toggles whether the map should be displayed or not by default on new posts.',SIGHTINGS_HANDLE) ?></span>
        <table style="margin-bottom: 20px">
            <tr><td><label for="display"><?php _e('Display sightings maps on posts:',SIGHTINGS_HANDLE) ?></label></td>
                <td><input name="display" id="display" type="checkbox" <?php echo $settings['display'] == 1 ? 'checked="checked"' : '' ?> value="<?php echo $settings['display'] == 1 ? true : false ?>"/></td></tr>
        </table>

        <hr/>
        <h3><?php _e('Contribution settings',SIGHTINGS_HANDLE) ?></h3>
        <h4><?php _e('Author',SIGHTINGS_HANDLE); ?>:</h4>
        <span class="howto"><?php _e('The user that will be set as author on contributor posts.',SIGHTINGS_HANDLE) ?></span>
        <table id="contributions_table" style="margin-bottom: 20px">
            <tr>
                <td>
                    <label for="users"><?php _e('Post as user:',SIGHTINGS_HANDLE) ?></label>
                </td>
                <td>
                    <select id="users">
<?php
                        global $wpdb;
    $query = "SELECT ID, user_nicename from $wpdb->users ORDER BY user_nicename";
    $authors = $wpdb->get_results($query);
    foreach($authors as $author) {
        echo '<option value="'.$author->ID.'">'.$author->user_nicename.'</option>';
    }
    ?>
                    </select>
                </td>
            </tr>
        </table>
        <h4><?php _e('Contributor categories',SIGHTINGS_HANDLE); ?>:</h4>
        <span class="howto"><?php _e('Select the categories you want to be available for contributors.',SIGHTINGS_HANDLE) ?></span>
        <div class="tables_container">
            <table id="available_categories">
                <tr>
                    <th class="cats_header">
                        <?php _e('Available categories',SIGHTINGS_HANDLE) ?>
                    </th>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <select id="sightings_categories">
                        <?php
                            global $wpdb;
                            // Get categories
                            $query = "SELECT term_id, name from $wpdb->terms ORDER BY name";
                            $categories = $wpdb->get_results($query);
                            foreach($categories as $category) {
                                echo '<option value="'.$category->term_id.'">'.$category->name.'</option>';
                            }
                        ?>
                        </select>
                    </td>
                    <td style="padding: 10px 20px;">
                        <input type="button" onclick="addCategory(jQuery('#sightings_categories option:selected'))" value="<?php _e('Add',SIGHTINGS_HANDLE) ?> &raquo;"/>
                    </td>

                </tr>
            </table>
            <table id="contributor_categories">
                <tr>
                    <th class="cats_header">
                        <?php _e('Contributor categories',SIGHTINGS_HANDLE) ?>
                    </th>
                </tr>
                <?php
                isset($settings['contributor_categories']);
                ?>
            </table>
        </div>

        <hr/>

        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">

    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#status_success').delay(3000).slideUp();
        jQuery('#donate_container').delay(5000).slideDown();
    });

    function validateSettings() {
        if(/^[-+]?[0-9]*\.?[0-9]+$/.test(jQuery('#map_lat').val())
                &&
                /^[-+]?[0-9]*\.?[0-9]+$/.test(jQuery('#map_lng').val())
                &&
                /^[-+]?[0-9]*\.?[0-9]+$/.test(jQuery('#map_zoom').val())) {
            return true;
        }
        else
        {
            jQuery('#status_fail').slideDown().delay(6000).slideUp();
            jQuery('#settings_table').css('border','1px solid #f00');
            return false;
        }
    }

    function removeCategory(node) {
        node.parent().parent().fadeOut(function() {
            jQuery(this).remove();
        });
    }

    function addCategory(jQObj) {

        var cat_list = jQuery('#contributor_categories input');
        var is_duplicate = false;
        jQuery.each(cat_list, function() {
            if(jQuery(this).val() == jQObj.val()) {
                is_duplicate = true;
                jQuery(this).parent('td').parent('tr').animate({backgroundColor:'red'}, 'fast', 'linear', function() {
                jQuery(this).animate({
                    backgroundColor: 'white'
                }, 'normal', 'linear', function() {
                    jQuery(this).css({'background':'none', backgroundColor : ''});
                });
                });
            }
        });
        if(! is_duplicate) {
            jQuery('#contributor_categories').append('<tr><td>'+jQObj.text()+'</td><td><a href="#" onclick="removeCategory(jQuery(this)); return false;"> [- <?php _e('Remove', SIGHTINGS_HANDLE) ?>]</a><input type="hidden" name="contributor_categories[]" value="'+jQObj.val()+'" /></td></tr>')
        }
    }

</script>