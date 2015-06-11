<?php

class ApiController extends Controller
{
    public $layout = false;
    public $lang = 'zh_cn';

    public function run($actionID)
    {
        utf8();
        $api = substr(Yii::app()->request->pathInfo, 3);
        $file = dirname(__FILE__).'/api/'.$api.'.php';
        $this->unpack($api);
        if (is_file($file)) {
            try {
                $data = require $file;
            }
            catch (Exception $e) {
                $this->error(1, $e);
            }
            if (is_array($data)) {
                $ret = array(
                    'success'=>1,
                    'data'=>$data
                );
                $this->show($ret);
            }
            else {
                $this->error(1);
            }
        }
        else {
            return parent::run($actionID);
        }
    }
    protected function unpack($api)
    {
        if (!empty($_POST)) {
            $_REQUEST = $_POST;
        }
        $data = $_REQUEST;
        foreach ($data as $k=>$v) {
            if (!empty($_POST[$k])) {
                $v = base64_decode($v);
            }
            $_REQUEST[$k] = $v;
        }
    }
    protected function log($api, $datastr)
    {
        try {
            $log = new LogApiCall;
            $log->Api = $api;
            $log->ClientIp = $_SERVER['REMOTE_ADDR'];
            $log->Host = 'http://'.$_SERVER['SERVER_NAME'];
            $log->LocalHost = 'http://local-api.mts.com/';
            $log->Query = 'api'.$api.'?'.http_build_query($_REQUEST);
            $log->Data = $datastr;
            $log->LogDateTime = date("Y-m-d H:i:s");
            $log->save();
        }
        catch (Exception $e) {
        }

    }
    protected function show($data)
    {
        if (getEnv('APPLICATION_ENV') =='local') {
            echo_r($data);//调试查看数据
        }
        $api = substr(Yii::app()->request->pathInfo, 3);
        $jdata = json_encode($data);
        $this->log($api, $jdata);
        echo base64_encode($jdata);
        Yii::app()->end();
    }
    protected function error($errorNo, $debug=null)
    {
        $data = array(
            'success'=>0,
            'errorno'=>(int)$errorNo,
            'errormsg'=>$this->t($errorNo)
        );
        $this->show($data);
    }
    protected function checkDb()
    {
        try {
            $t1 = microtime(true);
            Yii::app()->db;
            $t2 = microtime(true);
            //echo $t2-$t1;echo "<br>";

        }
        catch (CDbException $e) {
            $this->error(2);//数据库定义的数据还不存在,固定写2
        }
    }
    protected function getParam($name, $default=null)
    {
        $ret = $default;
        $params = $_REQUEST;
        if (isset($params[$name]))
            $ret = $params[$name];
        if ($name == 'lang') {
            $let2 = substr(strtolower($ret), 0, 2);
            if ($let2 == 'zh')
                $ret = 'zh_cn';
            else
                $ret = 'en';
        }
        return $ret;
    }
    protected function lang()
    {
        $lang = strtolower($this->getParam('lang'));
        if (!$lang)
            $lang = $this->lang;
        else
            $this->lang = $lang;
        $list = array('zh_cn','en');
        if (!in_array($lang, $list))
            $lang = 'zh_cn';
        return $lang;
    }
    protected function t($message, $params=array(),$lang=null)
    {
        if ($lang == null)
            $lang = $this->lang();
        return error_t($message, $lang, $params);
    }
}