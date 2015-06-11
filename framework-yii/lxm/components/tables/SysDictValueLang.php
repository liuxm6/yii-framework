<?php

class SysDictValueLang extends SysDictTable
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return SysDict::LANG_TABLE;
    }
    public function relations()
    {
        return array(
            'value' => array(self::BELONGS_TO, 'SysDictValue', 'valueId'),
        );
    }
}
