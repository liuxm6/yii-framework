<?php

class LxmController extends CController
{
    public $menu=array();
    public $breadcrumbs=array();
    public function checkAccess($item_id, $operation, $params=array(), $ret=false)
    {
        $ok = Yii::app()->authManager->checkAccess($item_id, $operation, $params);
        if (!$ok) {
            throw new CHttpException(403, "没有访问权限");
        }
        return $ok;
    }
    public function checkLogin()
    {
        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
        }
    }
    public function getAccess($params=array())
    {
        return true;
    }
    protected function beforeAction($action)
    {
        return true;
    }
}