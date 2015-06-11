<?php

//echo dechex(time());
//echo "<br>";

//echo bin2hex('cc');


//echo make_object_id();
/*
$list = mts_constant_list();
$file = mts_config_file('constant');

$lines = file($file);
$map = array();
foreach ($lines as $line) {
    if (trim($line) == '') continue;
    if (preg_match('/define\(\'([^\']+)\',[\s]+([x\d]+)\);[\s]+\/\/(.*)/',$line, $matches)) {

        $pos1 = strrpos($matches[1],'_');
        $pos2 = strpos($matches[3], ' ');
        $groupKey = substr($matches[1], 0, $pos1);
        $valueKey = trim(substr($matches[1], $pos1+1));
        if ($pos2) {
            $groupName = substr($matches[3], 0, $pos2);
            $valueName = trim(substr($matches[3], $pos2+1));
        }
        else {
            $groupName = trim($matches[3]);
            $valueName = trim($matches[3]);
        }
        $valueString = trim($matches[2]);
        if (is_numeric($valueString)) {
            if (strpos($valueString, '.')) {
                list($value) = sscanf($valueString, "%f");
            }
            else if (strtolower(substr($valueString, 0, 2)) == '0x') {
                list($value) = sscanf($valueString, "%x");
            }
            else {
                list($value) = sscanf($valueString, "%d");
            }
        }
        else {
            list($value) = sscanf($valueString, "%s");
        }


        echo $groupKey.",".$groupName.",".$valueKey.",".$valueName.",".$valueString.','.$value."\n";

    }
}
*/


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


Yii::app()->db->createCommand($sql)->execute();

$content = <<<EOD

MEMBER_STATUS,会员状态,INVALID,无效,0,0
MEMBER_STATUS,会员状态,VALID,有效,1,1

MEMBER_TYPE,单位性质,TRAIN,培训机构,1,1
MEMBER_TYPE,单位性质,COMP,企业单位,2,2
MEMBER_TYPE,单位性质,GOV,政府机关,3,3

USER_ROLE,角色,ADMIN,管理员,1,1
USER_ROLE,角色,USER,普通,2,2

ADMIN_ROLE,管理角色,ADMIN,超级管理员,1,1
ADMIN_ROLE,管理角色,USER,普通管理员,2,2

CLIENT_FORCE_UPDATE,客户端是否强制更新,FALSE,非强制,0,0
CLIENT_FORCE_UPDATE,客户端是否强制更新,TRUE,强制更新,1,1

USER_STATUS,用户状态,INVALID,无效,0,0
USER_STATUS,用户状态,VALID,有效,1,1

LANGUAGE,多语言,ZH_CN,中文,zh_cn,zh_cn
LANGUAGE,多语言,EN,英文,en,en

GENDER,性别,MALE,男性,M,M
GENDER,性别,FEMALE,女性,F,F

IDENTITY_TYPE,证件类型,IC,身份证,1,1
IDENTITY_TYPE,证件类型,PP,护照,2,2
IDENTITY_TYPE,证件类型,SSN,社会保障号,3,3
IDENTITY_TYPE,证件类型,DRL,驾驶证,4,4
IDENTITY_TYPE,证件类型,PTHK,港澳通行证,5,5
IDENTITY_TYPE,证件类型,PTTW,往来台湾通行证,6,6
IDENTITY_TYPE,证件类型,STN,学生证,11,11
IDENTITY_TYPE,证件类型,MON,军官证,12,12
IDENTITY_TYPE,证件类型,EMN,工作证,13,13
IDENTITY_TYPE,证件类型,MTPHK,港澳同胞回乡证,51,51
IDENTITY_TYPE,证件类型,MTPTW,台胞证,71,71
IDENTITY_TYPE,证件类型,GIC,身份证(全球),101,101
IDENTITY_TYPE,证件类型,GPP,护照(全球),102,102
IDENTITY_TYPE,证件类型,GSSN,社会保障号(全球),103,103
IDENTITY_TYPE,证件类型,GDRL,驾驶证(全球),104,104
IDENTITY_TYPE,证件类型,OTH,其他,99,99

