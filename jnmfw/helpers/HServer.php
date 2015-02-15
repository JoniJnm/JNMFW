<?php

namespace JNMFW\helpers;

use JNMFW\classes\databases\DBFactory;

abstract class HServer {
	private static $status_codes = array (
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
			419 => 'Authentication Timeout',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        );
	
	static private function sendStatus($statusCode, $close = false) {
		if (isset(self::$status_codes[$statusCode])) {
			$status_string = $statusCode . ' ' . self::$status_codes[$statusCode];
			\header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
			if ($close) {
				if ($statusCode >= 300) echo $statusCode.' '.self::$status_codes[$statusCode];
				self::close();
			}
		}
		else {
			static::sendServerError("No existe el status ".$statusCode);
		}
	}
	
	static public function sendOK() {
		DBFactory::getInstance()->transaccionCommit();
		static::sendJSON(null);
		self::close();
	}
	
	static public function sendNotFound($msg) {
		DBFactory::getInstance()->transaccionRollback();
		HLog::logError($msg);
		static::sendStatus(404, true);
	}
	
	static public function sendServerError($msg) {
		DBFactory::getInstance()->transaccionRollback();
		HLog::logError($msg);
		static::sendStatus(500, true);
	}
	
	static public function sendInvalidRequest($msg_key, $param) {
		DBFactory::getInstance()->transaccionRollback();
		$msg = HLang::get($msg_key);
		HLog::logError($msg);
		static::sendStatus(412);
		$data = array('msg' => $msg, 'invalid_params' => $param);
		static::sendJSON($data);
		self::close();
	}
	
	static public function sendConflict($msg, $errno = null) {
		DBFactory::getInstance()->transaccionRollback();
		HLog::logError($msg);
		static::sendStatus(409);
		$data = array('msg' => $msg);
		if ($errno) $data['errno'] = $errno;
		static::sendJSON($data);
		self::close();
	}
	
	static public function sendSessionTimeout() {
		DBFactory::getInstance()->transaccionRollback();
		static::sendStatus(419);
		self::close();
	}
	
	static public function sendUserNotVerified() {
		DBFactory::getInstance()->transaccionRollback();
		static::sendStatus(403);
		self::close();
	}
	
	static public function sendForbidden($msg = null) {
		DBFactory::getInstance()->transaccionRollback();
		static::sendStatus(403);
		if ($msg) {
			$data = array('msg' => $msg);
			static::sendJSON($data);
		}
		self::close();
	}
	
	static public function sendData($data) {
		DBFactory::getInstance()->transaccionCommit();
		static::sendJSON($data);
		self::close();
	}
	
	static private function sendJSON($data) {
		\header('Content-type: application/json');
		echo \json_encode($data, \JSON_NUMERIC_CHECK);
	}
	
	static private function close() {
		exit;
	}
}