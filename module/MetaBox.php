<?php
namespace Module;

class MetaBox {
    protected $postType;
    
    public function __construct(PostType $postType) {
        $this->postType = $postType;
        
        add_action('add_meta_boxes', array($this, 'add'));
        add_action('save_post', array($this, 'save'));
        
    }
    public function add(){
        $postType = $this->postType;
        $names = $postType->getName();
        add_meta_box($names['plural'] . '-meta-box-id', $names['singular'] . ' data', array($this, 'init'), $postType->getSlug(), 'normal', 'high');
        
        
    }
    public function init() {
        // $post is already set, and contains an object: the WordPress post
        global $post;
        $values = get_post_custom($post->ID);
        $date_start = isset($values['date_start']) ? $start->format('Y-m-d') : current_time('Y-m-d');
        $time_start = isset($values['time_start']) ? $start->format('H:i') : current_time('H:i');
        $date_end = isset($values['date_end']) ? $end->format('Y-m-d') : current_time('Y-m-d');
        $time_end = isset($values['time_end']) ? $end->format('H:i') : date('H:i', current_time('timestamp') + 7200);

        // We'll use this nonce field later on when saving.
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
