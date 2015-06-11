<?php

Yii::import('mts.extensions.echarts.Chart');

class ChartMap extends Chart
{
	public $usemap = true;
	public function init()
	{
		$this->addMod('echarts/chart/map');
		parent::init();
	}
}