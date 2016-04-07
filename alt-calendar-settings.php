<?php
/*
 *  Calendar option page
 */


add_action('admin_menu', 'alt_calendar_create_menu');

function alt_calendar_create_menu() {

	//create new top-level menu
	add_menu_page('Alt Calendar Settings', 'Alt Calendar', 'administrator', 'alt_calendar', 'alt_calendar_settings_page' , 'dashicons-calendar-alt' );
        add_submenu_page('alt_calendar', 'Events', 'Events', 'administrator', 'edit.php?post_type=calendar_event');
        add_submenu_page('alt_calendar', 'Calendars', 'Calendars', 'administrator', 'edit-tags.php?taxonomy=alt-calendar');
        //add_options_page('Alt Calendar Settings', 'Alt Calendar', 'administrator', 'alt_calendar','alt_calendar_settings_page' , 'dashicons-calendar-alt' );
	//call register settings function
	add_action( 'admin_init', 'register_alt_calendar_settings' );
}


function register_alt_calendar_settings() {
	//register our settings
	register_setting( 'alt-calendar-settings-group', 'new_option_name' );
	register_setting( 'alt-calendar-settings-group', 'some_other_option' );
	register_setting( 'alt-calendar-settings-group', 'option_etc' );
}

function alt_calendar_settings_page() {
?>
<div class="wrap">
<h2>Alt Calendar</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'alt-calendar-settings-group' ); ?>
    <?php do_settings_sections( 'alt-calendar-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New Option Name</th>
        <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Some Other Option</th>
        <td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('some_other_option') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Options, Etc.</th>
        <td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('option_etc') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>