<?php

namespace JNMFW\classes;

/**
 * Clase para controlar de manera sencilla las variables $_GET, $_POST, $_COOKIE y $_SESSION
 * 
 * Si no se accede a una propiedad específica de la clase (get, post, cookie, server, all)
 * por defecto el filtro se realizará sobre $_GET y $_POST
 *
 */

class Request extends Filter {
	/**
	 * Filtro sobre $_GET
	 * @var Filter
	 */
	public $get;
	
	/**
	 * Fitro sobre $_POST
	 * @var Filter
	 */
	public $post;
	
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
	 * Filtro sobre $_SESSION
	 * @var Filter
	 */
	public $session;
	
	/**
	 * @var Request
	 */
	private static $instance = null;
	
	/**
	 * Desde fuera llamar a getInstance()
	 */
	public function __construct($data = null) {
		$this->setData(\array_merge($_POST, $_GET));
		$this->get = new Filter($_GET);
		$this->post = new Filter($_POST);
		$this->cookie = new Filter($_COOKIE);
		$this->server = new Filter($_SERVER);
		//$this->session = new Filter($_SESSION);
	}
	
	/**
	 * Obtiene la instacia del Objeto singleton Request
	 * @return Request
	 */
	public static function getInstance() {
		if (static::$instance === null) {
			static::$instance = new static;
		}
		return static::$instance;
	}
}