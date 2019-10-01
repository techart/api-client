<?php

namespace Techart\API;

class Results
{
	public static function get($id)
	{
		return Cache::get("results-{$id}.json", function() use($id) {
			return API::getStruct("results/{$id}.json");});
	}
}