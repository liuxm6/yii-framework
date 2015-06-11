<?php
Yii::import('mts.core.MtsUserIdentity');
class UserIdentity extends MtsUserIdentity
{
    const ERROR_MEMBER_INVALID = 3;
    public $uid;
    public $admin;
    public function __construct($username,$password,$admin=null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->admin = $admin;
    }
    public function authenticate()
    {
        $user = SysAdmin::model()->findByAttributes(array('UserName'=>$this->username));
        if (!$user) {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
            return !$this->errorCode;
        }
        $password = $user->Password;
        if(md5($this->password) != $password) {
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
            return !$this->errorCode;
        }
        else {
            $this->uid = $user->id;
            $this->errorCode=self::ERROR_NONE;
            $user->LoginTimes           = time();
            $user->LastLoginIp          = $_SERVER["REMOTE_ADDR"];
            $user->save();
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