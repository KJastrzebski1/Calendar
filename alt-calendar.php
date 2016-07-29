<?php

/*
 * Plugin Name: Alt Calendar
 * Description: The best wordpress calendar you've ever seen. :)
 * Author: Krzysztof Jastrzebski
 * Text Domain: alt-calendar
 * Domain Path: /lang
 * Version: 1.0.3
 * License: GPL3
 * 
 */

require_once 'alt-calendar-functions.php';
require_once 'autoloader.php';
include_once 'module/MetaBox.php';

use Gloves\Plugin;
use Gloves\PluginMenu;
use Module\MetaBox;
use Module\Event;
use Module\Calendar;

use Model\Table;

class AltCal extends Plugin {

    protected static $modules = [
        'PluginManager' => '',
        'User' => '',
        'Widget' => '',
        'Event' => '',
        'Calendar' => '',
    ];
    protected static $models = [
        'Table',
    ];
    protected static $settings = [
        'default_calendar',
        'styling',
        'installed'
    ];

    public static function init() {
        PluginMenu::addPage('Events', 'edit.php?post_type=calendar_event');
        PluginMenu::addPage('Calendars', 'edit-tags.php?taxonomy=alt-calendar&post_type=calendar_event');
        PluginMenu::init('settings');
        parent::init();
        $eventsMetaBox = new MetaBox(Event::getInstance());
        
    }

    public static function activate() {
        parent::activate();
        $calendar_page = array(
            'post_title' => 'Alt Calendar',
            'post_status' => 'publish',
            'post_content' =>
            '<div id="calendar"></div><div id="dialog"></div>'
            ,
            'post_type' => 'page'
        );
        wp_insert_post($calendar_page);
        $installed = get_option('installed');
        if (!$installed) {
            update_option('installed', 1);

            $event_id = Event::insert('Example Event', 'Simple Description');

            $start = new DateTime(current_time('Y-m-d H:i'));
            $end = new DateTime(current_time('Y-m-d H:i'));
            $end->modify('+2 hours');
            update_post_meta($event_id, 'start', $start);
            update_post_meta($event_id, 'end', $end);
            update_option('styling', 0);

            wp_set_object_terms($event_id, 'Example Calendar', 'alt-calendar', true);
        }
    }

    public static function deactivate() {
        parent::deactivate();
        $page = get_page_by_title('Alt Calendar');
        wp_delete_post($page->ID, true);
        $event = get_page_by_title('Example Event');
        wp_delete_post($event->ID, true);

        flush_rewrite_rules();
    }

    public static function uninstall() {
        parent::uninstall();
        global $wpdb;
        $users = get_users(array(
            'fields' => 'all'
        ));

        foreach ($users as $user) {
            $user_id = $user->data->ID;
            delete_user_meta($user_id, 'user_alt_calendars');
        }
        unregister_setting('alt-calendar-settings', 'default_calendar');
        unregister_setting('alt-calendar-settings', 'styling');
        unregister_setting('alt-calendar-settings', 'installed');
        delete_option('installed');
        delete_option('default_calendar');
        delete_option('styling');
    }

}

AltCal::init();
