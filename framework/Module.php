<?php

namespace Framework;

abstract class Module{
    public function init();
    public function activate();
    public function deactivate();
    public function uninstall();
}