<?php

namespace JNMFW\classes;

use JNMFW\classes\databases\DBFactory;
use JNMFW\helpers\HLang;
use JNMFW\helpers\HLog;

class Server extends Singleton
{
	//http://php.net/manual/function.exit.php
	private $PROCESS_STATUS_END_ERROR_NUMBER = 255;

	private $status_codes = array(
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

	private function sendStatus($statusCode) {
		if (isset($this->status_codes[$statusCode])) {
			$status_string = $statusCode . ' ' . $this->status_codes[$statusCode];
			if (php_sapi_name() != 'cli') {
				\header(filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' ' . $status_string, true, $statusCode);
			}
		}
		else {
			$this->sendServerError("No existe el status " . $statusCode);
		}
	}

	/**
	 * @return Server
	 */
	public static function getInstance() {
		return parent::getInstance();
	}

	public function sendOK() {
		$this->sendResponse(
			new Response()
		);
	}

	/**
	 * @param Response $response
	 */
	public function sendResponse(Response $response) {
		$status = $response->getStatus();
		$this->sendStatus($status);

		if ($status >= 100 && $status < 400) {
			$this->sendJSON($response->getOutputData());
			$this->closeSuccess();
		}
		else {
			$response->put('http_status', array(
				'code' => $status,
				'message' => $this->status_codes[$status]
			));
			$this->sendJSON($response->getOutputData());
			$this->closeError();
		}
	}

	public function sendNotFound($msg_log = null) {
		if ($msg_log) {
			HLog::error($msg_log);
		}
		$this->sendResponse(
			(new Response())
			->setStatus(StatusCodes::HTTP_NOT_FOUND)
		);
	}

	public function sendServerError($msg_log) {
		HLog::error($msg_log);
		$this->sendResponse(
			(new Response())
				->setStatus(StatusCodes::HTTP_INTERNAL_SERVER_ERROR)
		);
	}

	public function sendInvalidParameter($param, $log = true) {
		$msg_user = HLang::translate('Parameter {param_name} invalid',
			array(
				'param_name' => strtoupper($param)
			));
		$this->sendInvalidRequest($param, $msg_user, $log);
	}

	public function sendInvalidRequest($param, $msg_user, $log = true) {
		if ($log) {
			HLog::error($msg_user);
		}

		$this->sendResponse(
			(new Response())
				->setStatus(StatusCodes::HTTP_PRECONDITION_FAILED)
				->setDialogError($msg_user)
				->put('invalid_param', $param)
		);
	}

	public function sendConflict($msg_user, $errno = null) {
		HLog::error($msg_user);

		$response = (new Response())
			->setStatus(StatusCodes::HTTP_CONFLICT)
			->setDialogError($msg_user);

		if ($errno) {
			$response->put('errno', $errno);
		}

		$this->sendResponse($response);
	}

	public function sendSessionTimeout() {
		$this->sendStatus(419, true);
	}

	public function sendUnauthorized() {
		$this->sendStatus(401, true);
	}

	public function sendForbidden($msg_user = null) {
		$response = (new Response())
			->setStatus(StatusCodes::HTTP_FORBIDDEN)
			->setDialogError($msg_user);

		if ($msg_user) {
			$response->setDialogError($msg_user);
		}

		$this->sendResponse($response);
	}

	public function sendData($data) {
		$this->sendResponse(
			new Response($data)
		);
	}

	private function sendJSON($data) {
		\header('Content-type: application/json');
		echo \json_encode($data);
	}

	private function transactionCommit() {
		DBFactory::commitAllConnections();
	}

	private function transactionRollback() {
		DBFactory::rollbackAllConnections();
	}

	private function closeSuccess() {
		$this->transactionCommit();
		exit;
	}

	private function closeError() {
		HLog::warning('Close error', true);
		$this->transactionRollback();
		exit($this->PROCESS_STATUS_END_ERROR_NUMBER);
	}
}
