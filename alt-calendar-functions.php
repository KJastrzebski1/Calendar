<?php

defined('ABSPATH') or die('No script kiddies please!');

add_action('plugins_loaded', 'alt_plugin_lang');

function write_log($log) {
    if (true === WP_DEBUG) {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}

function alt_plugin_lang() {
    load_plugin_textdomain('alt-calendar', false, dirname(plugin_basename(__FILE__)) . '/lang');
}

function mbe_set_current_menu($parent_file) {
    global $submenu_file, $current_screen, $pagenow;

    // Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
    if ($current_screen->post_type == 'calendar_event') {

        if ($pagenow == 'post.php') {
            $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
        }

        if ($pagenow == 'edit-tags.php') {
            $submenu_file = 'edit-tags.php?taxonomy=alt-calendar&post_type=' . $current_screen->post_type;
        }

        $parent_file = 'alt-calendar';
    }

    return $parent_file;
}

//add_filter('parent_file', 'mbe_set_current_menu');
