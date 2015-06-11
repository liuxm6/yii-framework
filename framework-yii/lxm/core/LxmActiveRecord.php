<?php

class LxmActiveRecord extends CActiveRecord
{
    public function afterSave()
    {
        parent::afterSave();
    }
    public function getDbConfigName()
    {
        return 'db';
    }
    public function getModelPath()
    {
        return 'application.models';
    }
    public function getDbConnection()
    {
        $dbname = $this->getDbConfigName();
        self::$db=Yii::app()->getComponent($dbname);
        if(self::$db instanceof CDbConnection)
            return self::$db;
        else
            throw new CDbException(Yii::t('yii','Active Record requires a "'.$dbname.'" CDbConnection application component.'));
    }
    public function exportValue($column)
    {
        return isset($this->{"show".$column})?$this->{"show".$column}:$this->$column;
    }
    public function findColumnBySql($column,$sql,$params=array())
    {
        $row = $this->findBySql($sql, $params);
        return $row->$column;
    }
    public function findColumnByPk($column, $pk,$condition='',$params=array())
    {
        $row = $this->findByPk($pk, $condition, $params);
        return $row->$column;
    }
    public function findColumnByAttributes($column, $attributes,$condition='',$params=array())
    {
        $row = $this->findByAttributes($attributes, $condition, $params);
        return $row->$column;
    }
    public function findColumnsAll($column, $condition='',$params=array())
    {
        $rows = $this->findAll($condition, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[] = $row->$column;
        }
        return $ret;
    }
    public function findColumnsByAttributes($column, $attributes,$condition='',$params=array())
    {
        $rows = $this->findAllByAttributes($attributes, $condition, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[] = $row->$column;
        }
        return $ret;
    }
    public function findColumnsBySql($column, $sql, $params=array())
    {
        $rows = $this->findAllBySql($sql, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[] = $row->$column;
        }
        return $ret;
    }
    public function findKeyValsAll($keycol, $valcol, $condition='',$params=array())
    {
        $rows = $this->findAll($condition, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[$row->$keycol] = $row->$valcol;
        }
        return $ret;
    }
    public function findKeyValsByAttributes($keycol, $valcol, $attributes,$condition='',$params=array())
    {
        $rows = $this->findAllByAttributes($attributes, $condition, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[$row->$keycol] = $row->$valcol;
        }
        return $ret;
    }
    public function findKeyValsBySql($keycol, $valcol, $sql, $params=array())
    {
        $rows = $this->findAllBySql($sql, $params);
        $ret = array();
        foreach ($rows as $row) {
            $ret[$row->$keycol] = $row->$valcol;
        }
        return $ret;
    }
    public function getRelationNameByAttribute($attribute)
    {
        foreach ($this->relations() as $k=>$v) {
            if ($v[0] == self::BELONGS_TO && $v[2] == $attribute) {
                return $k;
            }
        }
        return null;
    }
    public function getRelationClassNameByAttribute($attribute)
    {
        foreach ($this->relations() as $k=>$v) {
            if ($v[0] == self::BELONGS_TO && $v[2] == $attribute) {
                return $v[1];
            }
        }
        return null;
    }
    public function getRelationClassNameByName($relation)
    {
        $relations = $this->relations();
        if (isset($relations[$relation])) {
            return $relations[$relation][1];
        }
        return null;
    }
    public function hasColumnOption($attribute)
    {
        $opts = $this->getColumnOptions();
        return isset($opts[$attribute]);
    }
    public function getColumnOption($attribute)
    {
        $opts = $this->getColumnOptions();
        $ret = array();
        if (isset($opts[$attribute])) {
            $ret = $opts[$attribute];
        }
        return $ret;
    }
    public function getColumnOptions()
    {
        return array();
    }
    public function value($attribute)
    {
        if(strpos($attribute,'.')!==false) {
            $segs=explode('.',$attribute);
            $name=array_pop($segs);
            $model=$this;
            foreach($segs as $seg)
            {
                $relations=$model?$model->relations():array();
                if(isset($relations[$seg]) && $relations[$seg][0]==self::BELONGS_TO)
                    $model=$model->{$seg};
                else
                    break;
            }

            return $model?$model->getAttribute($name):'';
        }
        else {
            return $this->getAttribute($attribute);
        }

    }
    public function hasRelationAttribute($attribute)
    {
        if(strpos($attribute,'.')!==false) {
            $segs=explode('.',$attribute);
            $name=array_pop($segs);
            $model=$this;
            $c = count($segs);
            $i = 0;
            foreach($segs as $seg)
            {
                $relations=$model->relations();
                if(isset($relations[$seg]) && $relations[$seg][0]==self::BELONGS_TO) {
                    $model = CActiveRecord::model($relations[$seg][1]);
                    if (!$model) {
                        return false;
                    }
                    else {
                        $i++;
                    }
                }
                else
                    break;

            }
            if ($i != $c) {
                return false;
            }
            else {
                return $model?$model->hasAttribute($name):false;
            }
        }
        else {
            return $this->hasAttribute($attribute);
        }
    }
}