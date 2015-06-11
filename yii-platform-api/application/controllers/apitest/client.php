<?php
    /**
     * 获取 MTS 考试机最新版本信息
     * POST /client/version
     * @return string
     */

    $arr_con = $this->clientInfo();
    $this->render('index',$arr_con);