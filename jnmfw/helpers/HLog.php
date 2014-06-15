<?php

namespace JNMFW\helpers;

abstract class HLog {
	static private $file;
	
	static public function setLogFile($file) {
		static::$file = $file;
	}
	
	static public function logError($msg) {
		$trace = array();
		foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $t) {
			if (isset($t['file']))
				$trace[] = $t['function'].'() at '.basename($t['file']).':'.$t['line'];
			else
				$trace[] = $t['function'].'()';
		}
		static::log('ERROR', $msg.' - '.implode(', ', $trace));
	}
	
	static public function logWarning($msg) {
		static::log('WARNING', $msg);
	}
	
	static public function logDebug($msg) {
		static::log('DEBUG', $msg);
	}
	
	static public function logVerbose($msg) {
		static::log('VERBOSE', $msg);
	}
	
	static private function log($label, $msg) {
		$log = date('d-m H:i:s').' - '.$label.' - '.$msg;
		if (static::$file) file_put_contents(static::$file, $log."\n", FILE_APPEND);
		else echo $log." <br />\n";
	}
}
