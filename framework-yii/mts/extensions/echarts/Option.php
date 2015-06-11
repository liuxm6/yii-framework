<?php
include_once(dirname(__FILE__).'/ChartJSON.php');
class Option
{
	protected $_data;
	protected $_content;
	public function __construct($content=null)
	{
		$this->_content = $content;
		$str = trim($content);
		if (preg_match('/^\[.*\]$/s', $str)||preg_match('/^\{.*\}$/s', $str)) {
			$o = ChartJSON::decode($str);
			$this->_data = $o;
		}
	}
	public function getContent()
	{
		return $this->_content;
	}
	public function set($key, $value)
	{
		return $this->setValue($key, $value);
	}
	public function get($key)
	{
		return $this->getValue($key);
	}
	public function setValue($key, $value)
	{
		return $this->initItem($key, $value);
	}
	public function getValue($key)
	{
		return $this->initItem($key);
	}
	public function toJson()
	{
		return ChartJSON::encode($this->_data);
	}
	public function __toString()
	{
		return $this->toJson();
	}
	protected function initItem($key, $value=null)
	{
		$list = explode('.', $key);
		$data = &$this->_data;
		foreach ($list as $k) {
			if (is_array($data)) {
				if (is_numeric($k)) {
					if (isset($data[$k])) {
						$data = &$data[$k];
					}
					else {
						if ($value===null) return null;
						$len = count($data);
						$data[$len] = null;
						$data = &$data[$len];
					}
				}
				else if (is_string($k)) {
					return null;
				}
			}
			else if(is_object($data)) {
				if (is_numeric($k)) {
					return null;
				}
				else if (is_string($k)) {
					if (isset($data->$k)) {
						$data = &$data->$k;
					}
					else {
						if ($value===null) return null;
						$data->$k = null;
						$data = &$data->$k;
					}
				}
			}
			else if ($data===null) {
				if ($value===null) return null;
				if (is_numeric($k)) {
					$data=array(null);
					$data = &$data[0];
				}
				else if (is_string($k)) {
					$data = new stdclass;
					$data->$k = null;
					$data = &$data->$k;
				}
			}
		}
		if ($value !== null)
			$data = $value;
		return $data;
	}
}