<?php

namespace Techart\API;

class Results
{
	public static function get($id)
	{
		return Cache::get("results-{$id}.json", function() use($id) {
			return API::getStruct("results/{$id}.json");
		});
	}

	public static function getCase($id)
	{
		return Cache::get("case-{$id}.json", function() use($id) {
			return API::getStruct("portfolio/case/{$id}/");
		});
	}

	public static function getPortfolio($recipient, $date)
	{
		if ($date) {
			return API::getStruct("/portfolio/{$recipient}/{$date}/");
		}
		return API::getStruct("/portfolio/{$recipient}/");
	}
}