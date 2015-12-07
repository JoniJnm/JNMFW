<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\exceptions\JNMDBException;

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
	 * @var PDOStatement 
	 */
	private $lastRes;
	
	public function __construct($dsn, $user, $pass, $options = array()) {
		$this->conn = new \PDO($dsn, $user, $pass, $options);
	}
	
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * @return \PDO
	 */
	public function getNativeConnection() {
		return $this->conn;
	}
	
	public function quote($value) {
		if (is_null($value)) return 'NULL';
		elseif ($value === true) $value = 1;
		elseif ($value === false) $value = 0;
		return $this->conn->quote($value);
	}
	
	public function query($query) {
		$res = $this->conn->query($query);
		if (!$res) {
			throw new JNMDBException($this->conn->errorInfo().":\n".$query);
		}
		$this->lastRes = $res;
		return new PDOResource($res);
	}
	
	public function getAffectedRows() {
		return $this->lastRes->rowCount();
	}
	
	public function getInsertedID() {
		return $this->conn->lastInsertId();
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
	
	public function close() {
		$this->conn = null;
	}
}