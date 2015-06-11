<?php
    /**
     * 加入考试（登录到 Test Number）
     * POST /session/signinTestCode
     * @return string
     */

    $arr_con = $this->signinCon();
    $this->render('index',$arr_con);