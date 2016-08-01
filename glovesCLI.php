<?php

class GlovesCLI {

    protected $function;
    protected $args;
    protected $config;

    public function __construct($args) {
        $this->config = parse_ini_file('config.ini');
        if (isset($args[1])) {
            $this->function = $args[1];
            $method = $this->function;
            if (method_exists($this, $method)) {
                $arg = isset($args[2]) ? $args[2] : null;
                $this->$method($arg);
            } else {
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
        $name = $this->config['name'];
        $domain = $this->config['text-domain'];
        $class = str_replace(' ', '', $name);
        $slug = strtolower(str_replace(' ', '-', $name));
        $file = fopen($slug . '.php', 'w');
        $content = "<?php

/*
 * Plugin Name: $name
 * Description: Made in Gloves
 * Author: 
 * Text Domain: $domain
 * Domain Path: 
 * Version: 0.0.1
 * License: GPL3
 * 
 */

use Gloves\Plugin;

class $class extends Plugin {

    protected static \$modules = [];
    protected static \$models = [];
    protected static \$settings = [];
    
    public static function init() {
        parent::init();
    }
    
    public static function activate() {
        parent::activate();
    }
    
    public static function deactivate() {
        parent::deactivate();
    }
    
    public static function uninstall() {
        parent::uninstall();
    }
}

$class::init();";

        fwrite($file, $content);
        fclose($file);
    }

    protected function make_model($arg) {
        $file = fopen('Model\\'. $arg . '.php', 'w');
        $content = "<?php

namespace Model;

use Gloves\Model\Model;

class $arg extends Model{
    protected static \$fields = [
    
    ];
    
    protected static \$version = '';
}";
        fwrite($file, $content);
        fclose($file);
        $name = $this->config['name'];
        $slug = strtolower(str_replace(' ', '-', $name));
        $content = str_replace("protected static \$models = [", "protected static \$models = [\n        '$arg',", file_get_contents($slug . '.php'));
        $plugin_file = fopen($slug . '.php', 'w');
        fwrite($plugin_file, $content);
        fclose($plugin_file);
    }

}

new GlovesCLI($argv);
