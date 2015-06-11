<?php
    /**
     * 跳出应用程序
     * POST /session/quitApplication
     * @return string 通用错误
     */

    $arr_con = $this->quitappCon();
    $this->render('index',$arr_con);