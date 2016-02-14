<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\exceptions\JNMDBException;

/**
 * Clase para adaptar el uso de MySQL como si fuera una instancia de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli.php	
 */

class MySQLiAdapter implements \JNMFW\classes\databases\DBAdapter {
	/**
	 * Conexión MySQLi
	 * @var \mysqli
	 */
	private $conn;
	
	public function __construct($nativeConnection) {
		if ($nativeConnection instanceof \mysqli) {
			$this->conn = $nativeConnection;
		}
		else {
			throw new \InvalidArgumentException('The connection should be a mysqli object');
		}
	}
	
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * @return \mysqli
	 */
	public function getNativeConnection() {
		return $this->conn;
	}
	
	public function quote($value) {
		if (is_null($value)) return 'NULL';
		elseif ($value === true) $value = 1;
		elseif ($value === false) $value = 0;
		return "'".$this->conn->real_escape_string($value)."'";
	}
	
	public function query($query) {
		$res = $this->conn->query($query);
		if ($res === true) return true;
		elseif (!$res) throw new JNMDBException($this->conn->error.":\n".$query, $this->conn->errno);
		else return new MySQLiResource($res);
	}
	
	public function getAffectedRows() {
		return $this->conn->affected_rows;
	}
	
	public function getInsertedID() {
		return $this->conn->insert_id;
	}
	
	public function transactionBegin() {
		$this->conn->autocommit(false);
	}
	
	public function commit() {
		$this->conn->commit();
		$this->conn->autocommit(true);
	}
	
	public function rollback() {
		$this->conn->rollback();
		$this->conn->autocommit(true);
	}
	
	public function close() {
		$this->conn->close();
	}
}