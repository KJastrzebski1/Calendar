<?php
/*
 *  Calendar option page
 */


add_action('admin_menu', 'alt_calendar_create_menu');

function alt_calendar_create_menu() {
    wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
    wp_enqueue_script("settingsjs", plugins_url('assets/js/alt-calendar-settings.js', __FILE__));
    //create new top-level menu
    add_menu_page('Alt Calendar Settings', 'Alt Calendar', 'administrator', 'alt_calendar', 'alt_calendar_settings_page', 'dashicons-calendar-alt');
    add_submenu_page('alt_calendar', 'Events', 'Events', 'administrator', 'edit.php?post_type=calendar_event');
    add_submenu_page('alt_calendar', 'Calendars', 'Calendars', 'administrator', 'edit-tags.php?taxonomy=alt-calendar');
    //add_options_page('Alt Calendar Settings', 'Alt Calendar', 'administrator', 'alt_calendar','alt_calendar_settings_page' , 'dashicons-calendar-alt' );
    //call register settings function
    add_action('admin_init', 'register_alt_calendar_settings');
}

function register_alt_calendar_settings() {
    //register our settings
    register_setting('alt-calendar-settings-group', 'default_calendar');
}

function alt_calendar_settings_page() {
    ?>
    <div class="wrap">
        <h2>Alt Calendar</h2>

        <form method="post" action="options.php">
            <?php settings_fields('alt-calendar-settings-group'); ?>
            <?php do_settings_sections('alt-calendar-settings-group'); ?>
            <?php
            $users = get_users(array(
                'exclude' => array(1),
                'fields' => 'all'
            ));
            print_r($users[0]->data->user_login);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Chose default calendar</th>
                    <td><select name="default_calendar" >
                            <?php
                            $taxonomies = get_terms('alt-calendar', 'hide_empty=0');
                            foreach ($taxonomies as $tax) {
                                echo '<option value="' . $tax->term_id;
                                if ($tax->term_id == get_option('default_calendar')) {
                                    echo '" selected="selected"';
                                }
                                echo '">' . $tax->name . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Chose user</th>
                    <td>
                        <select id="alt-user-select" name="user">
                            <?php
                            foreach ($users as $user) {
                                echo '<option value="' . $user->data->ID . '" >';
                                echo $user->data->user_login . '</option>';
                            }
                            ?>
                        </select>
                    </td>

                </tr>

            </table>  

            <table id='alt-calendar-table'>

            </table>
            <div id="add-calendar-label">
                <label></label>
                <select id='add-calendar-select' name="add_calendar" >
                    <?php
                    $taxonomies = get_terms('alt-calendar', 'hide_empty=0');
                    foreach ($taxonomies as $tax) {
                        echo '<option value="' . $tax->term_id;
                        echo '">' . $tax->name . '</option>';
                    }
                    ?>
                </select>
                <i id="add-calendar" class="fa fa-plus"></i>
            </div>
            <script>//AltCalendarsTable(1);</script>




            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>