<?php

class SysDictTableColumn extends SysDictTable
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return SysDict::TABLE_COLUMN_TABLE;
    }
    public function relations()
    {
        return array(
            'group' => array(self::BELONGS_TO, 'SysDictGroup', 'groupId'),
        );
    }
}
