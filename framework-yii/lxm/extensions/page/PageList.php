<?php

class PageList extends COutputProcessor
{
	public $model;
	public $modelKey='id';
	public $pageKey = 'page';
	public $addAction = 'add';
	public $viewAction = 'view';
	public $editAction = 'edit';
	public $delAction = 'del';
	public $action;
	public $params=array();
	public $with=array();
	public $pageSize = 10;
	public $view;
	public $search = array();
	public $data=array();
	public $navnum = 5;
	public $label_prev = "上一页";
	public $label_next = "下一页";
	public $label_stat = "共 %s 页  记录%d条 第 %s 页";
	public $label_submit = "确定";
	public $formid;
	public $page_class = array(
		'page_nav'=>'page-nav',
		'btn_prev'=>'btn-left',
		'btn_prev_end'=>'btn-left-end',
		'btn_cur'=>'cur',
		'btn_next'=>'btn-right',
		'btn_next_end'=>'btn-right-end',
		'input'=>'inp-page'
	);
	public function init()
	{
		$queryString = Yii::app()->request->queryString;
		parse_str($queryString, $params);
		foreach ($this->search as $field=>$name) {
			$this->data['search'][$field]['name'] = $name;
			$this->data['search'][$field]['value'] = isset($params[$field])?$params[$field]:null;
		}
		$this->data['pageSize'] = $this->pageSize>0?$this->pageSize:10;
		$this->data['page'] = isset($params['page'])?$params['page']:1;
		if ($this->model instanceof CActiveRecord) {
			$tables = array();
			$compare = array();
			foreach ((array)$this->data['search'] as $field=>$val) {
				$compare[]=array(
					'field'=>$field,
					'name'=>$val['name'],
					'value'=>$val['value']
				);
				if (($pos=strpos($val['name'], '.')) !== false) {
					$table = substr($val['name'], 0, $pos);
					if ($table != 't')
						$tables[] = $table;
				}
			}
			$criteria=new CDbCriteria;
			$tables = array_merge($tables, $this->with);
			if (!empty($tables))
				$criteria->with = $tables;

			foreach ((array)$this->params as $k=>$v) {
				$criteria->{$k} = $v;
			}

			foreach ($compare as $k=>$v) {
				$key = $v['name'];
				$val = $v['value'];
				if (!empty($tables)) {
					if (($pos=strpos($key, '.')) === false) {
						$key = 't.'.$key;
					}
				}
				$criteria->compare($key, $val, true);
			}
			$currentPage = isset($params['page']) && ((int)$params['page'] - 1 >= 0)?(int)$params['page'] - 1:0;
			$dataProvider = new CActiveDataProvider($this->model, array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>$this->data['pageSize'],
					'currentPage'=>$currentPage
				)
			));
			if (!$this->formid)
				$this->formid = get_class($this->model).'-form';
			$this->data['formid'] = $this->formid;
			$this->data['model'] = $this->model;
			$this->data['rowset'] = $dataProvider->getData();
			$this->data['count'] = $dataProvider->getTotalItemCount();
			$this->data['pageCount'] = $dataProvider->getPagination()->getPageCount();
			$this->data['page'] = $dataProvider->getPagination()->getCurrentPage();
			$this->data['owner'] = $this;
			$this->data['pageContent'] = $this->getPageContent($this->data['page']+1, $this->data['pageCount'],$this->data['count']);

		}
		parent::init();
	}
	public function label($attribute, $htmlOptions=array())
	{
		return CHtml::activeLabel($this->model, $attribute, $htmlOptions);
	}
	public function dropDownList($attribute,$data,$htmlOptions=array())
	{
		return CHtml::dropDownList($attribute,$data,$htmlOptions);
	}
	public function textField($attribute,$htmlOptions=array())
	{
		$value = isset($this->data['search'][$attribute]['value'])?$this->data['search'][$attribute]['value']:null;
		return CHtml::textField($attribute, $value, $htmlOptions);
	}
	public function processOutput($output)
	{
		$output=CHtml::beginForm($this->action, 'GET', array('id'=>$this->formid));
		$output.=$this->decorate($output);
		$output.=CHtml::endForm();
		parent::processOutput($output);
	}
	protected function getPageContent($page, $pagecount, $datacount)
	{
		$url = Yii::app()->request->requestUri;
		$params=array(
			'class'=>'page-nav',
			'label_prev' => '上一页',
			'label_next' => '下一页',
			'label_stat' => '共 %s 页  记录%d条 第 %s 页',
			'label_submit' => '确定',
			'navnum'=>5,
			'page_arg'=>'page',
		);
		return get_page_nav_html($url, $page, $pagecount, $datacount, $params);
	}
	protected function renderScript()
	{
		$cs = Yii::app()->getClientScript();
		$assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
		$cs->registerCssFile($assets.'/page.css');
	}
	protected function decorate($content)
	{
		$owner=$this->getOwner();
		$viewFile = false;
		if ($this->view)
			$viewFile=$owner->getViewFile($this->view);
		if($viewFile!==false)
		{
			$data=$this->data;
			return $owner->renderFile($viewFile,$data,true);
		}
		else
			return $content;
	}
}