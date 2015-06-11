<?php

class SysDictGroup extends SysDictTable
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return SysDict::GROUP_TABLE;
    }
    public function relations()
    {
        return array(
            'fields' => array(self::HAS_MANY, 'SysDictField', 'groupId'),
            'values' => array(self::HAS_MANY, 'SysDictValue', 'groupId'),
        );
    }
    public function getValue($valueKey)
    {
        $valueKey = strtoupper($valueKey);
        $oValue = SysDictValue::model()->findByAttributes(array('key'=>$valueKey,'groupId'=>$this->id));
        $ret = new stdclass;
        foreach ($oValue->attributes as $k=>$v) {
            $ret->$k = $v;
        }
        return $ret;
    }
    public function getList($lang=null)
    {
        $ret = array();
        foreach ($this->values as $value) {
            $valueLang = null;
            if ($lang !== null) {
                $valueLang = SysDictValueLang::model()->findByAttributes(array('valueId'=>$value->id,'lang'=>$lang));
            }
            if (!$valueLang)
                $name = $value->name;
            else
                $name = $valueLang->name;
            $value = trim($value->value);
            $ret[$value] = $name;
        }
        return $ret;
    }
}
