<?php

namespace AltCalendar;

class User {

    public static function init() {
        add_action('wp_ajax_get_user', array('AltCalendar\User', 'getUser'));
        add_action('wp_ajax_nopriv_get_user', array('AltCalendar\User', 'getUser'));
    }

    /*
     * Returns users calendars
     * @param int. Optional userID
     * @return json:
     * $response = [
      "admin" - is admin
      "id" - ids of calendars
      "names" - names of calendars
      "logged_in" - is user looged in
      "styling" - styling settings
      ];
     */

    public static function getUser() {
        if (isset($_POST['data'])) {
            $user_id = intval($_POST['data']);
            $current_user = get_user_by('id', $user_id);
        } else {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
        }
        if ($user_id == 0) {
            $logged_in = false;
        } else {
            $logged_in = true;
        }

        $calendars = [];
        $taxonomies = get_terms('alt-calendar', array(
            'hide_empty' => 0
        ));
        $names = [];
        $id = [];
        $admin = 0;
        $term_id = get_option('default_calendar');
        $default = get_term_by('term_id', $term_id, 'alt-calendar');
        $names[0] = $default->name;
        $id[0] = $default->term_id;
        if ($user_id) {
            if (isset($current_user->caps['administrator'])) {
                $admin = 1;
                foreach ($taxonomies as $cal) {
                    $calendars[] = $cal->term_id;
                }
            } else {
                $calendars = get_user_option('user_alt_calendars', $user_id);
            }
            if ($calendars) {
                foreach ($calendars as $value) {
                    $cat = get_term_by('term_id', $value, 'alt-calendar');
                    if ($cat->term_id !== $default->term_id) {
                        $names[] = $cat->name;
                        $id[] = $cat->term_id;
                    }
                }
            }
        }
        if (get_option('styling')) {
            $styling = true;
        } else {
            $styling = false;
        }
        $response = [
            "admin" => $admin,
            "id" => $id,
            "names" => $names,
            "logged_in" => $logged_in,
            "styling" => $styling
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        wp_die();
    }

}
