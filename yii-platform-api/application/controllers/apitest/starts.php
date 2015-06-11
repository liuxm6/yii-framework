<?php
    /**
     * 获取指定场次是否可以开考(暂时不用)
     * POST /session/getStartTestStatus
     * @return string
     */

    $arr_con = $this->startsCon();
    $this->render('index',$arr_con);