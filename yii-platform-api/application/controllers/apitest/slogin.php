<?php
    /**
     * 登录准考证
     * POST /session/login
     * @return string
     */

    $arr_con = $this->sloginCon();
    $this->render('index',$arr_con);