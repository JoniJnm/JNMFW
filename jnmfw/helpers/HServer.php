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
			self::sendServerError("No existe el status ".$statusCode);
		}
	}
	
	static public function sendOK() {
		self::transactionCommit();
		self::sendJSON(null);
		self::close();
	}
	
	static public function sendNotFound($msg_log) {
		self::transactionRollback();
		HLog::logError($msg_log);
		self::sendStatus(404, true);
	}
	
	static public function sendServerError($msg_log) {
		self::transactionRollback();
		HLog::logError($msg_log);
		self::sendStatus(500, true);
	}
	
	static public function sendInvalidRequest($msg_key, $param) {
		self::transactionRollback();
		$msg = HLang::get($msg_key);
		HLog::logError($msg);
		self::sendStatus(412);
		$data = array('msg' => $msg, 'invalid_params' => $param);
		self::sendJSON($data);
		self::close();
	}
	
	static public function sendConflict($msg_user, $errno = null) {
		self::transactionRollback();
		HLog::logError($msg_user);
		self::sendStatus(409);
		$data = array('msg' => $msg_user);
		if ($errno) $data['errno'] = $errno;
		self::sendJSON($data);
		self::close();
	}
	
	static public function sendSessionTimeout() {
		self::transactionRollback();
		self::sendStatus(419, true);
	}
	
	static public function sendUserNotVerified() {
		self::transactionRollback();
		self::sendStatus(403, true);
	}
	
	static public function sendForbidden($msg_user = null) {
		self::transactionRollback();
		if ($msg_user) {
			self::sendStatus(403);
			$data = array('msg' => $msg_user);
			self::sendJSON($data);
			self::close();
		}
		else {
			self::sendStatus(403, true);
		}
	}
	
	static public function sendData($data) {
		self::transactionCommit();
		self::sendJSON($data);
		self::close();
	}
	
	static private function sendJSON($data) {
		\header('Content-type: application/json');
		echo \json_encode($data, \JSON_NUMERIC_CHECK);
	}
	
	static private function transactionCommit() {
		DBFactory::getInstance()->transactionCommit();
	}
	
	static private function transactionRollback() {
		DBFactory::getInstance()->transactionRollback();
	}
	
	static private function close() {
		exit;
	}
}