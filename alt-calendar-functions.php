<?php

defined('ABSPATH') or die('No script kiddies please!');

require_once 'alt-calendar-taxonomy.php';


add_action('wp_enqueue_scripts', 'alt_enqueue_scripts');

add_action('plugins_loaded', 'alt_plugin_lang');

function alt_enqueue_scripts() {
    wp_deregister_script('jquery');
    wp_enqueue_style('fullCalendar_lib_css', plugins_url('fullcalendar/fullcalendar.min.css', __FILE__));
    wp_enqueue_style('event_panel', plugins_url('assets/css/event.css', __FILE__));
    wp_enqueue_style('jquery_ui_css', plugins_url('fullcalendar/lib/cupertino/jquery-ui.min.css', __FILE__));
    wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

    wp_enqueue_script('jquery', plugins_url('fullcalendar/lib/jquery.min.js', __FILE__));
    wp_enqueue_script('momentjs', plugins_url('fullcalendar/lib/moment.min.js', __FILE__), ['jquery']);
    wp_enqueue_script('momentjs_tz', 'http://momentjs.com/downloads/moment-timezone.min.js', ['momentjs']);
    wp_enqueue_script('fullCalendar_lib', plugins_url('fullcalendar/fullcalendar.min.js', __FILE__), ['momentjs']);

    wp_enqueue_script('jquery-ui', plugins_url('fullcalendar/lib/jquery-ui.min.js', __FILE__), ['jquery'], false, true);

    global $pagename;
    $args = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'api_key' => 'AIzaSyBg5viJdIm0bBtQW6QVP1U7jx9OLevIUuw',
        'lang' => get_locale()
    );
    if ($pagename == 'alt-calendar') {
        wp_enqueue_script('fullCalendar', plugins_url('assets/js/fullCalendar.js', __FILE__), ['fullCalendar_lib', 'jquery-ui'], false, true);
        wp_localize_script('fullCalendar', 'ajax_object', $args);
    } else {
        wp_enqueue_script('calendar_widget', plugins_url('assets/js/alt-calendar-widget.js', __FILE__), ['fullCalendar_lib', 'jquery-ui']);
        wp_localize_script('calendar_widget', 'ajax_object', $args);
    }
    wp_enqueue_script('lang-all', plugins_url('fullcalendar/lang-all.js', __FILE__), ['fullCalendar_lib']);
    wp_enqueue_script('fc_gcal', plugins_url('fullcalendar/gcal.js', __FILE__), ['fullCalendar_lib']);
}



function alt_plugin_lang() {
    load_plugin_textdomain('alt-calendar', false, dirname(plugin_basename(__FILE__)) . '/lang');
}

