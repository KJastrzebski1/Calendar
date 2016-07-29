<?php

namespace Gloves;

class Render {
    protected static $dir;


    private function __construct() {
        ;
    }
    public static function init($dir){
        static::$dir = $dir.'/'.Config::get('views-directory');
    }

    public static function view($dir){
        $dir = str_replace('.', '/', $dir);
        include static::$dir.'/'.$dir.'.php';
    }
}