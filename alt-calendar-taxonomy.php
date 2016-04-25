<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function add_new_calendar(){
    $labels = array(
		'name'                       => _x( 'Calendars', 'taxonomy general name' , 'alt-calendar'),
		'singular_name'              => _x( 'Calendar', 'taxonomy singular name' , 'alt-calendar'),
		'search_items'               => __( 'Search Calendars' , 'alt-calendar' ),
		'popular_items'              => __( 'Popular Clendars' , 'alt-calendar'),
		'all_items'                  => __( 'All Calendars' , 'alt-calendar'),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Calendar' , 'alt-calendar'),
		'update_item'                => __( 'Update Calendar' , 'alt-calendar'),
		'add_new_item'               => __( 'Add New Calendar' , 'alt-calendar'),
		'new_item_name'              => __( 'New Writer Calendar' , 'alt-calendar'),
		'separate_items_with_commas' => __( 'Separate calendars with commas' , 'alt-calendar'),
		'add_or_remove_items'        => __( 'Add or remove calendars' , 'alt-calendar'),
		'choose_from_most_used'      => __( 'Choose from the most used calendars' , 'alt-calendar'),
		'not_found'                  => __( 'No calendars found.' , 'alt-calendar'),
		'menu_name'                  => __( 'Calendars' , 'alt-calendar'),
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
add_action('init', 'add_new_calendar');