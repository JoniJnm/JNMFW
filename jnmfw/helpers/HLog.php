<?php

namespace JNMFW\helpers;

abstract class HLog {
	const LEVEL_NONE = 0;
	const LEVEL_VERBOSE = 1;
	const LEVEL_DEBUG = 2;
	const LEVEL_WARNING = 3;
	const LEVLE_ERROR = 4;
	
	static private $level = 2;
	static private $file;
	
	static public function setLogFile($file) {
		static::$file = $file;
	}
	
	static public function setLevelError($level) {
		static::$level = $level;
	}
	
	static public function logError($msg) {
		$trace = array();
		foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $t) {
			if (isset($t['file']))
				$trace[] = $t['function'].'() at '.basename($t['file']).':'.$t['line'];
			else
				$trace[] = $t['function'].'()';
		}
		static::log(static::LEVEL_ERROR, 'ERROR', $msg.' - '.implode(', ', $trace));
	}
	
	static public function logWarning($msg) {
		static::log(static::LEVEL_WARNING, 'WARNING', $msg);
	}
	
	static public function logDebug($msg) {
		static::log(static::LEVEL_DEBUG, 'DEBUG', $msg);
	}
	
	static public function logVerbose($msg) {
		static::log(static::LEVEL_VERBOSE, 'VERBOSE', $msg);
	}
	
	static private function log($level, $label, $msg) {
		if ($level < static::$level) return;
		$log = date('d-m H:i:s').' - '.$label.' - '.$msg;
		if (static::$file) file_put_contents(static::$file, $log."\n", FILE_APPEND);
		else echo $log." <br />\n";
	}
}
