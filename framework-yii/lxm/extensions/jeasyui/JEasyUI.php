<?php

abstract class JEasyUI extends CWidget
{
	public $theme='default';
	public $scriptFile='';
	public static function publish()
	{
		return Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
	}
	public static function registerScript()
	{
		$jeasyui = JeasyUI::publish();
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($jeasyui.'/jquery.easyui.min.js');
		$cs->registerCssFile($jeasyui.'/themes/default/easyui.css');
		$cs->registerCssFile($jeasyui.'/themes/icon.css');
	}

}