<?php
namespace Module;

defined('ABSPATH') or die('No script kiddies please!');

class Widget extends \WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'alt_widget', 
                __('Alt Calendar', 'alt-calendar'), 
                array('description' => __('Best Calendar', 'alt-calendar'),) 
        );
    }
    public static function init(){
        add_action('widgets_init', function() {
            register_widget('Module\Widget'); 
        });
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