<?php
/**
 * @author guiping
 *
 */
class ErrorController extends Controller
{
    public $layout = false;
    /**
     * @desc 错误页面
     */
    public function actionIndex()
    {
        utf8();
        $error=Yii::app()->errorHandler->error;
        if (in_array($error['code'], array(403,404,500))) {
            $this->render($error['code'], array('data'=>$error));
        }
        else {
            $this->render('index', array('data'=>$error));
        }
    }
}