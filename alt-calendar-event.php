<?php

/* 
    Custom post type: calendar_event
 */

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
        'show_in_menu' => false,
        'query_var' => true,
        'rewrite' => array('slug' => 'calendar_event'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 105,
        'supports' => array('title', 'editor')
    );

    register_post_type('calendar_event', $args);
}