<?php

namespace JNMFW\helpers;

abstract class HLog {
	static private $file;
	
	static public function setLogFile($file) {
		static::$file = $file;
	}
	
	static public function logError($msg) {
		static::log('ERROR', $msg);
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
