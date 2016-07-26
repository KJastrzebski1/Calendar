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

class AltCal extends Plugin {

    protected static $modules = [
        'PluginManager' => '',
        'User' => '',
        'Widget' => '',
        'Event' => '',
        'Calendar' => '',
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
        $eventsMetaBox = new MetaBox(Event::getInstance());

        parent::init();
    }

    public static function activate() {
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

            $event_id = Event::newPost('Example Event', 'Simple Description');

            $start = new DateTime(current_time('Y-m-d H:i'));
            $end = new DateTime(current_time('Y-m-d H:i'));
            $end->modify('+2 hours');
            update_post_meta($event_id, 'start', $start);
            update_post_meta($event_id, 'end', $end);
            update_option('styling', 0);

            wp_set_object_terms($event_id, 'Example Calendar', 'alt-calendar', true);
            
        }
        $term_id = Calendar::insertTerm('Example Calendar');
        update_option('default_calendar', $term_id);
    }

    public static function deactivate() {
        $page = get_page_by_title('Alt Calendar');
        wp_delete_post($page->ID, true);
        $event = get_page_by_title('Example Event');
        wp_delete_post($event->ID, true);

        flush_rewrite_rules();
    }

    public static function uninstall() {
        global $wpdb;
        $users = get_users(array(
            'fields' => 'all'
        ));
        $query = new WP_Query(array('post_type' => 'calendar_event'));
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_object_term_relationships($query->post->ID, 'alt-calendar');
                wp_delete_post($query->post->ID);
            }
        }
        foreach (array('alt-calendar') as $taxonomy) {
            // Prepare & excecute SQL
            $terms = $wpdb->get_results($wpdb->prepare("SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy));

            // Delete Terms
            if ($terms) {
                foreach ($terms as $term) {
                    $wpdb->delete($wpdb->term_taxonomy, array('term_taxonomy_id' => $term->term_taxonomy_id));
                    $wpdb->delete($wpdb->terms, array('term_id' => $term->term_id));
                }
            }
            // Delete Taxonomy
            $wpdb->delete($wpdb->term_taxonomy, array('taxonomy' => $taxonomy), array('%s'));
        }
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
