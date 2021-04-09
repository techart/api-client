<?php

namespace Techart\API;

class Persons
{
	public static function get($email)
	{
		return API::getStruct("persons/by-email/{$email}/");
	}

	public function getAll()
	{
		return API::getStruct("persons.json");
	}
}