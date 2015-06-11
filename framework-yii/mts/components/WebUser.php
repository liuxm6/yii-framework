<?php
Yii::import('mts.core.MtsWebUser');
class WebUser extends MtsWebUser
{
    public $logoutUrl = array('logout');
    public $loginUrl = array('login');
    /**
     * 需要重新编写login
     */
    public function login($identity,$duration=0)
    {
        return parent::login($identity, $duration);
    }
    public function getCname()
    {
        if(($name=$this->getState('__cname'))!==null)
            return $name;
        else
            return $this->guestName;
    }
    protected function changeIdentity($id,$name,$states)
    {
        parent::changeIdentity($id, $name, $states);
    }
    public function getLoginUrl()
    {
        $url = $this->loginUrl;
        if (is_array($url)) {
            $route=isset($url[0]) ? $url[0] : $app->defaultController;
            $url=Yii::app()->createUrl($route,array_splice($url,1));
        }
        else {
            $url=Yii::app()->createUrl('login/index');
        }
        return $url;
    }
}