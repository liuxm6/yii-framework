<?php

Yii::import('mts.core.MtsController');
class Controller extends MtsController
{
    public $layout='//layouts/main';
    public function checkAccess($item_id, $operation, $params=array(), $ret=false)
    {
        return true;
    }
    public function checkLogin()
    {
    }
    public function replaceUrl($params, $url=null)
    {
        if (empty($url)) $url = Yii::app()->request->requestUri;
        return url_replace_param($url, $params);
    }
    protected function beforeAction($action)
    {
        return true;
    }
    public function runFile($file, $___params=array())
    {
        if (!empty($___params)) {
            extract($___params);
        }
        if (is_file($file))
            include $file;
    }
}