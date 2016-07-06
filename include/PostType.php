<?php

namespace AltCalendar;

class PostType {
    protected $slug;
    protected $single;
    protected $plural;
    protected $labels;
    protected $args;
    /**
     * 
     * @param string $single
     * @param string $plural
     * @param array $labels
     * @param array $args
     * 
     */
    public function __construct($postType , $single, $plural, $labels = array(), $args = array()) {
        $this->slug = $postType;
        $this->single = strtolower($single);
        $this->plural = strtolower($plural);
        $this->labels = $labels;
        $this->args = $args;
        
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        $plural = $this->plural;
        $single = $this->single;
        $postType = $this->slug;
        
        $dlabels = array(
            'name' => _x(ucfirst($plural), 'post type general name', 'alt-calendar'),
            'singular_name' => _x(ucfirst($single), 'post type singular name', 'alt-calendar'),
            'menu_name' => _x(ucfirst($plural), 'admin menu', 'alt-calendar'),
            'name_admin_bar' => _x(ucfirst($single), 'add new on admin bar', 'alt-calendar'),
            'add_new' => _x('Add new', 'event', 'alt-calendar'),
            'add_new_item' => __('Add new '.$single, 'alt-calendar'),
            'new_item' => __('New '.$single, 'alt-calendar'),
            'edit_item' => __('Edit '.$single, 'alt-calendar'),
            'view_item' => __('View '.$single, 'alt-calendar'),
            'all_items' => __('All '.$plural, 'alt-calendar'),
            'search_items' => __('Search '.$plural, 'alt-calendar'),
            'parent_item_colon' => __('Parent '.$plural.':', 'alt-calendar'),
            'not_found' => __('No '.$plural.' found.', 'alt-calendar'),
            'not_found_in_trash' => __('No '.$plural.' found in Trash.', 'alt-calendar')
        );

        $dargs = array(
            'labels' => $dlabels,
            'description' => __('Description.', 'alt-calendar'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => $postType),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 105,
            'supports' => array('title', 'editor')
        );

        register_post_type($postType, $dargs);
    }
    public function getSlug(){
        return $this->slug;
    }
    public function getName(){
        return array(
            'singular' => $this->single,
            'plural' => $this->plural
        );
    }
}
