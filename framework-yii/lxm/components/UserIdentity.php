<?php
Yii::import('mts.core.MtsUserIdentity');
class UserIdentity extends MtsUserIdentity
{
    public $uid;
    public function authenticate()
    {
        $row = new stdclass;
        $row->id = 1;
        $row->password = md5("123456");
        $row->username = "admin";
        $row->name = "admin";
        if(!$row)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else {
            $password = $row->password;
            if(md5($this->password) != $password)
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            else {
                $this->uid = $row->id;
                $this->errorCode=self::ERROR_NONE;
                $this->setState('cname', $row->name);
            }
        }
        return !$this->errorCode;
    }
    public function getId()
    {
        return $this->uid;
    }
    public function getName()
    {
        return $this->username;
    }
}