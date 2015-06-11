<?php

return array (
  'charset' => 'UTF-8',
  'components' => array (
    'db' => array (
          'connectionString' => 'mysql:host=localhost;dbname=XX',
          'username' => 'root',
          'password' => '',
    ),
    'session' => array (
        'class'=> 'CCacheHttpSession',
        'cookieMode' => 'only',
        'timeout' => 1200
    ),
    'cache'=>array(
        'class'=>'CMemCache',
        'servers'=>array(
            array(
                'host'=>'localhost',
                'port'=>11211,
            ),
        ),
    ),
  ),
  'defaultController' => 'index',
  'language' => 'zh_cn',
  'layout' => 'main',
  'modules' => '',
  'params'=>array(
    'XX'=>'XX',
  ),
    'ossConfig'=>array(
        'response'=>array(
            'accessKey'=>'',
            'secureKey'=>'',
            'bucket'=>'',
            'host'=>'',
            'baseurl'=>''
        ),
    ),
);
