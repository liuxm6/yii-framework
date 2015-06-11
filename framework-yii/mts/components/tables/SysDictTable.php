<?php

abstract class SysDictTable extends ActiveRecord
{
    public function getDbConfigName()
    {
        return SysDict::CONNECTION_ID;
    }
    public function getModelPath()
    {
        return SysDict::MODULE_PATH;
    }
}
