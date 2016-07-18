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
/*
  register_activation_hook(__FILE__, array("AltCal", "activate"));
  register_deactivation_hook(__FILE__, array("AltCal", "deactivate"));
  register_uninstall_hook(__FILE__, array("AltCal", "uninstall"));
 */

require_once 'alt-calendar-functions.php';
require_once 'autoloader.php';
/*
  include_once 'include/User.php';
  include_once 'include/Widget.php';
  include_once 'include/Event.php';
  include_once 'include/Calendar.php';
 */
include_once 'module/Settings.php';
include_once 'module/MetaBox.php';

use Gloves\Plugin;

use Module\MetaBox;
use Module\Event;

class AltCal extends Plugin {

    protected $modules = [
        'User' => '',
        'Widget' => '',
        'Calendar' => '',
        'Event' => '',
        'Settings' => 'views/settings'
    ];

    //public static function init() {
    // }
    public function __construct() {
        parent::__construct();
        $eventPostType = Event::getInstance();//new PostType('calendar_event', 'event', 'events');
        $eventsMetaBox = new MetaBox($eventPostType);
    }

    public static function activate() {
        //User::init();
        //Widget::init();
        //Calendar::init();
        //Event::init();
        //$settings = new Settings('views/settings');


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

            $example_event = array(
                'post_title' => 'Example Event',
                'post_status' => 'publish',
                'post_content' => 'Simple description',
                'post_type' => 'calendar_event'
            );
            $event_id = wp_insert_post($example_event);

            $start = new DateTime(current_time('Y-m-d H:i'));
            $end = new DateTime(current_time('Y-m-d H:i'));
            $end->modify('+2 hours');
            update_post_meta($event_id, 'start', $start);
            update_post_meta($event_id, 'end', $end);
            update_option('styling', 0);

            if (!taxonomy_exists('alt-calendar')) {
                add_new_calendar();
            }
            wp_set_object_terms($event_id, 'Example Calendar', 'alt-calendar', true);
        }
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
        unregister_setting('alt-calendar-settings-group', 'default_calendar');
        unregister_setting('alt-calendar-settings-group', 'styling');
        unregister_setting('alt-calendar-settings-group', 'installed');
        delete_option('installed');
        delete_option('default_calendar');
        delete_option('styling');
    }

}

$instance = new AltCal();
