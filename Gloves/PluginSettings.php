<?php

namespace Gloves;
use \Module\Calendar;
class PluginSettings {

    protected static $settings;

    public static function init() {
        add_action('admin_init', array('\Gloves\PluginSettings', 'register'));
    }

    public static function register() {
        $domain = Config::get('text-domain') . '-settings';
        if (isset(static::$settings)) {
            foreach (static::$settings as $name) {
                \register_setting($domain, $name);
            }
        }
        
    }

    public static function add($settings) {
        static::$settings = $settings;
    }

}
