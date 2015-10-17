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
	
	//Connection data, needed to clone connection for async queries
	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $port;
	
	
	/**
	 * Crea una instancia de esta clase para ser usada como objeto MySQLi
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 * @param int port
	 */
	public function __construct($host, $user, $pass, $dbname='', $port=3306) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->dbname = $dbname;
		$this->port = $port;
		
		$this->conn = $this->createNewNativeConnection();
	}
	
	public function createNewNativeConnection() {
		$conn = new \mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);
		if ($conn->connect_error) {
			$this->error = $conn->connect_error.' ('.$conn->connect_errno.')';
		}
		else {
			$conn->set_charset("utf8");
		}
		return $conn;
	}
	
	public function quote($value) {
		if (is_null($value)) return 'NULL';
		elseif ($value === true) $value = 1;
		elseif ($value === false) $value = 0;
		return "'".$this->conn->real_escape_string($value)."'";
	}
	
	public function query($query) {
		$res = $this->conn->query($query);
		$this->error = $this->conn->error;
		if ($res === true) return true;
		elseif (!$res) return false;
		else return new MySQLiResource($res);
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
}