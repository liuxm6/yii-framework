<?php

class LoginController extends Controller
{
    public $layout = false;
    public function actionIndex()
    {
        if(!Yii::app()->user->isGuest)
            $this->redirect($this->createUrl('/dashboard'));
        $model=new LoginForm;
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            if($model->validate() && $model->login()) {
                $this->redirect($this->createUrl('/dashboard'));
            }
        }
        $this->render('index', array('model'=>$model));
    }
}