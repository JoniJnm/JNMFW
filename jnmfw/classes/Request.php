<?php

namespace JNMFW\classes;

use JNMFW\helpers\HServer;

class Request extends Filter
{
	/**
	 * Filtro sobre $_COOKIE
	 * @var Filter
	 */
	public $cookie;

	/**
	 * Filtro sobre $_SERVER
	 * @var Filter
	 */
	public $server;

	/**
	 * @var Request
	 */
	private static $instance = null;

	private $method;

	/**
	 * Desde fuera llamar a getInstance()
	 */
	public function __construct($_ = null) {
		$method = filter_input(INPUT_POST, '_method');
		if (!$method) {
			$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
		}
		$method = strtolower($method);
		$this->method = $method;
		if ($method == 'put') {
			$data = null;
			parse_str(file_get_contents("php://input"), $data);
			if (!$data) {
				$data = array();
			}
		}
		elseif ($method == 'get') {
			$data = $_GET;
		}
		elseif ($method == 'post') {
			$data = \array_merge($_GET, $_POST);
		}
		else {
			$data = array();
		}
		parent::__construct($data);
		$this->cookie = new Filter($_COOKIE);
		$this->server = new Filter($_SERVER);
	}

	public function getMethod() {
		return $this->method;
	}

	public function getFile($key) {
		if (!isset($_FILES[$key]['error']) || is_array($_FILES[$key]['error'])) {
			if ($this->isStrict()) {
				HServer::sendInvalidParam($key);
			}
			return null;
		}
		$this->checkError($_FILES[$key]['error']);
		return $_FILES[$key]['tmp_name'];
	}

	public function getFiles($key) {
		if (!isset($_FILES[$key]['error']) || !is_array($_FILES[$key]['error'])) {
			if ($this->isStrict()) {
				HServer::sendInvalidParam($key);
			}
			return array();
		}
		foreach ($_FILES[$key]['error'] as $error) {
			$this->checkError($error);
		}
		return $_FILES[$key]['tmp_name'];
	}

	private function checkError($error) {
		switch ($error) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new \RuntimeException('No file sent');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new \RuntimeException('Exceeded filesize limit');
			default:
				throw new \RuntimeException('Unknown errors');
		}
	}

	/**
	 * Obtiene la instacia del Objeto singleton Request
	 * El filtro principal es merge($_GET, $_POST)
	 * @return Request
	 */
	public static function getInstance() {
		if (static::$instance === null) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	public function setStrictMode($strict) {
		parent::setStrictMode($strict);
		$this->cookie->setStrictMode($strict);
		$this->server->setStrictMode($strict);
	}
}