<?php
Yii::import('mts.extensions.echarts.Option');
class Chart extends CWidget
{
	public $id='main';
	public $option;
	public $callback;
	public $htmlOptions=array('height'=>500,'width'=>900);
	public $userjs;
	public $usemap = false;
	public $usestr = true;
	protected $_mods=array('echarts','echarts/chart/bar',
			'echarts/chart/line','echarts/chart/scatter',
			'echarts/chart/k','echarts/chart/pie','echarts/chart/radar',
			'echarts/chart/force','echarts/chart/chord'
			);
	public function init()
	{
		if (!$this->option instanceof Option)
			$this->option = new Option($this->option);
		return parent::init();
	}
	public function addMod($mod)
	{
		$this->_mods[] = $mod;
	}
	public function publish()
	{
		return Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
	}
	public function registerScript()
	{
		$path = $this->publish();
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($path.'/js/esl/esl.js');
		$cs->registerScriptFile($path.'/js/codemirror.js');
		$cs->registerScriptFile($path.'/js/javascript.js');
		$cs->registerCssFile($path.'/css/bootstrap.css');
		$cs->registerCssFile($path.'/css/bootstrap-responsive.css');
		$cs->registerCssFile($path.'/css/codemirror.css');
		$cs->registerCssFile($path.'/css/monokai.css');
		$cs->registerCssFile($path.'/css/echartsHome.css');
		$patharr = $reqarr = array();
		$mapjs = $this->usemap ?'echarts-map':'echarts';
		foreach ($this->_mods as $k) {
			$patharr[] = "'$k':fileLocation";
			$reqarr[] = "'$k'";
		}
		$userjs = trim($this->userjs);
		$userjs = str_replace("\n", "\n				", $userjs);
		$option = $this->option;
		if ($option instanceof Option) {
			if ($this->usestr)
				$option = $option->getContent();
			else
				$option = $option->toJson();
		}
		$jscontent = "
	var option = ".$option.";
	var myChart;
	var fileLocation =  '".$path."/js/".$mapjs."';
	require.config({
		paths:{".implode(',', $patharr)."}
	});
	require(
		[".implode(',', $reqarr)."],
		function(echarts){
			if (myChart && myChart.dispose) {
				myChart.dispose();
			}
			myChart = echarts.init(document.getElementById('".$this->id."'));
			myChart.setOption(option, true);
			{$userjs}
		}
	);
";
		$cs->registerScript(__CLASS__.'#'.$this->id."_script", $jscontent);

	}
	public function run()
	{
		$this->registerScript();
		echo '<div id="'.$this->id.'" style="width:'.$this->htmlOptions['width'].'px;height: '.$this->htmlOptions['height'].'px;"></div>';
	}

}