<?php
/**
 *
 * @author guiping
 *
 */
class IndexController extends Controller
{
    public $layout = 'main';

    /**
     * @desc 首页
     */
    public function actionIndex()
    {
        Yii::app()->cache->set('TEST-LANG=zhcn&val=333444', serialize($this));
    }
    public function actionSetCookie()
    {
        setcookie("b",3, 0,'/', '172.16.26.134');
    }
    public function actionGetCookie()
    {
        echo_r($_COOKIE);
    }
    public function actionImport()
    {
        $this->render('index');
    }
    public function actionLogin()
    {

        $model = new LoginForm;
        $model->username = 'admin';
        $model->password = 'admin';
        $model->validate();
        $model->login();
        echo Yii::app()->user->name;
    }
    public function actionUser()
    {

        Yii::app()->session->open();
        //echo session_id();
        //echo_r($_SESSION);
        echo Yii::app()->user->id;
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
    }

}