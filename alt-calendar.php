<?php
/*
  Plugin Name: Alt Calendar
  Description: The best wordpress calendar u've ever seen. :)
  Author: Krzysztof Jastrzebski
 * Version: 0.0.1
 */


defined('ABSPATH') or die('No script kiddies please!');

require_once 'class.alt-calendar-widget.php';

class Alt_Calendar {

    private $calendar_id;

    public function get() {
        return $this->calendar_id;
    }

    public function set($x) {
        $this->calendar_id = $x;
    }

}

add_action('widgets_init', function() {
    register_widget('Alt_Widget'); // class widget name
});

function alt_plugin_setup() {
    $calendar_page = array(
        'post_title' => 'Alt Calendar',
        'post_status' => 'publish',
        'post_content' => '<div id="calendar"></div>',
        'post_type' => 'page'
    );
    wp_insert_post($calendar_page);
}

function alt_plugin_delete() {
    $page = get_page_by_title('Alt Calendar');

    wp_delete_post($page->ID, true);

    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'alt_plugin_setup');
register_deactivation_hook(__FILE__, 'alt_plugin_delete');

function alt_event_init() {
    $labels = array(
        'name' => _x('Events', 'post type general name', 'your-plugin-textdomain'),
        'singular_name' => _x('Event', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name' => _x('Events', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar' => _x('Event', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new' => _x('Add New', 'event', 'your-plugin-textdomain'),
        'add_new_item' => __('Add New Event', 'your-plugin-textdomain'),
        'new_item' => __('New Event', 'your-plugin-textdomain'),
        'edit_item' => __('Edit Event', 'your-plugin-textdomain'),
        'view_item' => __('View Event', 'your-plugin-textdomain'),
        'all_items' => __('All Events', 'your-plugin-textdomain'),
        'search_items' => __('Search Events', 'your-plugin-textdomain'),
        'parent_item_colon' => __('Parent Events:', 'your-plugin-textdomain'),
        'not_found' => __('No events found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No events found in Trash.', 'your-plugin-textdomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'your-plugin-textdomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'event'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 105,
        'supports' => array('title', 'editor')
    );

    register_post_type('calendar_event', $args);
}

add_action('init', 'alt_event_init');

function alt_enqueue_scripts() {
    //wp_enqueue_style('fullCalendar_print_css', plugins_url('fullcalendar/fullcalendar.print.css', __FILE__));
    wp_enqueue_style('fullCalendar_lib_css', plugins_url('fullcalendar/fullcalendar.min.css', __FILE__));

    wp_enqueue_script('jQuery_lib', plugins_url('fullcalendar/lib/jquery.min.js', __FILE__));
    wp_enqueue_script('momentjs', plugins_url('fullcalendar/lib/moment.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_script('fullCalendar_lib', plugins_url('fullcalendar/fullcalendar.min.js', __FILE__), ['momentjs']);

    wp_enqueue_script('jquery-ui', plugins_url('fullcalendar/lib/jquery-ui.custom.min.js', __FILE__), ['jQuery_lib']);
    wp_enqueue_style('jquery_ui_css', plugins_url('fullcalendar/lib/cupertino/jquery-ui.min.css', __FILE__));
    wp_enqueue_script('fullCalendar', plugins_url('assets/js/fullCalendar.js', __FILE__), ['fullCalendar_lib']);
    wp_localize_script('fullCalendar', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'data' => 1));
}

add_action('wp_enqueue_scripts', 'alt_enqueue_scripts');

function alt_event_meta() {
    wp_enqueue_style('event_panel', plugins_url('assets/css/event.css', __FILE__));

    add_meta_box('event_meta', 'Event Information', 'event_meta', 'events', 'advanced', 'high'
    );
}

/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes() {
    add_meta_box('event-meta-box', __('Event Meta', 'textdomain'), 'wpdocs_my_display_callback', 'calendar_event');
}

add_action('add_meta_boxes', 'wpdocs_register_meta_boxes');

function wpdocs_my_display_callback($post, $metabox) {
    ?>
    <form>
        <?php
        //echo $post->post_modified; 
        wp_nonce_field('my_custom_box', 'my_custom_box_nonce');

        $values = get_post_meta($post->ID, 'event_meta_box', true);
        var_dump($values);
        ?>
        <br>
        Start: <input type="date" value=""/> <input type="time" /><br>
        End:   <input type="date" /> <input type="time" /><br>
        All Day: <input type="checkbox" value="allDay" />
    </form>
    <?php
}

function wpdocs_save_meta_box($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['my_custom_box_nonce'])) {
        return $post_id;
    }
    $nonce = $_POST['my_custom_box_nonce'];
    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'my_custom_box')) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if ('page' == $_POST['calendar_event']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }
    $accepted_fields['calendar-event'] = array(
        'data_begin',
        'data_end',
        'allDay'
    );
    $post_type_id = $_POST['post_type'];
    
    foreach ($accepted_fields[$post_type_id] as $key) {
        // Set it to a variable, so it's
        // easier to deal with.
        $custom_field = $_POST[$key];

        //If no data is entered
        if (is_null($custom_field)) {

            //delete the field. No point saving it.
            delete_post_meta($post_id, $key);

            // If it is set (there was already data),
            // and the new data isn't empty, update it.
        } elseif (isset($custom_field) && !is_null($custom_field)) {
            // update
            update_post_meta($post_id, $key, $custom_field);

            //Just add the data.
        } else {
            // Add?
            add_post_meta($post_id, $key, $custom_field, TRUE);
        }
    }
    // Save logic goes here. Don't forget to include nonce checks!
   // $mydata = sanitize_text_field($_POST['myplugin_new_field']);
   // update_post_meta($post_id, 'event_meta_box', $mydata);
}

add_action('save_post', 'wpdocs_save_meta_box');


// Same handler function...
add_action('wp_ajax_my_action', 'my_action_callback');
add_action('wp_ajax_nopriv_my_action', 'my_action_callback');

function my_action_callback() {
    global $wpdb;
    $data = $_POST["data"];
    header('Content-Type: application/json');
    echo 'elo';
    wp_die();
}
