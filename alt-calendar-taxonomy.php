<?php

/* 
 * 
 */



function add_new_calendar(){
    $labels = array(
		'name'                       => _x( 'Chose calendars', 'taxonomy general name' ),
		'singular_name'              => _x( 'Calendar', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Calendars' ),
		'popular_items'              => __( 'Popular Clendars' ),
		'all_items'                  => __( 'All Calendars' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Calendar' ),
		'update_item'                => __( 'Update Calendar' ),
		'add_new_item'               => __( 'Add New Calendar' ),
		'new_item_name'              => __( 'New Writer Calendar' ),
		'separate_items_with_commas' => __( 'Separate calendars with commas' ),
		'add_or_remove_items'        => __( 'Add or remove calendars' ),
		'choose_from_most_used'      => __( 'Choose from the most used calendars' ),
		'not_found'                  => __( 'No calendars found.' ),
		'menu_name'                  => __( 'Calendars' ),
	);
    $args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'alt-calendar' ),
	);
    register_taxonomy(
            'alt-calendar', 
            'calendar_event',
            $args
            );
}
//register_new_calendar('admin-calendar');
add_action('init', 'add_new_calendar');