<?php

register_activation_hook(__FILE__, array("AltCalendar", "activate"));
register_deactivation_hook(__FILE__, array("AltCalendar", "deactivate"));
register_uninstall_hook(__FILE__, array("AltCalendar", "uninstall"));

class AltCalendar {

    public function __construct() {
        
    }

    public function activate() {

        $calendar_page = array(
            'post_title' => 'Alt Calendar',
            'post_status' => 'publish',
            'post_content' =>
            '<div id="calendar"></div><div id="dialog"></div>'
            ,
            'post_type' => 'page'
        );

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
            wp_insert_post($calendar_page);
        }
    }

    public function deactivate() {
        $page = get_page_by_title('Alt Calendar');
        wp_delete_post($page->ID, true);
        $event = get_page_by_title('Example Event');
        wp_delete_post($event->ID, true);

        flush_rewrite_rules();
    }

    public function uninstall() {
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
