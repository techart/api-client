<?php

namespace Techart\API;

class Constants
{
    public static function getAll()
    {
        return Cache::get('constants.json', function() {
            return API::getStruct('constants.json');
        });
    }
    
    public static function get($name)
    {
        $all = self::getAll();
        return isset($all->$name)? $all->$name->value : null;
    }
}