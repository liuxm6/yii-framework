<?php
    /**
     * 提交考生状态（暂时不用）
     * POST /postCandidateStatus
     * @return string
     */

    $arr_con = $this->cstatCon();
    $this->render('index',$arr_con);