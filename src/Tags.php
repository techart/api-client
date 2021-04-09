<?php

namespace Techart\API;

class Tags
{
	public static function getAll()
	{
		return Cache::get('tags.json', function() {
			return API::getStruct('tags.json');
		});
	}

	public static function get($name)
	{
		return Cache::get("tags-{$name}.json", function() use($name) {
			return API::getStruct("tags/{$name}.json");
		});
	}
}
