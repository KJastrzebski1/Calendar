<?php

namespace Module;

use \Gloves\Taxonomy;

class Calendar extends Taxonomy {

    

    public static function init() {
        static::setup('alt-calendar', 'calendar', 'calendars', 'calendar_event');
        register_activation_hook(__FILE__, array('Module\Calendar', 'activate'));
        add_action('wp_ajax_get_events', array('Module\Calendar', 'getEvents'));
        add_action('wp_ajax_nopriv_get_events', array('Module\Calendar', 'getEvents'));

        add_action('wp_ajax_dialog_content', array('Module\Calendar', 'dialog'));

        add_action('wp_ajax_new_calendar', array('Module\Calendar', 'newCalendar'));

        add_action("delete_alt-calendar", array('Module\Calendar', 'delete'));
    }
    
    public static function activate(){
       
    }
    public static function activate_once(){
        $instance = static::getInstance();
       $instance->register();
       $term_id = static::insert('Example Calendar');
       $event_id = Event::getBy("name", "Example Event");
       \wp_set_object_terms($event_id[0]->ID, array(intval($term_id)), 'alt-calendar', true);
       update_option('default_calendar', $term_id);
    }

    public static function uninstall() {
        
    }
    
    public static function deactivate() {
        global $wpdb;
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
    }

    

    /*
     * returns events of calendar
     * 
     * @param int. calendarID
     * @return json:
     * events[] = [
     *      title
     *      description
     *      start
     *      end
     *      ID
     * ]
     */

    public static function getEvents() {
        $calendar_id = intval($_POST['data']);

        $google_id = get_term_meta($calendar_id, 'google_id', true);
        if ($google_id) {
            echo $google_id;
            wp_die();
            return;
        }

        $the_query = new \WP_Query(array(
            'post_type' => 'calendar_event',
            'tax_query' => array(
                array(
                    'taxonomy' => 'alt-calendar',
                    'field' => 'term_id',
                    'terms' => $calendar_id
                )
            )
        ));

        $response = [];

// The Loop
        if ($the_query->have_posts()) {
            $i = 0;
            while ($the_query->have_posts()) {

                $the_query->the_post();
                $post_id = get_the_ID();

                $response[$i]['ID'] = $post_id;
                $response[$i]['title'] = get_the_title();
                $response[$i]['description'] = get_the_content();
                $response[$i]['start'] = get_post_meta($post_id, 'start', true);
                $response[$i]['end'] = get_post_meta($post_id, 'end', true);
                $i++;
            }
        } else {
            
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        wp_die();
    }

    /*
     * Creates new calendar
     * 
     * @param array. Contains title and optional GoogleID
     * @return array. Contains term_id of new calendar and googleID if added
     */

    public static function newCalendar() {
        $data = $_POST['data'];
        $title = sanitize_text_field($data['title']);
        $google_id = intval($data['google_id']);
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_meta = 'user_alt_calendars';
        $calendars = get_user_option($user_meta, $user_id);
        delete_user_meta($user_id, $user_meta);
        $response = wp_insert_term($title, 'alt-calendar');
        if (!$calendars) {
            $calendars[0] = $response['term_id'];
        } else {
            array_push($calendars, $response['term_id']);
        }

        add_user_meta($user_id, 'user_alt_calendars', $calendars);
        add_term_meta($response['term_id'], 'google_id', $google_id);
        $response['google_id'] = $google_id;
        header('Content-Type: application/json');
        echo json_encode($response);
        wp_die();
    }

    public static function dialog() {
        \Gloves\Render::view('dialog');
        wp_die();
    }

    public static function delete($Term_ID) {
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

}
