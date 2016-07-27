<?php

namespace Gloves;

class Logger {

    protected static $dir;

    public static function init($dir) {
        static::$dir = $dir;
        $file = fopen(static::$dir, "a");
        fwrite($file, "Init\n");
        fclose($file);
    }

    public static function write($log) {
        $file = fopen(static::$dir, "a");
        if (is_array($log) || is_object($log)) {
            fwrite($file, serialize($log) . "\n");
        } else {
            fwrite($file, $log . "\n");
        }
        fclose($file);
    }

}
