<?php

/*
  Plugin Name: Alt Calendar
  Description: The best wordpress calendar you've ever seen. :)
  Author: Krzysztof Jastrzebski
 * Text Domain: alt-calendar
 * Domain Path: /lang
 * Version: 1.0
 */


defined('ABSPATH') or die('No script kiddies please!');

require_once 'class.alt-calendar-widget.php';
require_once 'alt-calendar-ajax.php';
require_once 'alt-calendar-settings.php';
require_once 'alt-calendar-event.php';
require_once 'alt-calendar-taxonomy.php';
require_once 'alt-calendar-metabox.php';

class Alt_Calendar {

    private $calendar_id;

    public function get() {
        return $this->calendar_id;
    }

    public function set($x) {
        $this->calendar_id = $x;
    }

}

function alt_enqueue_scripts() {

    wp_enqueue_style('fullCalendar_lib_css', plugins_url('fullcalendar/fullcalendar.min.css', __FILE__));
    wp_enqueue_style('event_panel', plugins_url('assets/css/event.css', __FILE__));
    wp_enqueue_script('jQuery_lib', plugins_url('fullcalendar/lib/jquery.min.js', __FILE__));
    wp_enqueue_script('momentjs', plugins_url('fullcalendar/lib/moment.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_script('momentjs_tz', 'http://momentjs.com/downloads/moment-timezone.min.js', ['momentjs']);
    wp_enqueue_script('fullCalendar_lib', plugins_url('fullcalendar/fullcalendar.min.js', __FILE__), ['momentjs']);
    wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
    wp_enqueue_script('jquery-ui', plugins_url('fullcalendar/lib/jquery-ui.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_style('jquery_ui_css', plugins_url('fullcalendar/lib/cupertino/jquery-ui.min.css', __FILE__));
    global $pagename;

    if ($pagename == 'alt-calendar') {
        wp_enqueue_script('fullCalendar', plugins_url('assets/js/fullCalendar.js', __FILE__), ['fullCalendar_lib', 'jquery-ui']);
        wp_localize_script('fullCalendar', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_key' => 'AIzaSyBg5viJdIm0bBtQW6QVP1U7jx9OLevIUuw',
            'lang' => get_locale()
        ));
    } else {
        wp_enqueue_script('calendar_widget', plugins_url('assets/js/alt-calendar-widget.js', __FILE__), ['fullCalendar_lib', 'jquery-ui']);
        wp_localize_script('calendar_widget', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_key' => 'AIzaSyBg5viJdIm0bBtQW6QVP1U7jx9OLevIUuw',
            'lang' => get_locale()
        ));
    }
    wp_enqueue_script('lang-all', plugins_url('fullcalendar/lang-all.js', __FILE__), ['fullCalendar_lib']);
    wp_enqueue_script('fc_gcal', plugins_url('fullcalendar/gcal.js', __FILE__), ['fullCalendar_lib']);
}

add_action('wp_enqueue_scripts', 'alt_enqueue_scripts');

add_action('widgets_init', function() {
    register_widget('Alt_Widget'); // class widget name
});

function alt_plugin_lang() {
    load_plugin_textdomain('alt-calendar', false, dirname(plugin_basename(__FILE__)) . '/lang');
}

add_action('plugins_loaded', 'alt_plugin_lang');

function alt_plugin_activate() {

    $calendar_page = array(
        'post_title' => 'Alt Calendar',
        'post_status' => 'publish',
        'post_content' =>
        '<div id="calendar"></div><div id="dialog"></div>'
        ,
        'post_type' => 'page'
    );
    wp_insert_post($calendar_page);
}

function alt_plugin_deactivate() {
    $page = get_page_by_title('Alt Calendar');
    wp_delete_post($page->ID, true);
    
    flush_rewrite_rules();
}

function alt_plugin_user_delete($user_id) {
    delete_user_meta($user_id, 'user_alt_calendars');
}

function alt_plugin_uninstall() {
    
    $users = get_users(array(
        'fields' => 'all'
    ));
    $query = new WP_Query(array('post_type' => 'calendar_event'));
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wp_delete_post($query->post->ID);
        }
    }
    $terms = get_terms('alt-calendar', 'hide_empty=0');
    foreach($terms as $term){
        wp_delete_term($term->term_id, 'alt-calendar');
    }
    foreach ($users as $user) {
        $user_id = $user->data->ID;
        delete_user_meta($user_id, 'user_alt_calendars');
    }
    unregister_setting('alt-calendar-settings-group', 'default_calendar');
    unregister_setting('alt-calendar-settings-group', 'styling');
    delete_option('default_calendar');
    delete_option('styling');
}

add_action('delete_user', 'alt_plugin_user_delete');

register_activation_hook(__FILE__, 'alt_plugin_activate');
register_deactivation_hook(__FILE__, 'alt_plugin_deactivate');
register_uninstall_hook(__FILE__, 'alt_plugin_uninstall');
add_action('init', 'alt_event_init');

// AJAX events handler
// defintions in alt-calendar-ajax.php

add_action('wp_ajax_update_event', 'update_event_callback');
//add_action('wp_ajax_nopriv_update_event', 'update_event_callback');
add_action('wp_ajax_get_events', 'get_events_callback');
add_action('wp_ajax_nopriv_get_events', 'get_events_callback');

add_action('wp_ajax_check_admin', 'check_admin_callback');
add_action('wp_ajax_nopriv_check_admin', 'check_admin_callback');

add_action('wp_ajax_delete_event', 'delete_event_callback');

add_action('wp_ajax_get_user', 'get_user_callback');
add_action('wp_ajax_nopriv_get_user', 'get_user_callback');

add_action('wp_ajax_new_calendar', 'new_calendar_callback');

add_action('wp_ajax_remove_calendar', 'remove_calendar_callback');

add_action('wp_ajax_add_calendar', 'add_calendar_callback');

add_action('wp_ajax_dialog_content', 'dialog_content_callback');

add_action('add_meta_boxes', 'alt_meta_box_add');
add_action('save_post', 'alt_meta_box_save');

function alt_meta_box_add() {
    add_meta_box('my-meta-box-id', 'Event data', 'alt_meta_box_cb', 'calendar_event', 'normal', 'high');
}

add_action("delete_alt-calendar", 'remove_calendar_from_users');

function remove_calendar_from_users($Term_ID) {
    $users = get_users(array(
        'exclude' => array(1),
        'fields' => 'all'
    ));
    $user_meta = 'user_alt_calendars';
    foreach ($users as $user) {
        $user_id = $user->data->ID;
        $calendars = get_user_option($user_meta, $user_id);
        $key = array_search(intval($Term_ID), $calendars);
        if ($key != NULL) {
            unset($calendars[$key]);
            delete_user_meta($user_id, $user_meta);
            add_user_meta($user_id, $user_meta, $calendars);
        }
    }
}
