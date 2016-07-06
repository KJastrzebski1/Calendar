<form>
    <p>
        <label><?php echo __('Start', 'alt-calendar'); ?></label><br />
        <input type="date" id="my_meta_box_ds" name="date_start" value="" />
        <input type="time" id="my_meta_box_ts" name="time_start" value="" /></p>
    <p>
        <label><?php echo __('End', 'alt-calendar'); ?></label><br />
        <input type="date" id="my_meta_box_de" name="date_end" value="" />
        <input type="time" id="my_meta_box_te" name="time_end" value="" /></p>
    <p>
        <label><?php echo __('Description', 'alt-calendar'); ?></label><br />
        <textarea rows="3" cols="40" id="my_meta_box_desc" name="my_meta_box_desc"></textarea>
    </p>
</form>