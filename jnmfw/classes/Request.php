<?php

namespace JNMFW\classes;

class Request extends Filter {
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
	
	/**
	 * Desde fuera llamar a getInstance()
	 */
	public function __construct($_ = null) {
		$method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
		if ($method == 'put') {
			$data = null;
			parse_str(file_get_contents("php://input"), $data);
			if (!$data) $data = array();
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