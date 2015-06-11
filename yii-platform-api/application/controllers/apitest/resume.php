<?php
    /**
     * 恢复考试
     * POST /session/resumeTest
     * @return string
     */

    $arr_con = $this->resumeCon();
    $this->render('index',$arr_con);