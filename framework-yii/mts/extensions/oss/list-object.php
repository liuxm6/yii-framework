<?php
error_reporting(0);
require_once './ALIOSS.php';

//$oss_sdk_service = new ALIOSS('owem4i3ss31UGiPS','xpKYeiIhrLBU14MaQsFOCKLQfEe6Wi','oss-cn-hangzhou.aliyuncs.com');
$oss_sdk_service = new ALIOSS(null,null,'oss-cn-beijing.aliyuncs.com');

//设置是否打开curl调试模式
$oss_sdk_service->set_debug_mode(false);

$options = array(
	'delimiter' => '/',
	'prefix' => 'response/555220c16562656efb574e7580834059-3/556bf9da31663235656d04ef66993243-20/556bf9da31663235651f7df237258848-696/',
	'max-keys' => 100,
	'marker' => '',
);

$response = $oss_sdk_service->list_object('guiping',$options);


print_r($response->body);