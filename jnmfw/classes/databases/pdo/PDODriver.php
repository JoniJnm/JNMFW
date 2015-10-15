<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBDriver;
use JNMFW\classes\databases\DBAdapter;

class PDODriver extends DBDriver {
	static private $instance = null;
	
	/**
	 * @return PDODriver
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			self::$instance = new static();
		}
		return self::$instance;
	}
	
	public function getAdapter($dsn, $user, $pass, $options = array()) {
		$conn = new PDOAdapter($dsn, $user, $pass, $options);
		if ($conn->getError()) {
			\JNMFW\helpers\HLog::error('Error de Conexión PDO '.$conn->getError());
			return null;
		}
		else {
			return $conn;
		}
	}
	
	public function getConnection(DBAdapter $adapter) {
		return new PDOConnection($adapter);
	}
}
