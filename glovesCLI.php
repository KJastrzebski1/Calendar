<?php
include_once 'Gloves/Config.php';

use Gloves\Config;

class GlovesCLI {

    protected $function;
    protected $args;

    public function __construct($args) {
        
        if (isset($args[1])) {
            $this->function = $args[1];
            $method = $this->function;
            if(method_exists($this, $method)){
                $this->$method();
            }else{
                $this->help();
            }
            
            
        } else {
            $this->help();
        }
    }

    private function help() {
        $methods = get_class_methods(__CLASS__);
        echo "\nTry use function from below: \n";

        foreach ($methods as $method) {
            if ($method == "__construct") {
                continue;
            }
            echo "\t- $method\n";
        }
    }

    protected function setup() {
        $name = Config::get('name');
        $domain = Config::get('text-domain');
        $slug = str_replace(' ', '-', $name);
        $file = fopen($slug.'.php', 'w');
        $content = "<?php \n"
                . "/*
 * Plugin Name: $name
 * Description: Made in Gloves
 * Author: 
 * Text Domain: $domain
 * Domain Path: 
 * Version: 0.0.1
 * License: GPL3
 * 
 */"
                . ""
                . "";
        fwrite($file, $content);
        fclose($file);
        
    }

    protected function make() {
        
    }

}

new GlovesCLI($argv);
