<?php
    /**
     * 考试确认（确认 Test Number）
     * POST /session/confirmTestNumber
     * @return string
     */
    $arr_con = $this->confirmCon();
    $this->render('index',$arr_con);