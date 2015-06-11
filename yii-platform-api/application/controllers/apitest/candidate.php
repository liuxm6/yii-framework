<?php
    /**
     * 确认考生信息
     * POST /session/confirmCandidate
     * @return string
     */

    $arr_con = $this->candidateCon();
    $this->render('index',$arr_con);