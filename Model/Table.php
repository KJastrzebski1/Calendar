<?php

namespace Model;

use Gloves\Model\Model;
class Table extends Model{
    protected static $fields = [
        'title' => 'text'
    ];
    
    protected static $version = '0.1';
}
