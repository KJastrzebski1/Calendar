<?php

namespace Gloves;

defined('ABSPATH') or die('No script kiddies please!');
/**
 * Manages plugin options
 * 
 */
class PluginSettings {

    protected static $settings;

    public static function init() {
        add_action('admin_init', array('\Gloves\PluginSettings', 'register'));
    }

    /**
     * On admin_init hook
     */
    public static function register() {
        $domain = Config::get('text-domain') . '-settings';
        if (isset(static::$settings)) {
            foreach (static::$settings as $name) {
                \register_setting($domain, $name);
            }
        }
    }

    /**
     * On plugin deactivation
     */
    public static function unregister() {
        $domain = Config::get('text-domain') . '-settings';
        if (isset(static::$settings)) {
            foreach (static::$settings as $name) {
                \unregister_setting($domain, $name);
                \delete_option($name);
            }
        }
    }

    /**
     * Adds array of settings
     * 
     * @param array $settings
     */
    public static function add(array $settings) {
        foreach ($settings as $option) {
            static::$settings[] = $option;
        }
    }

    public static function get($option) {
        return \get_option($option);
    }

    public static function set($option, $value) {
        return \update_option($option, $value);
    }

}
