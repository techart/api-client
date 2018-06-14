<?php

namespace Techart\API;

class Cache
{
    protected static $path = false;
    protected static $lifeTime = 60;
    protected static $attemptTime = 30;
    protected static $lifeTimes = array();
    protected static $enable = true;
    
    public static function setPath($path)
    {
        self::$path = $path;
    }
    
    public static function setLifeTime($time)
    {
        self::$lifeTime = $time;
    }
    
    public static function setLifeTimeFor($name, $time)
    {
        self::$lifeTimes[$name] = $time;
    }
    
    public function getLifeTimeFor($name)
    {
        if (isset(self::$lifeTimes[$name])) {
            return self::$lifeTimes[$name];
        }
        return self::$lifeTime;
    }
    
    public static function setAttemptTime($time)
    {
        self::$attemptTime = $time;
    }
    
    public static function disable()
    {
        self::$enable = false;
    }
    
    public static function getPath($subPath)
    {
        if (!self::$path) {
            $path = realpath(rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/..').'/cache/techart-api-client';
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }
            self::setPath($path);
        }
        return rtrim(self::$path,'/').'/'.ltrim($subPath, '/');
    }
    
    public static function set($name, $value, $lifeTime = false)
    {
        $lifeTime = $lifeTime===false? self::$lifeTime : $lifeTime;
        $expireTime = $lifeTime==0? (time()+100000000) : (time()+$lifeTime*60);
        $path = self::getPath($name);
        $value = serialize($value);
        file_put_contents($path, "{$expireTime}:{$value}");
    }
    
    public static function delete($name)
    {
        $path = self::getPath($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
    
    public static function get($name, $call = false)
    {
        $value = null;
        $path = self::getPath($name);
        if (self::$enable && is_file($path)) {
            $content = file_get_contents($path);
            if (preg_match('{^(\d+):(.+)$}', $content, $m)) {
                $time = (int)$m[1];
                $value = unserialize($m[2]);
                if ($time>time()) {
                    return $value;
                }
            }
        }
        
        if (is_callable($call)) {
            $newValue = call_user_func($call);
            if (empty($newValue)) {
                if (!is_null($value)) {
                    self::set($name, $value, self::$attemptTime);
                }
            } else {
                $value = $newValue;
                self::set($name, $value, self::getLifeTimeFor($name));
            }
        }
        return $value;
    }
}