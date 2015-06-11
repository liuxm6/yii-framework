<?php


class SysDict extends CComponent
{
    const CONNECTION_ID      = 'db';
    const MODULE_PATH        = 'mts.components';
    const FIELD_TABLE        = 'dict_field';
    const TABLE_COLUMN_TABLE = 'dict_table_column';
    const VALUE_TABLE        = 'dict_value';
    const GROUP_TABLE        = 'dict_group';
    const LANG_TABLE         = 'dict_value_lang';
    public $lang             = 'zh_cn';
    public $db;
    public $reload           = false;
    public $duration         = 3600;

    public function __construct()
    {
    }
    public function getGroup($groupKey)
    {
        $groupKey = strtoupper($groupKey);
        return SysDictGroup::model()->findByAttributes(array('key'=>$groupKey));
    }
    public function getValue($valueKey, $groupKey=null)
    {
        $valueKey = strtoupper($valueKey);
        $groupKey = strtoupper($groupKey);
        $ret = false;
        if (empty($groupKey)) {
            if (($pos=strpos($valueKey,':'))!==false) {
                $groupKey = substr($valueKey, 0, $pos);
                $valueKey = substr($valueKey, $pos+1);
            }
        }
        $cache = Yii::app()->cache;
        $list = array();
        $cacheKey = 'cacheDictListKey';
        if ($cache) {
            $list = $cache->get($cacheKey);
        }
        if (empty($list) || $this->reload) {
            $rows = SysDictValue::model()->findAll();
            foreach ($rows as $row) {
                $o = new stdclass;
                foreach ($row->attributes as $k=>$v) {
                    $o->$k = $v;
                }
                $list[$row->groupKey][$row->key] = $o;
            }
            if ($cache) {
                $cache->set($cacheKey, $list, $this->duration);
            }
        }
        if (isset($list[$groupKey][$valueKey])) {
            $ret = $list[$groupKey][$valueKey];
        }
        else {
            $oGroup =$this->getGroup($groupKey);
            if ($oGroup) {
                $ret = $oGroup->getValue($valueKey);
            }
        }
        return $ret;
    }
    public function getList($groupKey)
    {
        $oGroup =$this->getGroup($groupKey);
        if ($oGroup) {
            return $oGroup->getList($this->lang);
        }
        return false;
    }
    public function addFullValue($groupKey,$groupName,$valueKey,$valueName,$valueString,$value)
    {
        $groupKey    = trim($groupKey);
        $groupName   = trim($groupName);
        $valueKey    = trim($valueKey);
        $valueName   = trim($valueName);
        $valueString = trim($valueString);
        $value       = trim($value);
        $oGroup = SysDictGroup::model()->findByAttributes(array('key'=>$groupKey));
        if (!$oGroup) {
            $oGroup = new SysDictGroup;
            $oGroup->key = $groupKey;
            $oGroup->name = $groupName;
            $oGroup->save();
        }
        $oValue = SysDictValue::model()->findByAttributes(array('key'=>$valueKey,'groupId'=>$oGroup->id));
        if (!$oValue) {
            $oValue = new SysDictValue;
            $oValue->groupId = $oGroup->id;
            $oValue->groupKey = $oGroup->key;
            $oValue->key = $valueKey;
        }
        $oValue->name = $valueName;
        $oValue->value = $value;
        $oValue->valueString = $valueString;
        $oValue->save();
        return $this;

    }
    public function importContent($content)
    {
        $data = array();
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (trim($line) == '') continue;
            $list = explode(',', $line);
            $data[$list[0]]['key'] = $list[0];
            $data[$list[0]]['name'] = $list[1];
            $data[$list[0]]['values'][$list[2]]=array(
                'key'=>$list[2],
                'name'=>$list[3],
                'valueString'=>$list[4],
                'value'=>$list[5]
            );
        }
        ksort($data);
        foreach ($data as $groupKey=>$data1) {
            $groupName = $data1['name'];
            foreach ($data1['values'] as $data2) {
                $valueKey      = $data2['key'];
                $valueName     = $data2['name'];
                $value         = $data2['value'];
                $valueString   = $data2['valueString'];
                $this->addFullValue($groupKey,$groupName,$valueKey,$valueName,$valueString,$value);
            }
        }
    }
}

