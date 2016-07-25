<?php

namespace Gloves;

class PluginMenu {
    protected static $viewDir;
    protected static $instance;
    protected static $subpages;

    private function __construct() {
        
    }
    
    public static function getInstance(){
        if(!isset(static::$instance)){
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    public static function init($view){
        static::$viewDir = $view;
        
        add_action('admin_menu', array('\Gloves\PluginMenu', 'create'));
    }
    
    public static function create(){
        $domain = Config::get('text-domain');
        $name = Config::get('name');
        add_menu_page($name, $name, 'administrator', $domain, array('\Gloves\PluginMenu', 'view'));
        foreach (static::$subpages as $page){
            \add_submenu_page($domain, $page['title'], $page['title'], 'administrator', $page['link']);
        }
    }
    
    public static function view(){
        include '/../'. static::$viewDir .'.php';
    }
    
    public static function addPage($title, $link){
        $i = count(static::$subpages);
        static::$subpages[$i]['title'] = $title;
        static::$subpages[$i]['link'] = $link;
    }
}
