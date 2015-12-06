<?php

namespace JNMFW\helpers;

abstract class HLog {
	const LEVEL_VERBOSE = 0;
	const LEVEL_DEBUG = 1;
	const LEVEL_WARNING = 2;
	const LEVEL_ERROR = 3;
	const LEVEL_NONE = 4;
	
	static private $level = 2;
	static private $file;
	static private $displayErrors = false;
	
	static public function setFile($file) {
		self::$file = $file;
	}
	
	static public function setDisplayErrors($display) {
		self::$displayErrors = (bool)$display;
	}
	
	static public function setLevel($level) {
		self::$level = $level;
	}
	
	static public function error($msg) {
		$trace = array();
		foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $t) {
			if (isset($t['file']))
				$trace[] = $t['function'].'() at '.basename($t['file']).':'.$t['line'];
			else
				$trace[] = $t['function'].'()';
		}
		self::log(static::LEVEL_ERROR, 'ERROR', $msg."\n".implode("\n", $trace));
	}
	
	static public function warning($msg) {
		self::log(static::LEVEL_WARNING, 'WARNING', $msg);
	}
	
	static public function debug($msg) {
		self::log(static::LEVEL_DEBUG, 'DEBUG', $msg);
	}
	
	static public function verbose($msg) {
		self::log(static::LEVEL_VERBOSE, 'VERBOSE', $msg);
	}
	
	static private function log($level, $label, $msg) {
		if ($level < self::$level) return;
		$microtime = explode(' ', microtime());
		$msecs = str_pad(floor($microtime[0] * 1000), 3, '0', STR_PAD_LEFT);
		$log = date('d-m H:i:s', $microtime[1]).'.'.$msecs.' - '.$label.' - '.$msg."\n";
		if (self::$file) {
			file_put_contents(self::$file, $log, FILE_APPEND);
		}
		if (self::$displayErrors) {
			if (php_sapi_name() == 'cli') {
				echo $log;
			}
			else {
				echo str_replace("\n", '<br />', $log);
			}
		}
	}
}
