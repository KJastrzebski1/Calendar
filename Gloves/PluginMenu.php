<?php

namespace Gloves;

class PluginMenu {

    protected static $viewDir;
    protected static $instance;
    protected static $page;
    protected static $subpages;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function init($view) {
        static::$viewDir = $view;

        add_action('admin_menu', array('\Gloves\PluginMenu', 'create'));
        add_filter('parent_file', array('\Gloves\PluginMenu', 'filter'));
    }

    public static function create() {
        $domain = Config::get('text-domain');
        static::$page['file'] = $domain;
        $name = Config::get('name');
        static::$page['id'] = add_menu_page($name, $name, 'administrator', $domain, array('\Gloves\PluginMenu', 'view'));
        foreach (static::$subpages as &$page) {
            $page['id'] = (\add_submenu_page($domain, $page['title'], $page['title'], 'administrator', $page['link']));
        }
    }

    public static function filter() {
        global $submenu_file, $current_screen, $pagenow;
        $parent_file = null;
        // Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
        Logger::write($current_screen);
        if ($current_screen->post_type == 'calendar_event') {
            
            if ($pagenow == 'post.php') {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }

            if ($pagenow == 'edit-tags.php') {
                $submenu_file = 'edit-tags.php?taxonomy=alt-calendar&post_type=' . $current_screen->post_type;
            }

            $parent_file = static::$page['file'];
        }

        return $parent_file;
    }

    public static function view() {
        Render::view(static::$viewDir);
    }

    public static function addPage($title, $link) {
        $i = count(static::$subpages);
        static::$subpages[$i]['title'] = $title;
        static::$subpages[$i]['link'] = $link;
    }

}
