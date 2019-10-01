<?php

namespace Techart\API;

class InfoChannels
{
	public static function countRows($query)
	{
		$count = 0;
		$query = self::parseQuery($query);
		foreach($query['sources'] as $source => $filters) {
			$count += self::countSource($source, $filters);
		}
		return $count;
	}
	
	public static function selectRows($query)
	{
		$rows = array();
		$query = self::parseQuery($query);
		foreach($query['sources'] as $source => $filters) {
			$r = self::selectSource($source, $filters, $query['offset'] + $query['limit']);
			if (is_array($r)) {
				foreach($r as $row) {
					$row = (array)$row;
					$rows[] = array(
						'id' => $row['id'],
						'key' => $source.':'.$row['id'],
						'date' => $row['?icdate'],
						'updated_at' => $row['?updated'],
						'title' => $row['?ictitle'],
					);
				}
			}
		}
		usort($rows, function($a, $b) {
			if ($a['date'] > $b['date']) {
				return -1;
			}
			if ($a['date'] < $b['date']) {
				return 1;
			}
			return 0;
		});
		return array_slice($rows, $query['offset'], $query['limit']);
	}
	
	public static function loadRow($address)
	{
	}
	
	public static function renderRow($address)
	{
	}
	
	public static function countSource($source, $filter)
	{
		if (preg_match('{^([a-z]+):(.+)$}i', $source, $m)) {
			$platform = $m[1];
			$table = $m[2];
			if ($platform == 'dna') {
				return DNA::from($table)->filter($filter)->count();
			}
		}
		return 0;
	}
	
	public static function selectSource($source, $filter, $limit = 100)
	{
		if (preg_match('{^([a-z]+):(.+)$}i', $source, $m)) {
			$platform = $m[1];
			$table = $m[2];
			if ($platform == 'dna') {dump(date('Y-m-d', 1541076493));
				return DNA::from($table)->filter($filter)->columns('id,?icdate,?ictitle,?updated')->limit($limit)->select();
			}
		}
		return array();
	}
	
	public static function parseQuery($query)
	{
		$limit = 0;
		$offset = 0;
		$data = array();
		$source = false;
		foreach(preg_split('{\s+}', $query) as $chunk) {
			if (preg_match('{^([a-z0-9_*]+)=(.+)$}i', $chunk, $m)) {
				$key = strtolower($m[1]);
				$value = $m[2];
				if ($value[0] == '[') {
					$value = json_decode($value);
				}
				if ($key == 'offset') {
					$offset = $value;
				}
				elseif ($key == 'limit') {
					$limit = $value;
				}
				elseif ($key[0] == '*') {
					$filter = substr($key, 1);
					if ($source) {
						$data[$source][$filter] = $value;
					}
				}
			}
			elseif ($chunk[0] == '!') {
				$source = substr($chunk, 1);
				if (!isset($data[$source])) {
					$data[$source] = array();
				}
			}
		}
		return array(
			'offset' => $offset,
			'limit' => $limit,
			'sources' => $data,
		);
	}
}