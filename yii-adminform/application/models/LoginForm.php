<?php

class LoginForm extends CFormModel
{
    public $username;
    public $password;

    private $_identity;

    public function rules()
    {
        return array(
            array('username, password', 'required'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array('username'=>'用户名', 'password'=>'密码');
    }

    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors())
        {
            $this->_identity=new UserIdentity($this->username, $this->password);
            if(!$this->_identity->authenticate()) {
                switch ($this->_identity->errorCode) {
                    case UserIdentity::ERROR_USERNAME_INVALID:
                        $this->addError('username',_t('此用户不存在')); break;
                    case UserIdentity::ERROR_PASSWORD_INVALID:
                        $this->addError('password',_t('密码不正确')); break;
                }
            }
        }
    }
    public function hasError($attribute)
    {
        $error = $this->getError($attribute);
        return $error !== null;
    }

    public function login()
    {
        if($this->_identity===null)
        {
            $this->_identity=new UserIdentity($this->username,$this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $duration=3600*24*30;
            Yii::app()->user->login($this->_identity,$duration);
            return true;
        }
        else
            return false;
    }
}
