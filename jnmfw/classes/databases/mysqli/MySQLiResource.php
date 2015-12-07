<?php

namespace JNMFW\classes\databases\mysqli;

/**
 * Clase para adaptar un recurso devuelto por mysql_connect para ser usado como un recurso de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli-result.php
 */

class MySQLiResource implements \JNMFW\classes\databases\DBResource {
	/**
	 * El recurso mysql
	 * @var mysqli_result
	 */
	protected $res;
	
	/**
	 * Constructor para instanciar esta clase y usarla como si fuera de tipo mysqli_result
	 * @param mysqli_result $resource
	 */
	public function __construct($resource) {
		$this->res = $resource;
	}
	
	public function __destruct() {
		$this->free();
	}
	
	public function fetch_object() {
		return $this->res->fetch_object();
	}
	
	public function fetch_row() {
		return $this->res->fetch_row();
	}
	
	public function fetch_array() {
		return $this->res->fetch_array(\MYSQLI_ASSOC);
	}
	
	public function getNumRows() {
		return $this->res->num_rows;
	}
	
	public function free() {
		$this->res->free();
	}
}