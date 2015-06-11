<?php

return array (
  'charset' => 'UTF-8',
  'components' => array (
    'db' => array (
      'connectionString' => 'mysql:host=localhost;dbname=XXX',
      'username' => 'root',
      'password' => '',
    ),
    'db01' => array (
      'class'=>'CDbConnection',
      'connectionString' => 'mysql:host=localhost;dbname=XXX',
      'username' => 'root',
      'password' => '',
      'charset'  => 'utf8'
    ),
    'session' => array (
        'class'=> 'CCacheHttpSession',
        'cookieMode' => 'only',
        'timeout' => 1200
    ),
  ),
  'defaultController' => 'index',
  'language' => 'zh_cn',
  'layout' => 'main',
  'modules' => '',
  'params'=>array(
        'title' => '',
        'url' => '',
    ),
);
