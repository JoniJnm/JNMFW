<?php

namespace JNMFW\classes\databases\mysqli;

class DatabaseMySQLi extends \JNMFW\classes\databases\Database {	
	public static function connect($host, $user, $pass, $dbname='') {
		$conn = new MySQLiAdapter($host, $user, $pass, $dbname);
		if ($conn->getError()) {
			\JNMFW\helpers\HLog::logError('Error de Conexión MySQLi '.$conn->getError());
			return null;
		}
		else {
			return $conn;
		}
	}
	
	public function getQueryBuilderInsert($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLQueryBuilderInsert($this, $table);
	}

	public function getQueryBuilderSelect($table, $alias=null) {
		return new \JNMFW\classes\databases\mysqli\MySQLQueryBuilderSelect($this, $table, $alias);
	}

	public function getQueryBuilderUpdate($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLQueryBuilderUpdate($this, $table);
	}
	
	public function getQueryBuilderDelete($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLQueryBuilderDelete($this, $table);
	}
}