PLATFORM_TYPE,考试机平台类型,WINDOWS,MICROSOFT WINDOWS,0x0001,1
PLATFORM_TYPE,考试机平台类型,OSX,APPLE OSX(预留),0x0002,2
PLATFORM_TYPE,考试机平台类型,LINUX,LINUX(预留),0x0004,4
PLATFORM_TYPE,考试机平台类型,IPAD,APPLE IPAD,0x0010,16
PLATFORM_TYPE,考试机平台类型,APAD,ANDROID PAD,0x0020,32
PLATFORM_TYPE,考试机平台类型,MPAD,MTS PAD,0x0040,64
PLATFORM_TYPE,考试机平台类型,IPHONE,APPLE IPHONE、APPLE IPOD TOUCH(预留),0x0100,256
PLATFORM_TYPE,考试机平台类型,ANDROID,ANDROID 手机(预留),0x0200,512
PLATFORM_TYPE,考试机平台类型,WEBKIT,WEBKIT 核心的浏览器 如：GOOGLE CHROME、APPLE SAFARI(预留),0x1000,4096
PLATFORM_TYPE,考试机平台类型,IE,MICROSOFT WINDOWS、INTERNET EXPLORER浏览器(预留),0x2000,8192
PLATFORM_TYPE,考试机平台类型,FIREFOX,MOZILLA FIREFOX浏览器(预留),0x4000,16384

CANDIDATE_STATUS,考生状态,READY,就绪,0x0000,0
CANDIDATE_STATUS,考生状态,LOGIN,登录未确认,0x0001,1
CANDIDATE_STATUS,考生状态,CONFIRM,登录已确认,0x0005,5
CANDIDATE_STATUS,考生状态,TESTING,考试进行中,0x0010,16
CANDIDATE_STATUS,考生状态,PAUSE,考试暂停,0x0020,32
CANDIDATE_STATUS,考生状态,CLOSE,考试结束,0x0200,512
CANDIDATE_STATUS,考生状态,ABSENT,缺考,0x0400,1024
CANDIDATE_STATUS,考生状态,UPLOADING,上传试卷中,0x0500,1280
CANDIDATE_STATUS,考生状态,UPLOAD_FAIL,上传试卷失败,0x0600,1536
CANDIDATE_STATUS,考生状态,UPLOAD_SUCC,上传试卷成功,0x0700,1792
CANDIDATE_STATUS,考生状态,SCOREING,判分中,0x0800,2048
CANDIDATE_STATUS,考生状态,SCORE_FAIL,判分失败,0x0900,2304
CANDIDATE_STATUS,考生状态,SCORE_SUCC,判分成功,0x0A00,2560

CANDIDATE_CONFIRM,考生确认考试,YES,取消,1,1
CANDIDATE_CONFIRM,考生确认考试,NO,取消,0,0

PROTOCOL,传输协议,HTTP,HTTP,1,1
PROTOCOL,传输协议,HTTPS,HTTPS,2,2
PROTOCOL,传输协议,FTP,FTP,3,3
PROTOCOL,传输协议,FTPS,FTPS,4,4
PROTOCOL,传输协议,FILE,FILE,5,5
PROTOCOL,传输协议,OSS,OSS,6,6

TEST_TYPE,考试类型,NORMAL,正式考试,1,1
TEST_TYPE,考试类型,DEMO,试考,2,2

TEST_MODE,考试模式,UNIFIED_SF,为统一开考统一结束,1,1
TEST_MODE,考试模式,UNIFIED_S,统一开考不统一结束,2,2
TEST_MODE,考试模式,READY_S,随报随考,4,4

TEST_RENDER,开考时间呈现方式,SHOW_S,仅显示最早开考日期时间,1,1
TEST_RENDER,开考时间呈现方式,SHOW_SF,显示开考起止日期或时间,3,3

TEST_STATUS,考试状态,S000,初始状态,0x000,0
TEST_STATUS,考试状态,S001,考试发布,0x001,1
TEST_STATUS,考试状态,S011,考中状态,0x011,17
TEST_STATUS,考试状态,S101,考前结束,0x101,257
TEST_STATUS,考试状态,S111,考中结束,0x111,273
TEST_STATUS,考试状态,S121,自动结束,0x121,289

TEST_PUBLISH_STATUS,考试发布状态,S0,初始状态,0x0,0
TEST_PUBLISH_STATUS,考试发布状态,S1,发布状态,0x1,1

