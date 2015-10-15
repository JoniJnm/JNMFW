<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBDriver;
use JNMFW\classes\databases\DBAdapter;

class MySQLiDriver extends DBDriver {
	static private $instance = null;
	
	/**
	 * @return MySQLiDriver
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			self::$instance = new static();
		}
		return self::$instance;
	}
	
	public function getAdapter($host, $user, $pass, $dbname='') {
		$conn = new MySQLiAdapter($host, $user, $pass, $dbname);
		if ($conn->getError()) {
			\JNMFW\helpers\HLog::error('Error de Conexión MySQLi '.$conn->getError());
			return null;
		}
		else {
			return $conn;
		}
	}
	
	public function getConnection(DBAdapter $adapter) {
		return new MySQLiConnection($adapter);
	}
}
