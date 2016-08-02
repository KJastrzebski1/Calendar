<?php

include 'autoloader.php';
//define( 'ABSPATH', dirname(__FILE__, 4) . '/' );
include dirname(__FILE__, 4) . '/wp-config.php';
echo "Welcome in Gloves CLI.\n";

class GlovesCLI {

    protected $function;
    protected $args;
    protected $config;

    public function __construct($args) {
        $this->config = include 'conf.php';
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
        $name = "Plugin Name: ".$this->config['name'];
        
        $domain = $this->config['text-domain'];
        $class = str_replace(' ', '', $name);
        $slug = strtolower(str_replace(' ', '-', $name));
        if(file_exists($slug.'.php')){
            echo "Plugin file already exists.";
            exit();
        }
        $file = fopen($slug . '.php', 'w');
        $content = "<?php

/*
 * $name
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
        $class = "\\Model\\" . $arg;
        echo "Creating Model file $class\n";
        $file = fopen($class . '.php', 'w');
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
        echo "$class file created. Go to the file and create structure of the table. After you are finished add Model name to array in your main plugin file.";
    }

    protected function remove_model($arg) {
        $class = "\\Model\\" . $arg;

        if (class_exists($class)) {
            echo "Dropping $class...\n";
            $class::drop();
        } else {
            echo "There is no such model. $class";
            exit();
        }
        echo "Table of $class dropped. Do you also want to remove file? (Y/N)";
        $stdin = fopen('php://stdin', 'r');
        $response = fgetc($stdin);
        if ($response != 'Y') {
            echo "Aborted.\n";
            exit;
        }
        unlink($class.'.php');
        echo "File $class removed.\nRemember to remove Model from array in your main plugin file.";
    }

}

new GlovesCLI($argv);
