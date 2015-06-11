<?php
    /**
     * 获取试卷密码（暂时不用）
     * POST /session/getTestFormPassword
     * @return string
     */

    $arr_con = $this->testpwdCon();
    $this->render('index',$arr_con);