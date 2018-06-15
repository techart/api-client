<?php

namespace Techart\API;

class Auth
{
	public static function getData($code)
	{
		return API::getStruct("/auth-data/{$code}/");
	}
}