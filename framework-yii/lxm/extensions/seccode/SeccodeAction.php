<?php

class SeccodeAction extends CCaptchaAction
{
    public function getVerifyCode($regenerate=true)
    {
        return parent::getVerifyCode($regenerate);
    }
    protected function generateVerifyCode()
    {
        $list = array('b','c','e','f','g','h','j','k','r','s','t','w','x','y',1,3,4,5,6,7,8,9);
        shuffle($list);
        $code = '';
        for ($i=0;$i<4;$i++)
            $code .=  $list[$i];
        return $code;
    }
    protected function renderImage($code)
    {
        Yii::import('mts.extensions.seccode.SeccodeClass');
        $img = new SeccodeClass;
        $img->code = $code;
        $img->display();
    }
}