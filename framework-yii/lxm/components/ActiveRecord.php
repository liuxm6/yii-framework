<?php

Yii::import('mts.core.MtsActiveRecord');
class ActiveRecord extends MtsActiveRecord
{
    public function getErrorFirst()
    {
        $str = null;
        foreach ($this->errors as $error) {
            if (!empty($error))
                $str = reset($error);
        }
        return $str;
    }
    /**
     * 转换支持数据表中的首字母大写的字段使用小写读取
     * @param name
     * @return mixed
     */
    public function __get($name)
    {
        $name = (string)$name;
        if (!$this->hasAttribute($name)) {
            $uname = ucfirst($name);
            if ($this->hasAttribute($uname)) {
                $name = $uname;
            }
        }
        return parent::__get($name);
    }
    /**
     * 转换支持数据表中的首字母大写的字段使用小写读取
     * @param name
     * @return mixed
     */
    public function __set($name, $value)
    {
        $name = (string)$name;
        if (!$this->hasAttribute($name)) {
            $uname = ucfirst($name);
            if ($this->hasAttribute($uname)) {
                $name = $uname;
            }
        }
        return parent::__set($name, $value);
    }
    public function cache($duration, $key=null, $queryCount=1)
    {
        if (Yii::app()->cache && $key) {
            $dependency = new CacheDependency($key);
            parent::cache($duration, $dependency, $queryCount);
        }
        return $this;
    }
    public function toObject()
    {
        $o = new stdclass;
        foreach ($this->attributes as $k=>$v) {
            $o->$k = $v;
        }
        return $o;
    }
}