<?php
defined('YII_PROJECT_PATH')  or define('YII_PROJECT_PATH', dirname(getcwd()));
defined('YII_BASE_PATH') or define('YII_BASE_PATH', YII_PROJECT_PATH.DIRECTORY_SEPARATOR.'application');

ini_set('error_reporting',  ~E_NOTICE & E_ALL);
require_once dirname(__FILE__)."/lxm/functions.php";
require_once dirname(__FILE__)."/lxm/mtsfuncs.php";
require_once dirname(__FILE__)."/yii.php";
Yii::setPathOfAlias('lxm', dirname(__FILE__).'/lxm');
