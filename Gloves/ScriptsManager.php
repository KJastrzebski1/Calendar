<?php

namespace Gloves;

abstract class ScriptsManager {

    protected static $instance;
    protected static $scripts;
    protected static $adminScripts;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function init() {
        $class = get_called_class();
        add_action('admin_menu', array($class, 'loadAdmin'));
        add_action('wp_enqueue_scripts', array($class, 'load'));
    }

    abstract static function loadAdmin();

    abstract static function load();

}
