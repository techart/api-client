<?php

namespace Techart\API;

class MasterInfo
{
	public static function getData($code)
	{
		if ($code == 'vita-divisions') {
			return Cache::get("master-info-{$code}.json", function() use ($code) {
				return API::getStruct("/divisions/");
			});
		}
		return Cache::get("master-info-{$code}.json", function() use ($code) {
			return API::getStruct("/master-info/{$code}/");
		});
	}

	public static function getText($code)
	{
		$data = self::getData($code);
		if ($data) {
			return $data->content;
		}
	}
}
