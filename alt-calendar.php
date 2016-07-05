<?php

/*
 * Plugin Name: Alt Calendar
 * Description: The best wordpress calendar you've ever seen. :)
 * Author: Krzysztof Jastrzebski
 * Text Domain: alt-calendar
 * Domain Path: /lang
 * Version: 1.0.2
 * License: GPL3
 * 
 */

register_activation_hook(__FILE__, array("AltCalendar", "activate"));
register_deactivation_hook(__FILE__, array("AltCalendar", "deactivate"));
register_uninstall_hook(__FILE__, array("AltCalendar", "uninstall"));

require_once 'alt-calendar-functions.php';
include_once 'include/User.php';


AltCalendar::init();

class AltCalendar {

    public static function init() {
        //User::init();
        //add_action('wp_ajax_get_user', 'get_user_callback');
        //add_action('wp_ajax_nopriv_get_user', 'get_user_callback');
        add_action('wp_ajax_get_user', array('User', 'get_user_callback'));
        add_action('wp_ajax_nopriv_get_user', array('User', 'get_user_callback'));
        
        add_action('wp_enqueue_scripts', 'alt_enqueue_scripts');
        add_action('widgets_init', function() {
            register_widget('Alt_Widget'); // class widget name
        });
        add_action('plugins_loaded', 'alt_plugin_lang');

        //AJAX
        add_action('wp_ajax_update_event', 'update_event_callback');
        //add_action('wp_ajax_nopriv_update_event', 'update_event_callback');
        add_action('wp_ajax_get_events', 'get_events_callback');
        add_action('wp_ajax_nopriv_get_events', 'get_events_callback');

        add_action('wp_ajax_delete_event', 'delete_event_callback');

        add_action('wp_ajax_new_calendar', 'new_calendar_callback');

        add_action('wp_ajax_remove_calendar', 'remove_calendar_callback');

        add_action('wp_ajax_add_calendar', 'add_calendar_callback');

        add_action('wp_ajax_dialog_content', 'dialog_content_callback');

        add_action('add_meta_boxes', 'alt_meta_box_add');
        add_action('save_post', 'alt_meta_box_save');

        add_action("delete_alt-calendar", 'remove_calendar_from_users');

        add_action('delete_user', 'alt_plugin_user_delete');
        add_action('init', 'alt_event_init');
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
