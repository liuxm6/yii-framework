<?php

Yii::import('system.gii.GiiModule');
class GiiMtsModule extends GiiModule
{
    private $_newBasePath;
    public $generatorPaths=array('mts.gii');
    public function getBasePath()
    {
        if($this->_newBasePath===null)
        {
            $class=new ReflectionClass('GiiModule');
            $this->_newBasePath=dirname($class->getFileName());
            parent::setBasePath(dirname($class->getFileName()));
        }
        return $this->_newBasePath;
    }
    public function beforeControllerAction($controller, $action)
    {
        return true;
    }
    protected function findGenerators()
    {
        $ret = array();
        $data = parent::findGenerators();
        $ret['controller'] = $data['controller'];
        $ret['module'] = $data['module'];
        $ret['dbmodel'] = $data['dbmodel'];
        //$ret['crud'] = $data['crud'];
        $ret['project'] = $data['project'];
        $ret['mtscrud'] = $data['mtscrud'];
        $ret['form'] = $data['form'];
        $ret['select'] = $data['select'];
        return $ret;
    }
}