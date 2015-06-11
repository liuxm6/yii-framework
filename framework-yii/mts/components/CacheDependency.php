<?php

class CacheDependency extends CCacheDependency
{
    const PRE_FIX = 'cache_dependency_key:';
    protected $key;
    public function __construct($key)
    {
        $this->key = self::PRE_FIX.$key;
    }
    protected function generateDependentData()
    {
        $cache = Yii::app()->cache;
        return $cache?$cache->get($this->key):null;
    }
    public static function update($key)
    {
        $key = self::PRE_FIX.$key;
        $cache = Yii::app()->cache;
        if ($cache) {
            $value = (int)$cache->get($key);
            $cache->set($key, ($value+1)%65535);
        }
    }
    public static function get($key)
    {
        $key = self::PRE_FIX.$key;
        $cache = Yii::app()->cache;
        if ($cache) {
            return $cache->get($key);
        }
        return false;
    }
}