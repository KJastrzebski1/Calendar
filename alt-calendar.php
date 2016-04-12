<?php
/*
  Plugin Name: Alt Calendar
  Description: The best wordpress calendar u've ever seen. :)
  Author: Krzysztof Jastrzebski
 * Version: 0.5.1
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
    //wp_enqueue_style('fullCalendar_print_css', plugins_url('fullcalendar/fullcalendar.print.css', __FILE__));
    wp_enqueue_style('fullCalendar_lib_css', plugins_url('fullcalendar/fullcalendar.min.css', __FILE__));
    wp_enqueue_style('event_panel', plugins_url('assets/css/event.css', __FILE__));
    wp_enqueue_script('jQuery_lib', plugins_url('fullcalendar/lib/jquery.min.js', __FILE__));
    wp_enqueue_script('momentjs', plugins_url('fullcalendar/lib/moment.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_script('momentjs_tz', 'http://momentjs.com/downloads/moment-timezone.min.js', ['momentjs']);
    //wp_enqueue_script('momentjs-timezone', 'http://momentjs.com/downloads/moment-timezone-with-data-2010-2020.min.js', ['momentjs_tz']);
    wp_enqueue_script('fullCalendar_lib', plugins_url('fullcalendar/fullcalendar.min.js', __FILE__), ['momentjs']);
    wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
    wp_enqueue_script('jquery-ui', plugins_url('fullcalendar/lib/jquery-ui.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_style('jquery_ui_css', plugins_url('fullcalendar/lib/cupertino/jquery-ui.min.css', __FILE__));
    wp_enqueue_script('fullCalendar', plugins_url('assets/js/fullCalendar.js', __FILE__), ['fullCalendar_lib', 'jquery-ui']);
    
    wp_enqueue_script('fc_gcal', plugins_url('fullcalendar/gcal.js', __FILE__),['fullCalendar_lib']);
    wp_localize_script('fullCalendar', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'data' => 1));
}

add_action('wp_enqueue_scripts', 'alt_enqueue_scripts');

add_action('widgets_init', function() {
    register_widget('Alt_Widget'); // class widget name
});

function alt_plugin_setup() {
    $calendar_page = array(
        'post_title' => 'Alt Calendar',
        'post_status' => 'publish',
        'post_content' => '<div id="calendar"></div><div id="dialog"><form>
        <p>
        <label>Start</label>
        <input type="date" id="my_meta_box_ds" name="date_start" value="" /><input type="time" id="my_meta_box_ts" name="time_start" value="" />
        </p>
        <p>
        <label>End</label>
        <input type="date" id="my_meta_box_de" name="date_end" value="" /><input type="time" id="my_meta_box_te" name="time_end" value="" />
        </p>
        <label>Description</label>
        <textarea rows="3" cols="40" id="my_meta_box_desc" name="my_meta_box_desc"></textarea>
        </form>
        
        </div>'
        ,
        'post_type' => 'page'
    );

    wp_insert_post($calendar_page);
}

function alt_plugin_delete() {
    $page = get_page_by_title('Alt Calendar');

    wp_delete_post($page->ID, true);

    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'alt_plugin_setup');
register_deactivation_hook(__FILE__, 'alt_plugin_delete');
add_action('init', 'alt_event_init');



// event meta box = = = = == = = = = == = 
// AJAX events handler
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


add_action('add_meta_boxes', 'cd_meta_box_add');
add_action('save_post', 'cd_meta_box_save');
function cd_meta_box_add() {
    add_meta_box('my-meta-box-id', 'Event data', 'cd_meta_box_cb', 'calendar_event', 'normal', 'high');
}

