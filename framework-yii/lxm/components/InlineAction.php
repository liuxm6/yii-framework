<?php


class InlineAction extends CInlineAction
{
    public function run()
    {
        $module = $this->getController()->getModule();
        if (!$module) $module = Yii::app();
        $dir = $module->getControllerPath();
        $action = strtolower($this->getId());
        $method='action'.$this->getId();
        if(method_exists($this->getController(), $method)) {
            $this->getController()->$method();
        }
        else {
            $file = $dir.DIRECTORY_SEPARATOR.$this->getController()->id.DIRECTORY_SEPARATOR.$action.'.php';
            $this->getController()->runFile($file);
        }
    }
    public function runWithParams($params)
    {
        $module = $this->getController()->getModule();
        if (!$module) $module = Yii::app();
        $dir = $module->getControllerPath();
        $action = strtolower($this->getId());
        $method='action'.$this->getId();
        if(method_exists($this->getController(), $method)) {
            return parent::runWithParams($params);
        }
        else {
            $file = $dir.DIRECTORY_SEPARATOR.$this->getController()->id.DIRECTORY_SEPARATOR.$action.'.php';
            $this->getController()->runFile($file, $params);
        }
    }
}