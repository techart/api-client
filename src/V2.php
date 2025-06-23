<?php

namespace Techart\API;

class V2
{
	protected $resourceName = false;

	protected $params = [
		'offset' => 0,
		'limit' => 10,
	];

	public static function resource($name)
	{
		$resource = new self;
		return $resource->init($name);
	}

	public function init($name)
	{
		$this->resourceName = $name;
		return $this;
	}

	public function select()
	{
		$data = $this->query();
		return $data;
	}

	public function count()
	{
		$data = $this->query();
		$result = isset($data['result'])? $data['result'] : false;
		if ('ok' ==  $result) {
			return isset($data['count'])? $data['count'] : $data;
		}
		return $data;
	}

	public function limit($value)
	{
		$this->params['limit'] = $value;
		return $this;
	}

	public function offset($value)
	{
		$this->params['offset'] = $value;
		return $this;
	}

	public function where($param, $value = null)
	{
		if (is_array($param)) {
			foreach($param as $key => $value) {
				$this->addWhereParam($key, $value);
			}
		}

		else {
			return $this->addWhereParam($param, $value);
		}

		return $this;
	}

	public function addWhereParam($param, $value)
	{
		$this->params[$param] = $value;
		return $this;
	}

	public function query()
	{
		$query = http_build_query($this->params);
		$url = "/v2/select/{$this->resourceName}/?{$query}";
		return API::get($url, true);
	}
}