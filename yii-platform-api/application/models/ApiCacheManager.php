<?php

class ApiCacheManager
{
    const DEPEND_KEY = 'ApiCacheDependKey';
    const CACHE_TIME = 600;
    protected $cache;
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
    }
    public function set($name, $value)
    {
        $dependValue = $this->cache->get(self::DEPEND_KEY);
        if (!isset($dependValue[$name])) {
            $dependValue[$name] = 1;
            $this->cache->set(self::DEPEND_KEY, $dependValue, self::CACHE_TIME);
        }
        $data = array($value, $dependValue[$name]);
        $this->cache->set($name, $data);
    }
    public function get($name, $reload=false)
    {
        /*
        $dependValue = $this->cache->get(self::DEPEND_KEY);
        $checkValue = isset($dependValue[$name])?$dependValue[$name]:0;
        $data = $this->cache->get($name);
        $ret = null;
        if(isset($data[0],$data[1]) && $data[1] == $checkValue && !$reload) {
            $ret = $data[0];
        }*/
        $ret = false;

        return $ret;
    }
    public function update($name)
    {
        $dependValue = $this->cache->get(self::DEPEND_KEY);
        if (!isset($dependValue[$name])) {
            $dependValue[$name] = 1;
        }
        else {
            $v = ($dependValue[$name] + 1)%65535;
            if ($v == 0) $v++;
            $dependValue[$name] = $v;
        }
        $this->cache->set(self::DEPEND_KEY, $ependValue);
    }
    public function updateAll()
    {
        $this->cache->set(self::DEPEND_KEY, array());
    }
    public function getCacheKey($params)
    {
        ksort($params);
        return http_build_query($params);
    }

}