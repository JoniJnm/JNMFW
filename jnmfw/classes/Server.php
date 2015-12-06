<?php

namespace JNMFW\classes;

use JNMFW\classes\databases\DBFactory;

class Server extends Singleton {
	//http://php.net/manual/function.exit.php
	private $PROCESS_STATUS_END_ERROR_NUMBER = 255;
	
	private $status_codes = array (
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
	
	private function sendStatus($statusCode, $close = false) {
		if (isset($this->status_codes[$statusCode])) {
			$status_string = $statusCode . ' ' . $this->status_codes[$statusCode];
			if (php_sapi_name() != 'cli') {
				\header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
			}
			if ($close) {
				if ($statusCode >= 300) echo $statusCode.' '.$this->status_codes[$statusCode]."\n";
				$this->closeError();
			}
		}
		else {
			$this->sendServerError("No existe el status ".$statusCode);
		}
	}
	
	/**
	 * @return Server
	 */
	public static function getInstance() {
		return parent::getInstance();
	}
	
	public function sendOK() {
		$this->transactionCommit();
		$this->sendJSON(null);
		$this->close();
	}
	
	public function sendNotFound($msg_log) {
		$this->transactionRollback();
		HLog::error($msg_log);
		$this->sendStatus(404, true);
	}
	
	public function sendServerError($msg_log) {
		$this->transactionRollback();
		HLog::error($msg_log);
		$this->sendStatus(500, true);
	}
	
	public function sendInvalidRequest($msg_user, $param, $log = true) {
		$this->transactionRollback();
		if ($log) HLog::error($msg_user);
		$this->sendStatus(412);
		$data = array('msg' => $msg_user, 'invalid_param' => $param);
		$this->sendJSON($data);
		$this->closeError();
	}
	
	public function sendConflict($msg_user, $errno = null) {
		$this->transactionRollback();
		HLog::error($msg_user);
		$this->sendStatus(409);
		$data = array('msg' => $msg_user);
		if ($errno) $data['errno'] = $errno;
		$this->sendJSON($data);
		$this->closeError();
	}
	
	public function sendSessionTimeout() {
		$this->transactionRollback();
		$this->sendStatus(419, true);
	}
	
	public function sendUserNotVerified() {
		$this->transactionRollback();
		$this->sendStatus(403, true);
	}
	
	public function sendForbidden($msg_user = null) {
		$this->transactionRollback();
		if ($msg_user) {
			$this->sendStatus(403);
			$data = array('msg' => $msg_user);
			$this->sendJSON($data);
			$this->closeError();
		}
		else {
			$this->sendStatus(403, true);
		}
	}
	
	public function sendData($data) {
		$this->transactionCommit();
		$this->sendJSON($data);
		$this->close();
	}
	
	private function sendJSON($data) {
		\header('Content-type: application/json');
		echo \json_encode($data, \JSON_NUMERIC_CHECK);
	}
	
	private function transactionCommit() {
		DBFactory::commitAllConnections();
	}
	
	private function transactionRollback() {
		DBFactory::rollbackAllConnections();
	}
	
	private function close() {
		exit;
	}
	
	private function closeError() {
		exit($this->PROCESS_STATUS_END_ERROR_NUMBER);
	}
}
