<?php
    /**
     * 上传答案
     * POST 请求交卷接口返回的uploadURI 如：/mtsonline/postAnswer
     * @return string
     */

    $arr_con = $this->upanswerCon();
    $this->render('index',$arr_con);