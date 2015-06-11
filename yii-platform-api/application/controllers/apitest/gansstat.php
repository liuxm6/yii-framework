<?php
    /**
     * 交卷是否完成
     * POST /session/getAnswerStatus
     * @return string
     */

    $arr_con = $this->gansstatCon();
    $this->render('index',$arr_con);