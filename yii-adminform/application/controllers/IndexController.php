<?php
/**
 *
 * @author guiping
 *
 */
class IndexController extends Controller
{
    public $layout = 'main-nav';
    /**
     * @desc 首页
     */
    public function actionIndex()
    {
        if(Yii::app()->user->isGuest){
            $this->redirect($this->createUrl('/login'));
        }
        else{
            $this->redirect($this->createUrl('/dashboard'));
        }
        $this->render('index');
    }
}