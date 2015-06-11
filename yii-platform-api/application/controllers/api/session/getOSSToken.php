<?php
    #api:getOSSToken

    $data = array();
    $this->checkDb();
    $dict = new SysDict;
    //require 'inc-token.php';

    $ossConfig = Yii::app()->ossConfig['response'];
    $content = $this->getParam('content');
    if (empty($content)) {
        $this->error($dict->getValue('ERROR_CODE:E00001')->value);
    }
    $data['OSSToken'] = 'OSS '.$ossConfig['accessKey'].':'.base64_encode(hash_hmac('sha1', $content, $ossConfig['secureKey'],true));
    return $data;
