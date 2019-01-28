<?php

namespace Techart\API;

class API
{
    protected static $url = 'https://api.techart.ru';
    protected static $key = false;
    protected static $debug = false;
    
    public static function setUrl($value)
    {
        self::$url = $value;
    }
    
    public static function setKey($value)
    {
        self::$key = $value;
    }
    
    public static function setDebug($callback)
    {
        self::$debug = $callback;
    }
    
    public static function debug()
    {
        if (self::$debug) {
            call_user_func_array(self::$debug, func_get_args());
        }
    }
    
    public static function get($uri)
    {
        if (!preg_match('{^http}', $uri)) {
            $url = rtrim(self::$url, '/') .'/'. ltrim($uri, '/');
        } else {
            $url = $uri;
        }
        self::debug("Request {$url}");
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
        $responseHeaders = array();
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $h) use(&$contentType, &$responseHeaders) {
            $responseHeaders[] = $h;
            $len = strlen($h);
            if (preg_match('{^Content-Type:\s*([^/]+)/([0-9a-z_-]+)}i', $h, $m)) {
                $contentType = $m[1].'/'.$m[2];
            }
            return $len;
        }); 
        
        $result = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        
        if ($code==404) {
            self::debug("Response 404");
            return null;
        }
        
        if ($contentType=='application/json') {
             $result = json_decode($result);
        }
        
        $type = 'Unknown Type';
        if (is_string($result)) {
            $type = 'string('.strlen($result).')';
        }
        if (is_array($result)) {
            $type = 'array('.count($result).')';
        }
        if (is_object($result)) {
            $type = 'object('.get_class($result).')';
        }
        if (is_int($result)) {
            $type = 'int('.$result.')';
        }
        
        self::debug("Response {$code}: {$contentType} {$type}");
        if ($code == 500) {
            self::debug($result, 'Ответ 500');
        }
        
        if ($code != 200) {
        	return;
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