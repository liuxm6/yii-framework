<?php

define('YII_DEBUG',true);
define('YII_TRACE_LEVEL',3);

chdir(dirname(__DIR__).'/public');
require_once '../../framework-yii/mts.php';
utf8();
mts_create_app();

$testing = true;
if ($testing) {
    $config = array(
        'class'=>'CDbConnection',
        'connectionString' => 'mysql:host=127.0.0.1;dbname=mtsop',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8',
    );
    $db = Yii::createComponent($config);
    Yii::app()->setComponent('db', $db);
}
else {
    $db = Yii::app()->db;
}

$file = dirname(__FILE__).'/dict.txt';
if (!is_file($file))
    die();

$content = file_get_contents($file);

$sql = <<<EOD
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `dict_field`;
CREATE TABLE `dict_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(40) NOT NULL COMMENT '属性名',
  `multi` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否多选值,默认不多选，多选必须是数据项值定义为二进制只有一个位为1的整数',
  `groupId` int(11) NOT NULL COMMENT '分组',
  PRIMARY KEY (`id`),
  KEY `dict_field_groupId` (`groupId`),
  CONSTRAINT `dict_field_groupId` FOREIGN KEY (`groupId`) REFERENCES `dict_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='属性域';

DROP TABLE IF EXISTS `dict_group`;
CREATE TABLE `dict_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(80) NOT NULL COMMENT '键值名',
  `name` varchar(255) NOT NULL COMMENT '显示名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='属性域可选数据项';

DROP TABLE IF EXISTS `dict_table_column`;
CREATE TABLE `dict_table_column` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(255) NOT NULL COMMENT '表名',
  `column` varchar(255) NOT NULL COMMENT '字段名',
  `groupId` int(11) NOT NULL COMMENT '字典组',
  PRIMARY KEY (`id`),
  KEY `dict_table_column_groupId` (`groupId`),
  CONSTRAINT `dict_table_column_groupId` FOREIGN KEY (`groupId`) REFERENCES `dict_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库字段对应选值字典组';

DROP TABLE IF EXISTS `dict_value`;
CREATE TABLE `dict_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL COMMENT '字典组',
  `groupKey` varchar(255) NOT NULL COMMENT '字典组键',
  `key` varchar(255) NOT NULL COMMENT '键名,在同一个groupId下不能相同',
  `name` varchar(255) NOT NULL COMMENT '数据项显示名,默认一种显示，增加定义需要使用多语言映射',
  `value` varchar(255) NOT NULL COMMENT '数据值',
  `valueString` varchar(255) NOT NULL COMMENT '数据值(代码显示)',
  PRIMARY KEY (`id`),
  KEY `dict_value_groupId` (`groupId`),
  CONSTRAINT `dict_value_groupId` FOREIGN KEY (`groupId`) REFERENCES `dict_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据项定义';

DROP TABLE IF EXISTS `dict_value_lang`;
CREATE TABLE `dict_value_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valueId` int(11) NOT NULL,
  `name` varchar(200) NOT NULL COMMENT '数据项显示名,默认一种显示，增加定义需要使用多语言映射',
  `lang` varchar(10) NOT NULL COMMENT '数据值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='多语言数据项显示';

EOD;


$db->createCommand($sql)->execute();
$dict = new SysDict;


$dict->importContent($content);
