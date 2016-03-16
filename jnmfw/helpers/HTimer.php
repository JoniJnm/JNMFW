<?php

namespace JNMFW\helpers;

abstract class HTimer
{
	static private $times = array();

	static public function init($name)
	{
		self::$times[$name] = microtime(true);
	}

	static public function end($name, $msg = '')
	{
		$time = round(self::get($name), 4);
		$line = $name . ' - ' . $time . ' msecs';
		if ($msg) {
			$line .= ' - ' . $msg;
		}
		HLog::verbose($line);
	}

	static public function get($name)
	{
		return microtime(true) - self::$times[$name];
	}
}
