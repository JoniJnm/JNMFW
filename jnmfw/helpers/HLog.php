<?php

namespace JNMFW\helpers;

abstract class HLog
{
	const LEVEL_VERBOSE = 0;
	const LEVEL_DEBUG = 1;
	const LEVEL_WARNING = 2;
	const LEVEL_ERROR = 3;
	const LEVEL_NONE = 4;

	static private $level = 2;
	static private $file;
	static private $displayErrors = false;
	static private $infoAppendFunc;

	static public function setInfoAppendFunc($func) {
		self::$infoAppendFunc = $func;
	}

	static public function setFile($file) {
		self::$file = $file;
	}

	static public function setDisplayErrors($display) {
		self::$displayErrors = (bool)$display;
	}

	static public function setLevel($level) {
		self::$level = $level;
	}

	static public function exception($e, $level = HLog::LEVEL_ERROR) {
		$msg = $e->getMessage().' '.basename($e->getFile()).':'.$e->getLine();
		$trace = $e->getTrace();
		if ($level == HLog::LEVEL_ERROR) {
			self::error($msg, $trace);
		}
		elseif ($level == HLog::LEVEL_WARNING) {
			self::warning($msg, $trace);
		}
		elseif ($level == HLog::LEVEL_DEBUG) {
			self::debug($msg, $trace);
		}
		elseif ($level == HLog::LEVEL_VERBOSE) {
			self::verbose($msg, $trace);
		}
	}

	static public function error($msg, $trace = null) {
		if (!$trace && $trace !== false) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}
		self::log(static::LEVEL_ERROR, 'ERROR', $msg, $trace);
	}

	static public function warning($msg, $trace = null) {
		if ($trace === true) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}
		self::log(static::LEVEL_WARNING, 'WARNING', $msg, $trace);
	}

	static public function debug($msg, $trace = null) {
		if ($trace === true) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}
		self::log(static::LEVEL_DEBUG, 'DEBUG', $msg, $trace);
	}

	static public function verbose($msg, $trace = null) {
		if ($trace === true) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}
		self::log(static::LEVEL_VERBOSE, 'VERBOSE', $msg, $trace);
	}

	static private function traceToString($trace) {
		$calls = array();
		foreach ($trace as $t) {
			if (isset($t['file'])) {
				$calls[] = $t['function'] . '() at ' . basename($t['file']) . ':' . $t['line'];
			}
			else {
				$calls[] = $t['function'] . '()';
			}
		}
		return implode("\n", $calls);
	}

	static private function log($level, $label, $msg, $trace = null) {
		if ($level < self::$level) {
			return;
		}
		$microtime = explode(' ', microtime());
		$msecs = str_pad(floor($microtime[0] * 1000), 3, '0', STR_PAD_LEFT);
		$log = date('d-m H:i:s', $microtime[1]) . '.' . $msecs . ' - ' . $label . ' - ';
		if (self::$infoAppendFunc) {
			$func = self::$infoAppendFunc;
			$append = $func();
			if ($append) {
				$log .= $append . "\n";
			}
		}
		$log .= $msg . "\n";
		if ($trace) {
			$log .= self::traceToString($trace) . "\n\n";
		}
		if (self::$file) {
			file_put_contents(self::$file, $log, FILE_APPEND);
		}
		if (self::$displayErrors) {
			if (php_sapi_name() == 'cli') {
				echo $log;
			}
			else {
				echo nl2br($log);
			}
		}
	}
}
