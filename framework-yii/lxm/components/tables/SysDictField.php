<?php

class SysDictField extends DictTable
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return Dict::FIELD_TABLE;
    }
    public function relations()
    {
        return array(
            'group' => array(self::BELONGS_TO, 'SysDictGroup', 'groupId'),
        );
    }
}
