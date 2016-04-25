<?php

/* 
    Custom post type: calendar_event
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function alt_event_init() {
    $labels = array(
        'name' => _x('Events', 'post type general name', 'alt-calendar'),
        'singular_name' => _x('Event', 'post type singular name', 'alt-calendar'),
        'menu_name' => _x('Events', 'admin menu', 'alt-calendar'),
        'name_admin_bar' => _x('Event', 'add new on admin bar', 'alt-calendar'),
        'add_new' => _x('Add New', 'event', 'alt-calendar'),
        'add_new_item' => __('Add New Event', 'alt-calendar'),
        'new_item' => __('New Event', 'alt-calendar'),
        'edit_item' => __('Edit Event', 'alt-calendar'),
        'view_item' => __('View Event', 'alt-calendar'),
        'all_items' => __('All Events', 'alt-calendar'),
        'search_items' => __('Search Events', 'alt-calendar'),
        'parent_item_colon' => __('Parent Events:', 'alt-calendar'),
        'not_found' => __('No events found.', 'alt-calendar'),
        'not_found_in_trash' => __('No events found in Trash.', 'alt-calendar')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'alt-calendar'),
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