<?php
/**
 * 示例:
 *	$this->Widget('mts.extensions.ztree.JzTree', array(
 *		'id'=>'testtree',
 *		'setting'=>array('check'=>array('enable'=>true)),
 *		'data'=>array(
 *			array('name'=>'baidu',url=>"http://www.baidu.com", 'target'=>'_self','open'=>true,'children'=>array(array('name'=>'子1'),array('name'=>'子2'))),
 *			array('name'=>'父节点2', 'open'=>true,'children'=>array(array('name'=>'子1'),array('name'=>'子2'))),
 *		)
 *	));
 */


class JzTree extends CWidget
{
	public $id;
	public $data;
	public $setting;
	public $userjs;
	public $htmlOptions = array();

	public function init()
	{
		if (!$this->data) $this->data = array();
		if (!$this->setting) $this->setting = array();
		return parent::init();
	}
	public function run()
	{
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
		$cs->registerCssFile($assets.'/css/zTreeStyle.css');
		$cs->registerScriptFile($assets.'/js/jquery.ztree.all-3.5'.(YII_DEBUG?'':'.min').'.js',CClientScript::POS_END);
		if ($this->userjs)
			$cs->registerScript(__CLASS__."#".$this->id."_user_script", $this->userjs, CClientScript::POS_END);
		$datastr = json_encode($this->data);
		$settingstr = $this->setting;
		$js = "var setting = ".$settingstr.";\n";
		$js .= "var zNodes=".$datastr.";\n";
		$js .= "\$.fn.zTree.init(\$(\"#".$this->id."\"), setting, zNodes);";
		$cs->registerScript(__CLASS__."#".$this->id."_ready_script", $js);
		$this->htmlOptions['id'] = $this->id;
		$this->htmlOptions['class'] = 'ztree';
		echo CHtml::OpenTag("ul", $this->htmlOptions);
		echo CHtml::CloseTag("ul");
	}
}