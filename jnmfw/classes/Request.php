<?php

namespace JNMFW\classes;

/**
 * Clase para controlar de manera sencilla las variables $_GET, $_POST y $_COOKIE
 * 
 * Si no se accede a una propiedad específica de la clase (cookie, server)
 * por defecto el filtro se realizará sobre merge($_GET, $_POST)
 */

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
		parent::__construct(\array_merge($_GET, $_POST));
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
}