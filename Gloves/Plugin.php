<?php

namespace Gloves;

include_once 'Config.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

abstract class Plugin {
    /*
     * list of modules
     * 
     * @var array
     */

    protected static $modules;

    /**
     * list of settings
     * 
     * @var array
     */
    protected static $settings;

    private function __construct() {
        
    }

    /*
     * Modules initialization
     */

    public static function init() {
         
        
        $main = new \ReflectionClass(get_called_class());

        $dir = $main->getFileName();
        $class = get_called_class();
        
        register_activation_hook($dir, array($class, "activate"));
        register_deactivation_hook($dir, array($class, "deactivate"));
        register_uninstall_hook($dir, array($class, "uninstall"));
        
        Logger::init(dirname($dir).'/debug.log');
        
        PluginSettings::add(static::$settings);
        PluginSettings::init();
        Render::init(dirname($dir));
       
        foreach (static::$modules as $module => $args) {
            $module = '\Module\\' . $module;

            $module::init($args);
        }
        //do_action('taxonomy-init');
        spl_autoload_unregister('autoload');

    }

    public static function activate(){
        Logger::write('activate');
        foreach (static::$modules as $module => $args) {
            $module = '\Module\\' . $module;
            if(method_exists($module, 'activate')){
                $module::activate($args);
            }
            
        }
    }

    public static function deactivate(){
        Logger::write('deactivate');
        foreach (static::$modules as $module => $args) {
            $module = '\Module\\' . $module;
            if(method_exists($module, 'deactivate')){
                $module::deactivate($args);
            }
            
        }
    }

    public static function uninstall(){
        Logger::write('uninstall');
        foreach (static::$modules as $module => $args) {
            $module = '\Module\\' . $module;
            if(method_exists($module, 'uninstall')){
                $module::uninstall($args);
            }
            
        }
    }
}
