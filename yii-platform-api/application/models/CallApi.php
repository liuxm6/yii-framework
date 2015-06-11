<?php

define('YII_DEBUG',true);
define('YII_TRACE_LEVEL',3);
define('YII_PROJECT_PATH', dirname(dirname(__DIR__)));
define('YII_BASE_PATH', dirname(__DIR__));
require_once '../../../framework-yii/mts.php';
mts_create_app();


CallApi::run();

class CallApi
{
    public static function random($data)
    {
        $data = array_values($data);
        return $data[rand(0, count($data)-1)];
    }
    public static function run()
    {
        $apis = array(
            'ApiClientVersion',
            'ApiSessionSigninTestCode',
            'ApiSessionConfirmTestNumber',
            'ApiSessionLogin',
            'ApiSessionGetTestFormPassword',
            'ApiSessionGetStartTestStatus',
            'ApiSessionGetPostAnswerStatus',
            'ApiSessionGetAnswerStatus',
            'ApiSessionResumeTest'
        );

        foreach ($apis as $name) {
            preg_match('/Api([A-Z][a-z]+)([A-Z].*)/', $name, $matches);
            $api = '/'.strtolower($matches[1]).'/'.lcfirst($matches[2]);
            $o = new $name;
            echo http_build_query($o)."\n";
        }
    }
}


class ApiClientVersion
{
    public function __construct()
    {
        $this->platform = CallApi::random(mts_constant_list('mts_platform'));
        $this->lang = 'zh_cn';
    }
}

class ApiSessionSigninTestCode
{
    public function __construct()
    {
        $this->deviceID='0x'.sprintf("%04s",dechex(CallApi::random(mts_constant_list('mts_platform')))).mts_guid();
        $this->testNumber='123456';
        $this->deviceInfo=CallApi::random(array("IOS 8.1","4.0.0","windows 7 32bit"));
        $this->appVersion=CallApi::random(array("IOS 8.1","4.0.0","windows 7 32bit"));
        $this->osVersion=CallApi::random(array("IOS 8.1","4.0.0","windows 7 32bit"));
        $this->sdkVersion=CallApi::random(array("IOS 8.1","4.0.0","windows 7 32bit"));
        $this->modelName='ipad air';
        $this->resolution='1024x768';
        $this->location="112.44";
        $this->other='density';
        $this->lang='zh-cn';
    }
}


class ApiSessionConfirmTestNumber
{
    public function __construct()
    {
        $this->deviceID='0x'.sprintf("%04s",dechex(CallApi::random(mts_constant_list('mts_platform')))).mts_guid();
        $this->testNumber='123456';
        $this->confirm=CallApi::random(array(0,1));
        $this->lang='zh-cn';
    }
}


class ApiSessionLogin
{
    public function __construct()
    {
        $this->deviceID='0x'.sprintf("%04s",dechex(CallApi::random(mts_constant_list('mts_platform')))).mts_guid();
        $this->testNumber='123456';
        $this->candidateNumber = "STU".rand(0,9).rand(0,99).rand(0,99);
        $this->confirm = CallApi::random(array(0,1));
        $this->useragent = CallApi::random(array("Chrome","IE","Firefox"));
        $this->lang='zh-cn';
    }
}

class ApiSessionGetTestFormPassword
{
    public function __construct()
    {
        $this->uuid = mts_guid();
        $this->lang='zh-cn';
    }
}


class ApiSessionGetStartTestStatus
{
    public function __construct()
    {
        $this->uuid = mts_guid();
        $this->lang='zh-cn';
    }
}

class ApiSessionGetPostAnswerStatus
{
    public function __construct()
    {
        $this->uuid = mts_guid();
        $this->type = CallApi::random(array(1,2,3,4,5));
        $this->lang='zh-cn';
    }
}

class ApiSessionGetAnswerStatus
{
    public function __construct()
    {
        $this->uuid = mts_guid();
        $this->xmlSha1 = sha1(microtime());
        $this->sha1 = sha1(microtime(true));
        $this->lang='zh-cn';
    }
}

class ApiSessionResumeTest
{
    public function __construct()
    {
        $this->uuid = mts_guid();
        $this->localtime = date('Y-m-d H:i:s');
        $this->records = array(1,2,3,4);
        $this->lang='zh-cn';
    }
}
