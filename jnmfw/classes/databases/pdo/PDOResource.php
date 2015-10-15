<?php

namespace JNMFW\classes\databases\pdo;

/**
 * Clase para adaptar un recurso devuelto por mysql_connect para ser usado como un recurso de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli-result.php
 */

class PDOResource implements \JNMFW\classes\databases\DBResource {
	/**
	 * El recurso pdo
	 * @var \PDOStatement
	 */
	protected $res;
	
	/**
	 * Constructor para instanciar esta clase y usarla como si fuera de tipo mysqli_result
	 * @param \PDOStatement $resource
	 */
	public function __construct(\PDOStatement $resource) {
		$this->res = $resource;
	}
	
	public function fetch_object() {
		return $this->res->fetchObject();
	}
	
	public function fetch_row() {
		return $this->res->fetch(\PDO::FETCH_ASSOC);
	}
	
	public function fetch_array() {
		return $this->res->fetch(\PDO::FETCH_NUM);
	}
	
	public function getNumRows() {
		return $this->res->rowCount();
	}
	
	public function free() {
		$this->res->closeCursor();
	}
}