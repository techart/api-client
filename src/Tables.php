<?php

namespace Techart\API;

class Tables
{
	protected static $serviceHost = 'dna.techart.ru';
	protected $project = false;
	protected $table = false;
	protected $error = false;
	protected $offset = false;
	protected $order = false;
	protected $limit = false;
	protected $filter = false;
	protected $columns = false;
	protected $exclude = false;

	public static function setServiceHost($value)
	{
		self::$serviceHost = $value;
	}

	public static function from($code)
	{
		$builder = new self();
		if (preg_match('{^([a-z0-9_]+)\.([a-z0-9_]+)$}i', $code, $m)) {
			$builder
				->setProject($m[1])
				->setTable($m[2])
			;
		}
		return $builder;
	}

	public function count()
	{
		$url = $this->url('count');
		$obj = API::get($url);
		if (!is_object($obj) || !isset($obj->count)) {
			return $obj;
		}
		return (int)$obj->count;
	}

	public function select()
	{
		$url = $this->url('select');
		$obj = API::get($url);
		if (!is_object($obj) || !isset($obj->rows)) {
			return $obj;
		}
		return $obj->rows;
	}

	public function find($id)
	{
		$url = $this->url('item', $id);
		$obj = API::get($url);
		if (!is_object($obj) || !isset($obj->item)) {
			return $obj;
		}
		return $obj->item;
	}

	protected function url($action, $id = false)
	{
		if (!$this->project || !$this->table) {
			return false;
		}

		$host = self::$serviceHost;
		$url = "http://{$this->project}.{$host}/api/{$action}/{$this->table}";
		if ($id) {
			$url .= "/{$id}";
		}
		$url .= '.json';
		$params = array();
		foreach(array('limit', 'offset', 'order', 'filter', 'columns', 'exclude', 'updated') as $param) {
			if ($this->$param ?? false) {
				$params[$param] = $this->$param;
			}
		}
		if ($params) {
			$url .= '?' . http_build_query($params);
		}

		return $url;
	}

	public function __call($method, $args)
	{
		if (preg_match('{^set(.+)$}', $method, $m)) {
			$property = strtolower($m[1]);
			$this->$property = $args[0];
			return $this;
		}
		if (preg_match('{^get(.+)$}', $method, $m)) {
			$property = strtolower($m[1]);
			return $this->$property;
		}
		if (in_array($method, array('offset', 'limit', 'order', 'filter', 'columns', 'exclude', 'updated'))) {
			$this->$method = $args[0];
			return $this;
		}
	}
}
