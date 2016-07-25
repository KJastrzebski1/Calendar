<?php

namespace Module;

class Settings{

    protected static $viewDir;

    public function __construct($view) {
        
        
    }
    
    public static function init($view){
        static::$viewDir = $view;
        
        add_action('admin_menu', array('\Module\Settings', 'alt_calendar_create_menu'));
    }

    public static function alt_calendar_create_menu() {
        wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
        wp_enqueue_style('settings_css', plugins_url('/../assets/css/settings.css', __FILE__));
        wp_enqueue_script("settings_js", plugins_url('/../assets/js/alt-calendar-settings.js', __FILE__));
        wp_localize_script("settings_js", 'alt_var', array(
            'name' => __('Name', 'alt-calendar')
        ));
        //create new top-level menu
        add_menu_page('Alt Calendar Settings', 'Alt Calendar', 'administrator', 'alt-calendar', array('\Module\Settings', 'alt_calendar_settings_page'), 'dashicons-calendar-alt');
        add_submenu_page('alt-calendar', 'Events', 'Events', 'administrator', 'edit.php?post_type=calendar_event');
        add_submenu_page('alt-calendar', 'Calendars', 'Calendars', 'administrator', 'edit-tags.php?taxonomy=alt-calendar&post_type=calendar_event');

        //call register settings function
        add_action('admin_init', array('\Module\Settings', 'register_alt_calendar_settings'));
    }

    public static function register_alt_calendar_settings() {

        //register our settings
        register_setting('alt-calendar-settings-group', 'default_calendar');
        register_setting('alt-calendar-settings-group', 'styling');
        register_setting('alt-calendar-settings-group', 'installed');
        $term_id = wp_insert_term('Example Calendar', 'alt-calendar');

        if (is_wp_error($term_id)) {
            $term_id = get_term_by('name', 'Example Calendar', 'alt-calendar');
            update_option('default_calendar', $term_id->term_id);
        } else {
            update_option('default_calendar', $term_id['term_id']);
        }
    }

    public static function alt_calendar_settings_page() {
        include '/../'. static::$viewDir .'.php';
    }

}
