<?php

namespace Techart\API;

class Models
{
	public static function get($name)
	{
		$name = str_replace('\\', '-', trim($name, '\\'));
		return API::get("model-export/{$name}");
	}
}
