<?php
defined('ABSPATH') or die('No script kiddies please!');

class Alt_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'alt_widget', // Base ID
                __('Alt Calendar', 'alt_calendar'), // Name
                array('description' => __('Best Calendar', 'alt_calendar'),) // Args
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        ?>
<a href="alt-calendar/">

            <div id="calendar">

            </div>
        </a>
        <?php
        echo $args['after_widget'];
    }


    public function form($instance) {
        
    }


    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

}

// class Foo_Widget