<?php

class LogoutController extends Controller
{
    public function actionIndex()
    {
        Yii::app()->session->clear();
        Yii::app()->session->destroy();
        Yii::app()->user->logout();
        $this->redirect('/index');
    }

}
