<?php
namespace Module;

use \Gloves\PostType;
use \Gloves\AbstractMetaBox;

class MetaBox extends AbstractMetaBox{
    
    public function init() {
        global $post;
        $values = get_post_custom($post->ID);
        $start = unserialize($values['start'][0]);
        $end = unserialize($values['end'][0]);
        $date_start = isset($values['start']) ? $start->format('Y-m-d') : current_time('Y-m-d');
        $time_start = isset($values['start']) ? $start->format('H:i') : current_time('H:i');
        $date_end = isset($values['end']) ? $end->format('Y-m-d') : current_time('Y-m-d');
        $time_end = isset($values['end']) ? $end->format('H:i') : date('H:i', current_time('timestamp') + 7200);

        wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
        ?>

        <p>
            <label>Start</label>
            <input type="date" id="my_meta_box_ds" name="date_start" value="<?php echo $date_start ?>" />
            <input type="time" id="my_meta_box_ts" name="time_start" value="<?php echo $time_start ?>" />
        </p>
        <p>
            <label>End</label>
            <input type="date" id="my_meta_box_de" name="date_end" value="<?php echo $date_end ?>" />
            <input type="time" id="my_meta_box_te" name="time_end" value="<?php echo $time_end ?>" />
        </p>
        <?php
    }

    function save($post_id) {
        // Bail if we're doing an auto save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce'))
            return;

        // if our current user can't edit this post, bail
        if (!current_user_can('edit_post'))
            return;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        $date_start = esc_attr($_POST['date_start']);
        $time_start = esc_attr($_POST['time_start']);
        $date_end = esc_attr($_POST['date_end']);
        $time_end = esc_attr($_POST['time_end']);
        $start = new \DateTime($date_start . ' ' . $time_start);
        update_post_meta($post_id, 'start', $start);
        $end = new \DateTime($date_end . ' ' . $time_end);
        update_post_meta($post_id, 'end', $end);
        update_post_meta($post_id, 'user_id', $user_id);
    }

}
