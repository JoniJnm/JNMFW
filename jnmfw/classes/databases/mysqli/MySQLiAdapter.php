<?php

namespace JNMFW\classes\databases\mysqli;

/**
 * Clase para adaptar el uso de MySQL como si fuera una instancia de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli.php	
 */

class MySQLiAdapter implements \JNMFW\classes\databases\DBAdapter {
	/**
	 * Conexión MySQLi
	 * @var mysqli
	 */
	private $conn;
	
	/**
	 * Error producido
	 * @var string
	 */
	private $error = null;
	
	/**
	 * Crea una instancia de esta clase para ser usada como objeto MySQLi
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 */
	public function __construct($host, $user, $pass, $dbname='') {
		if ($host) {
			$this->conn = new \mysqli($host, $user, $pass, $dbname);
			if ($this->conn->connect_error) {
				$this->error = $this->conn->connect_error.' ('.$this->conn->connect_errno.')';
			}
			else {
				$this->conn->set_charset("utf8");
			}
		}
	}
	
	public function escape($str) {
		return $this->conn->real_escape_string($str);
	}
	
	public function query($query) {
		$res = $this->conn->query($query);
		$this->error = $this->conn->error;
		if ($res === true) return true;
		elseif (!$res) return false;
		else return new MySQLiResourceAdapter($res);
	}
	
	public function getAffectedRows() {
		return $this->conn->affected_rows;
	}
	
	public function getInsertedID() {
		return $this->conn->insert_id;
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function transaccionBegin() {
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
	
	public function set_charset($charset) {
		$this->conn->set_charset($charset);
	}
}