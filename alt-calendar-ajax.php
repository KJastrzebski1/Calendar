<?php
/*
 *  Ajax functions
 */


function delete_event_callback() {
    $post_id = $_POST['data'];
    echo $post_id;
    wp_delete_post($post_id);
    wp_die();
}

function check_admin_callback() {
    if (current_user_can('administrator')) {
        echo '1';
    } else {
        echo '0';
    }
    wp_die();
}
function get_calendars_callback(){
    
}
function get_user_callback() {
    $current_user = wp_get_current_user();
    $id = $current_user->ID;
    $taxonomies = get_terms('alt-calendar');
    $names = [];
    $id = [];
    $admin = 0;
    $t_id = [];
    
    foreach ($taxonomies as $cat){
        $names[] = $cat->name;
        $id[] = $cat->term_id;
    }
    /*if (current_user_can('administrator')) {
        $admin = 1;
        $all_users = get_users(array('search'=> '*'));
        foreach ($all_users as $user){
            $t_id[] = $user->ID;
        }
        $id = $t_id;
    }*/
    $response = [
        "admin" => $admin,
        "id" => $id,
        "names" => $names
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
}

function get_events_callback() {
    $calendar_id = $_POST['data'];
    //echo $calendar_id;
    $the_query = new WP_Query(array(
        'post_type' => 'calendar_event',
        'tax_query' => array(
            array(
                'taxonomy' => 'alt-calendar',
                'field' => 'term_id',
                'terms' => $calendar_id
            //'operator' => 'NOT IN'
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
            //if ($calendar_id == get_post_meta($post_id, 'calendar_id', true)) {
                $response[$i]['ID'] = $post_id;
                $response[$i]['title'] = get_the_title();
                $response[$i]['description'] = get_the_content();
                $response[$i]['start'] = get_post_meta($post_id, 'start', true);
                $response[$i]['end'] = get_post_meta($post_id, 'end', true);

                $i++;
            //}
        }
    } else {
        // no posts found
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
}

function update_event_callback() {
    global $wpdb;
    $event = $_POST["data"];
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    //$calendar_id = 
    $response = [];

    $id = $event['id'];

    $title = $event['title'];
    $start = new DateTime($event['start']);
    $end = new DateTime($event['start']);
    $end->modify("+2 hours");
    $start_date = $start->format('Y-m-d');
    $start_time = $start->format('H:i');
    if ($event['end'] != '') {
        $end = new DateTime($event['end']);
    }

    if ($event['description'] != '') {
        $desc = $event['description'];
    }
    $all_day = $event['allDay'];

    $calendar_event = array(
        'post_title' => $title,
        'post_status' => 'publish',
        'post_content' => $desc . ' ',
        'post_type' => 'calendar_event'
    );
    if (isset($event['post_id'])) {
        $post_id = $event['post_id'];
        $calendar_event['ID'] = $post_id;
    }
    //var_dump($calendar_event);
    $post_id = wp_insert_post($calendar_event);
    if (is_wp_error($post_id)) {
        $errors = $post_id->get_error_messages();
        foreach ($errors as $error) {
            echo $error;
        }
    }
    if ($post_id) {
        $response = $post_id;
        update_post_meta($post_id, 'start', $start);
        update_post_meta($post_id, 'end', $end);
        update_post_meta($post_id, 'event_id', esc_attr($id));
        update_post_meta($post_id, 'user_id', $user_id);
        
    }

    //header('Content-Type: application/json');
    echo $response;

    wp_die();
}