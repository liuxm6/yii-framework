<?php

function mts_create_app($basePath=null)
{
    Yii::import('mts.components.SaveManager');
    Yii::import('mts.core.MtsWebApplication');
    $config = mts_get_config();
    if ($basePath == null)
        $basePath = YII_BASE_PATH;
    $config['basePath'] = $basePath;
    $app = Yii::createApplication('MtsWebApplication', $config);
    return $app;
}

function mts_config_file($t='config')
{
    $env = getenv('APPLICATION_ENV');
    if (!$env) $env = 'local'; //local,development,testing,production
    $configfile = YII_BASE_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$env.'.php';
    if (!is_file($configfile))
        $configfile = YII_BASE_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'local.php';
    return $configfile;
}
function mts_get_domain()
{
    $serv = $_SERVER['SERVER_NAME'];
    $ip = $_SERVER['SERVER_ADDR'];
    $domain = '';
    if ($serv != $ip) {
        $domain = strpos($_SERVER['SERVER_NAME'],'.')!==false?$_SERVER['SERVER_NAME']:'';
    }
    return $domain;
}
function mts_get_config()
{
    $debug = defined('YII_DEBUG') && YII_DEBUG;
    if ($debug) {
        if (!is_dir(YII_BASE_PATH)) {
            @mkdir(YII_BASE_PATH);
        }
    }
    $config_file = mts_config_file();
    if (is_file($config_file))
        $config=require($config_file);
    else
        $config=array();
    if (isset($config['modules']) && is_string($config['modules']) && trim($config['modules'])!="") {
        $config['modules'] = preg_split("/[;,\s]+/", trim($config['modules']));
    }
    else if (!is_array($config['modules'])){
        $config['modules'] = array();
    }

    $mts_config = require(dirname(__FILE__).'/config.php');
    $modules_dir = YII_BASE_PATH.DIRECTORY_SEPARATOR.'modules';
    if (is_dir($modules_dir)) {
        $dirs = scandir($modules_dir);
        foreach ($dirs as $dir) {
            if ($dir != '.' && $dir != '..' && is_dir($modules_dir.'/'.$dir)) {
                $config['modules'][] = $dir;
            }
        }
    }
    if (!empty($config["modules"]))
        $config['modules'] = array_diff($config['modules'], array('gii'));
    unset($config['modules']['gii']);
    $env = getenv('APPLICATION_ENV');
    if (PHP_OS != 'WINNT') {
        unset($mts_config['modules']['gii']);
    }
    $mts_config = array_merge_recu($mts_config, $config);
    return $mts_config;
}
function mts_encrypt($data, $key)
{
    $des = DesPKCS5::instance();
    return $des->encode($data, $key);
}
function mts_decrypt($enc, $key)
{
    $des = DesPKCS5::instance();
    return $des->decode($enc, $key);
}
function mts_password($len=16)
{
    $len = (int)$len;
    if ($len<9) $len = 9;
    if ($len>61) $len=61;
    $pwds = array();
    $list = array();
    $list[0] = array();
    for ($i=0;$i<26;$i++) {
        $list[0][] = chr($i+65);
        $list[0][] = chr($i+97);
        $list[1][] = chr($i+65);
        $list[2][] = chr($i+97);
    }
    shuffle($list[0]);
    $size = $len - 9;
    for ($i=0;$i<$size;$i++)
        $pwds[] = $list[0][$i];
    shuffle($list[1]);
    for ($i=0;$i<3;$i++)
        $pwds[] = $list[1][$i];
    shuffle($list[2]);
    for ($i=0;$i<3;$i++)
        $pwds[] = $list[2][$i];
    for ($i=0;$i<3;$i++)
        $pwds[] = rand(0,9);
    shuffle($pwds);
    return implode('', $pwds);

}
/**
 * 压缩文件函数
 *
 * @zip_file  string 压缩的zip文件名
 * @files     array  需要压缩的文件数组
 * @options   array  参数配置,array('password'=>'','basedir'=>'')
 * @return   int     返回状态
 *
 */
function mts_zip($zip_file, $files, $options=array())
{
    $password = isset($options['password'])?$options['password']:'';
    if ($password) {

    }
}
/**
 * 解压缩文件函数
 *
 * @zip_file  string 需要解压缩的zip文件名
 * @target    string 解压路径
 * @options   array  参数配置,array('password'=>'')
 * @return   int     返回状态
 *
 */
function mts_unzip($zip_file, $target, $options=array())
{
}

function mts_guid($len=32)
{
    return make_object_id().rand(pow(10,7),pow(10,8)-1);
}
function mts_guid_time($token)
{
    $hex = substr($token, 0, 8);
    return (int)hexdec($hex);
}
function mts_t($message,$params=array(), $lang=null,$source=null, $category='global')
{
    if ($params === null) $params = array();
    return Yii::t($category,$message,$params,$source,$lang);
}

function mts_oss_upload($path, $file, $bucket=null, $host=null, $ak=null, $sk=null, $pathSize=5242880)
{
    if (!is_file($file)) {
        echo 'is not file';
        return false;
    }
    Yii::import('mts.extensions.oss.ALIOSS');
    try {
        if (empty($bucket)) $bucket = Yii::app()->getParams()->OSS_BUCKET;
        if (empty($host)) $host = Yii::app()->getParams()->OSS_HOST;
        if (empty($ak)) $ak = Yii::app()->getParams()->OSS_AK;
        if (empty($sk)) $sk = Yii::app()->getParams()->OSS_SK;
        $oss = new ALIOSS($ak,$sk,$host);
        if (Yii_DEBUG)
            $oss->set_debug_mode(true);
        $options = array(
            ALIOSS::OSS_FILE_UPLOAD => $file,
            'partSize' => $pathSize,
        );
        $oss->create_mpu_object($bucket, $path, $options);
        return true;
    }
    catch (Exception $e) {
        print_r($e);
        return false;
    }
}