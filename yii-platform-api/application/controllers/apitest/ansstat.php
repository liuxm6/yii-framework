<?php
    /**
     * 请求交卷
     * POST /session/getPostAnswerStatus
     * @return string
     */

    $arr_con = $this->ansstatCon();
    $this->render('index',$arr_con);