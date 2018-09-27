<?php

namespace Techart\API;

class Subscriptions
{
    public static function getAll()
    {
        return Cache::get('subscriptions-all.json', function() {
            return API::getStruct('/subscribe/export/');
        });
    }
}