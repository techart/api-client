<?php

namespace Techart\API;

class Vita
{
	public static function query($params = array())
	{
		$query = http_build_query($params);
		return API::getStruct("/vita.json?{$query}");
	}
}