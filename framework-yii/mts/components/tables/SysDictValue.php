<?php

class SysDictValue extends SysDictTable
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return SysDict::VALUE_TABLE;
    }
    public function relations()
    {
        return array(
            'group' => array(self::BELONGS_TO, 'SysDictGroup', 'groupId'),
            'langs' => array(self::HAS_MANY, 'SysDictValueLang', 'valueId'),
        );
    }

}
