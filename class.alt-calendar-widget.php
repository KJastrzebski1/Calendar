<?php
defined('ABSPATH') or die('No script kiddies please!');

class Alt_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'alt_widget', // Base ID
                __('Alt Calendar', 'alt-calendar'), // Name
                array('description' => __('Best Calendar', 'alt-calendar'),) // Args
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        ?>
        <div id="widget_calendar">

        </div>
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