TEST_TIME_STATUS,考试时间状态,S0,考前状态,0x0,0
TEST_TIME_STATUS,考试时间状态,S1,考中状态,0x1,1
TEST_TIME_STATUS,考试时间状态,S2,考后状态,0x2,2

TEST_CLOSE_STATUS,考试关闭状态,YES,考试已结束,0x1,1
TEST_CLOSE_STATUS,考试关闭状态,NO,考试未结束,0x0,0

TEST_SCORE_STATUS,考试判分状态,S0,无判分状态,0x0,0
TEST_SCORE_STATUS,考试判分状态,S1,部分判分状态,0x1,1
TEST_SCORE_STATUS,考试判分状态,S2,完成判分状态,0x2,2

TEST_START_STATUS,考试开考状态,ALLOW,允许开始考试,1,1
TEST_START_STATUS,考试开考状态,BEFORE_STIME,未到开考时间,11,11
TEST_START_STATUS,考试开考状态,AFTER_STIME,已超过最晚开考时间,12,12
TEST_START_STATUS,考试开考状态,SESSION_START,当前场次已开考,21,21
TEST_START_STATUS,考试开考状态,FINISH,考试已结束,26,26

TEST_UPLOAD_TYPE,上传的考试包数据类型,TYPE1,表示统一结束的场次，时间到达预定结束时间，自动交卷,1,1
TEST_UPLOAD_TYPE,上传的考试包数据类型,TYPE2,表示统一结束的场次，试卷考完早于预定结束时间，以试卷时长为准，自动交卷,2,2
TEST_UPLOAD_TYPE,上传的考试包数据类型,TYPE3,表示非统一结束场次，以试卷时长为准，时间走完，自动交卷,3,3
TEST_UPLOAD_TYPE,上传的考试包数据类型,TYPE4,表示可允许提前交卷的场次，点击自动交卷，自动交卷,4,4
TEST_UPLOAD_TYPE,上传的考试包数据类型,TYPE5,表示在考试列表中，主动交卷，自动交卷,5,5

TEST_SHOW_SCORE,是否显示分数,YES,显示,1,1
TEST_SHOW_SCORE,是否显示分数,NO,不显示,0,0

DATA_UPLOADED_STATUS,答案上传状态,YES,上传成功,1,1
DATA_UPLOADED_STATUS,答案上传状态,NO,上传失败,0,0

DATA_VERIFIED_STATUS,答案校验状态,YES,校验成功,1,1
DATA_VERIFIED_STATUS,答案校验状态,NO,校验失败,0,0

ERROR_CODE,错误码,E00001,未知/未捕获的异常,1,1
ERROR_CODE,错误码,E00002,数据库连接错误,2,2
ERROR_CODE,错误码,E00003,服务器错误,3,3
ERROR_CODE,错误码,E00004,SQL语句错误,4,4
ERROR_CODE,错误码,E00100,请求MTS Online服务器的令牌（token）不存在或已过期,100,100
ERROR_CODE,错误码,E01002,Test Number不存在,1002,1002
ERROR_CODE,错误码,E01003,不支持该设备类型,1003,1003
ERROR_CODE,错误码,E01005,Token不存在,1005,1005
ERROR_CODE,错误码,E02211,考试试卷包不存在,2211,2211
ERROR_CODE,错误码,E02212,考试试卷包未发布,2212,2212
ERROR_CODE,错误码,E02216,考试试卷包密码未发布,2216,2216
ERROR_CODE,错误码,E02217,考试试卷包密码已关闭,2217,2217
ERROR_CODE,错误码,E02501,不允许上传考试数据包,2501,2501
ERROR_CODE,错误码,E02502,已上传成功了，不需要再重复上传,2502,2502
ERROR_CODE,错误码,E08001,登录错误:准考证号错误或不存在,8001,8001
ERROR_CODE,错误码,E08002,登录错误:考试已结束,8002,8002
ERROR_CODE,错误码,E08003,登录错误:已在另一台设备中重复登录,8003,8003
ERROR_CODE,错误码,E08004,登录错误:迟到,8004,8004
ERROR_CODE,错误码,E08005,登录错误:该设备已登录，请确认输入的准考证号,8005,8005


EOD;

$dict = new SysDict;
$dict->importContent($content);