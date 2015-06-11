<?php
Yii::import('mts.extensions.echarts.ECJson');
class OptionItem  extends CComponent implements IteratorAggregate
{
	protected $_name;
	protected $_attributes;
	protected $_config=array();
	public function __construct()
	{
		$this->_attributes = array();
		$this->initConfig($this->_config);
		$this->init();

	}
	public function __set($name,$value)
	{
		if($this->setAttribute($name,$value)===false)
			parent::__set($name,$value);
	}
	public function __get($name)
	{
		if(isset($this->_attributes[$name]))
			return $this->_attributes[$name]->value;
		else
			return parent::__get($name);
	}
	public function __isset($name)
	{
		if(isset($this->_attributes[$name]))
			return true;
		else
			return parent::__isset($name);
	}
	public function __unset($name)
	{
		if(isset($this->_attributes[$name]))
			$this->_attributes[$name]->value = null;
		else
			parent::__unset($name);
	}
	public function init()
	{
	}
	public function initConfig(array $config)
	{
		foreach ($config as $k=>$v) {
			$name = $default = $type = null;
			if (is_string($k)) $name = $k;
			if (is_array($v)) {
				if (isset($v['name']) $name=$v['name'];
				if (isset($v['type']) $type=$v['type'];
				if (isset($v['default']) $default=$v['default'];
			}
			if ($name && !isset($this->_attributes[$name])) {
				$oval = new OptionValue;
				$oval->name = $name;
				$oval->type = $type;
				$oval->default = $default;
				$this->_attributes[$name] = $oval;
			}
		}
	}
	public function setContent($content)
	{
		$value = trim($content);
		if (preg_match('/^\[.*\]$/s', $value)||preg_match('/^\{.*\}$/s', $value)) {
			$o = ECJson::decode($value);
			if ($o)	$value = $o;
		}
		if (is_object($value)) {
			$this->setAttributes($value);
		}

	}
	public function setAttributes($attributes)
	{
		foreach ($attributes as $k=>$v) {
			$this->setAttribute($k, $v);
		}
	}
	public function setAttribute($name,$value)
	{
		if (isset($this->_attributes[$name])) {
			if (is_string($value)) {
				$value = trim($value);
				if (preg_match('/^\[.*\]$/s', $value)||preg_match('/^\{.*\}$/s', $value)) {
					$o = ECJson::decode($value);
					if ($o)	$value = $o;
				}
			}
			$class = $this->loadClass($this->_attributes[$name]->type);
			if (is_object($value) && $class) {
				$obj = new $class;
				$obj->setAttributes($value);
				$this->_attributes[$name]->value = $obj;
			}
			else
				$this->_attributes[$name]->value = $value;

			return true;
		}
		return false;
	}
	public function getAttributes()
	{
		return array_keys($this->_attributes);
	}
	public function getIterator()
	{
		$attributes=$this->getAttributes();
		return new CMapIterator($attributes);
	}
	public function toArrayJson($array)
	{
		$ret = array();
		foreach ($array as $val) {
			if (is_array($val)) {
				$ret[] = $this->toArrayJson($val);
			}
			else if ($val instanceof OptionItem) {
				$ret[] = $val->toJson();
			}
			else {
				$ret[] = ECJson::encode($val);
			}
		}
		return '['.implode(',', $ret).']';
	}
	public function toJson()
	{
		$ret = array();
		foreach ($this->_attributes as $k=>$oval) {
			$v = $oval->value;
			if ($v === null) continue;
			if (is_array($v)) {
				$ret[] = $k.':'.$this->toArrayJson($v);
			}
			else if ($v instanceof OptionItem) {
				$ret[] = $k.':'.$v->toJson();
			}
			else {
				$ret[] = $k.':'.ECJson::encode($val);
			}
		}
		return '{'.implode(',', $ret).'}';
	}
	public function loadClass($type)
	{
		$class = 'Option'.ucfirst($type);
		if (!class_exists($class, false)) {
			if (is_file(dirname(__FILE__).'/'.$type.'.php')) {
				include_once(dirname(__FILE__).'/'.$type.'.php');
				if (!class_exists($class, false))
					$class = null;
			}
		}
		return $class;
	}
}

class
{
	public $name=null;
	public $type=null;
	public $default=null;
	public $value=null;
}