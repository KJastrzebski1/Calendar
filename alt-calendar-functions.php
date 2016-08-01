<?php

defined('ABSPATH') or die('No script kiddies please!');

add_action('plugins_loaded', 'alt_plugin_lang');


function alt_plugin_lang() {
    load_plugin_textdomain('alt-calendar', false, dirname(plugin_basename(__FILE__)) . '/lang');
}
