<?php

namespace Techart\API;

class API
{
    protected static $url = 'https://api.techart.ru';
    protected static $key = false;
    
    public static function setUrl($value)
    {
        self::$url = $value;
    }
    
    public static function setKey($value)
    {
        self::$key = $value;
    }
    
    public static function get($uri)
    {
        $url = rtrim(self::$url, '/') .'/'. ltrim($uri, '/');
        $contentType = 'text/html';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 

        $sendHeaders = array();
        if (self::$key) {
            $sendHeaders[] = 'x-techart-api-key: '.self::$key; 
        }
        
        if (!empty($sendHeaders)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $sendHeaders); 
        }
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $h) use(&$contentType) {
            $len = strlen($h);
            if (preg_match('{^Content-Type:\s*([^/]+)/([0-9a-z_-]+)}i', $h, $m)) {
                $contentType = $m[1].'/'.$m[2];
            }
            return $len;
        }); 
        
        $result = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        
        if ($code==404) {
            return null;
        }
        
        if ($contentType=='application/json') {
             $result = json_decode($result);
        }
        
        return $result;
    }
    
    public static function getStruct($uri)
    {
        $result = self::get($uri);
        if (!is_array($result) && !is_object($result)) {
            return;
        }
        return $result;
    }
}