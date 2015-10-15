<?php

namespace JNMFW\classes\databases\pdo;

/**
 * Clase para adaptar el uso de PDO como si fuera una instancia de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli.php	
 */

class PDOAdapter implements \JNMFW\classes\databases\DBAdapter {
	/**
	 * Conexión PDO
	 * @var PDO
	 */
	private $conn;
	
	/**
	 * Error producido
	 * @var string
	 */
	private $error = null;
	
	/**
	 * @var PDOStatement 
	 */
	private $lastRes;
	
	public function __construct($dsn, $user, $pass, $options = array()) {
		$this->conn = new \PDO($dsn, $user, $pass, $options);
	}
	
	public function quote($value) {
		return $this->conn->quote($value);
	}
	
	public function query($query) {
		$res = $this->conn->query($query);
		$errno = $this->conn->errorCode();
		if ($errno) {
			$this->error = $this->conn->errorInfo().' ('.$errno.')';
			return false;
		}
		$this->error = null;
		$this->lastRes = $res;
		return new PDOResource($res);
	}
	
	public function getAffectedRows() {
		return $this->lastRes->rowCount();
	}
	
	public function getInsertedID() {
		return $this->conn->lastInsertId();
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function transactionBegin() {
		$this->conn->beginTransaction();
	}
	
	public function commit() {
		$this->conn->commit();
	}
	
	public function rollback() {
		$this->conn->rollBack();
	}
}