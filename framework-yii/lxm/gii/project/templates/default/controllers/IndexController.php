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
        $this->render('index');
    }
    public function actionTest()
    {
        $this->render('index');
    }

}