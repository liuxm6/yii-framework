<?php


class Mts extends CComponent
{
    public $user;
    public $cssUrl = '/css';
    public $imgUrl = '/img';
    public $jsUrl = '/js';
    public $bootUrl = '/bootstrap';
    public $rootUrl = '/';
    public function __construct()
    {
        if (!Yii::app()->user->isGuest) {
            $userId = Yii::app()->user->id;
            $sysUser = SysAdmin::model()->findByPk($userId);
            $this->user = $sysUser;
        }
        else {
            $this->user = new stdclass;
            $this->user->id = 0;
            $this->user->Name = 'guest';
        }
        $this->initJs();
    }
    public function active($match, $level=1)
    {
        $match = trim($match,'/');
        $url = trim(Yii::app()->request->pathInfo,'/');
        $class = $level == 1?' active':' class="active"';
        return substr($url,0,strlen($match))==$match?$class:'';
    }
    public function log($action, $log,$memberId=0,$MemberName='',$MemberShortName='')
    {
        if (empty($action))
            $action = 'default';
        if (empty($log))
            return;
        $controller = Yii::app()->getController();
        $model = new SysAdminLog();
        $model->UserId              = Yii::app()->user->id;
        $model->MemberId            = $memberId;
        $model->MemberName          = $MemberName;
        $model->MemberShortName     = $MemberShortName;
        $model->ActionName          = $action;
        $model->ActionTime          = time();
        $model->ActionDescription   = $log;
        $model->Ip                  = $_SERVER["REMOTE_ADDR"];
        $model->UserAgent           = $_SERVER['HTTP_USER_AGENT'];
        try {
            $model->save();
        }
        catch (Exception $e) {
        }
    }
    public function emailLog($uid,$isAdmin=false){
        if($isAdmin){
            $sendMailLog = new SysAdminMlog();
        }
        else{
            $sendMailLog = new SysSendmailLog();
        }
        $desCode = PublicFunction::createRandomStr(100);
        $sendMailLog->UserId            = $uid;
        $sendMailLog->Code              = $desCode;
        $sendMailLog->IsUsed            = 1;
        $sendMailLog->CreateTime        = time();
        $sendMailLog->SendTime          = time();
        if($sendMailLog->validate(null,false)){
            try{                
                if($sendMailLog->save()){
                    return $sendMailLog;
                }
            }
            catch(Exception $e){
                return false;
            }
        }
        return false;
    }
    public function initJs(){
        $cs = Yii::app()->clientScript;
        $set = new stdClass();
        $set->basePath = $this->rootUrl;
        $set->locale = Yii::app()->language;
        $set->isGuest = Yii::app()->user->isGuest?true:false;
        $cs->registerScript('init',
            '$.extend(mts.settings,'.json_encode($set).')',
            CClientScript::POS_HEAD
        );
    }
    public function checkAccess($operation)
    {
        if(!Yii::app()->user->isGuest){
            if($this->user->IsAdmin){
                return true;
            }
            else{
                $dict = new SysDict();
                $pv = $dict->getValue($operation,'ADMIN_PERMS')->value;
                $perms = unserialize($this->user->Perm);
                if(in_array($pv,$perms))
                    return true;
            }
        }
        return false;
    }
}