<?php

Yii::import('mts.core.MtsController');
class Controller extends MtsController
{
    public $layout='//layouts/main-nav';
    public $mts;
    public function init()
    {
        $this->mts = new Mts;
        parent::init();
    }
    public function checkLogin()
    {
        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
        }
    }
    public function replaceUrl($params, $url=null)
    {
        if (empty($url)) $url = Yii::app()->request->requestUri;
        return url_replace_param($url, $params);
    }
    public function createAction($actionID)
    {
        if($actionID==='')
            $actionID=$this->defaultAction;
        $module = $this->getModule();
        if (!$module)  $module = Yii::app();
        $dir = $module->getControllerPath();
        $action = strtolower($actionID);
        $file = $dir.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$action.'.php';
        if(file_exists($file)) {
            return new InlineAction($this,$actionID);
        }
        else {
            return parent::createAction($actionID);
        }
    }
    public function runFile($file, $___params=array())
    {
        if (!empty($___params)) {
            extract($___params);
        }
        if (is_file($file))
            include $file;
    }
    protected function beforeAction($action)
    {
        $controller = $action->controller;
        $module = isset($controller->module)?$controller->module->id:null;
        if ($module) {
            $guestPaths = array(
                
            );
            if(!in_array(implode('/',array($module,$controller->id,$controller->action->id)),$guestPaths)){
                $this->checkLogin();
            }
        }
        return true;
    }
}