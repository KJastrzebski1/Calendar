<?php
namespace Module;

class PluginManager extends \Gloves\ScriptsManager{

    public static function loadAdmin() {
        wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
        wp_enqueue_style('settings_css', plugins_url('/../assets/css/settings.css', __FILE__));
        wp_enqueue_script("settings_js", plugins_url('/../assets/js/alt-calendar-settings.js', __FILE__));
        wp_localize_script("settings_js", 'alt_var', array(
            'name' => __('Name', 'alt-calendar')
        ));
    }

    public static function load() {
        
        wp_deregister_script('jquery');
        wp_enqueue_style('fullCalendar_lib_css', static::getAssetsUrl('fullcalendar/fullcalendar.min.css', __FILE__));
        wp_enqueue_style('event_panel', static::getAssetsUrl('css/event.css', __FILE__));
        wp_enqueue_style('jquery_ui_css', static::getAssetsUrl('fullcalendar/lib/cupertino/jquery-ui.min.css', __FILE__));
        wp_enqueue_style('awesomefonts', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

        wp_enqueue_script('jquery', static::getAssetsUrl('fullcalendar/lib/jquery.min.js', __FILE__));
        wp_enqueue_script('momentjs', static::getAssetsUrl('fullcalendar/lib/moment.min.js', __FILE__), ['jquery']);
        wp_enqueue_script('momentjs_tz', 'http://momentjs.com/downloads/moment-timezone.min.js', ['momentjs']);
        wp_enqueue_script('fullCalendar_lib', static::getAssetsUrl('fullcalendar/fullcalendar.min.js', __FILE__), ['momentjs']);

        wp_enqueue_script('jquery-ui', static::getAssetsUrl('fullcalendar/lib/jquery-ui.min.js', __FILE__), ['jquery'], false, true);

        global $pagename;
        $args = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_key' => 'AIzaSyBg5viJdIm0bBtQW6QVP1U7jx9OLevIUuw',
            'lang' => get_locale()
        );
        if ($pagename == 'alt-calendar') {
            wp_enqueue_script('fullCalendar', static::getAssetsUrl('js/fullCalendar.js', __FILE__), ['fullCalendar_lib', 'jquery-ui'], false, true);
            wp_localize_script('fullCalendar', 'ajax_object', $args);
        } else {
            wp_enqueue_script('calendar_widget', static::getAssetsUrl('js/alt-calendar-widget.js', __FILE__), ['fullCalendar_lib', 'jquery-ui']);
            wp_localize_script('calendar_widget', 'ajax_object', $args);
        }
        wp_enqueue_script('lang-all', static::getAssetsUrl('fullcalendar/lang-all.js', __FILE__), ['fullCalendar_lib']);
        wp_enqueue_script('fc_gcal', static::getAssetsUrl('fullcalendar/gcal.js', __FILE__), ['fullCalendar_lib']);
    }
    
    
}
