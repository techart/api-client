<?php

namespace Techart\API;

class Results
{
    public static function get($id)
    {
        return API::getStruct("results/{$id}.json");
    }
}