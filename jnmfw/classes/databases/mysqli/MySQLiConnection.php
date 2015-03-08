<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DatabaseConnection;

class MySQLiConnection extends DatabaseConnection {	
	public function getQueryBuilderInsert($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLiQueryBuilderInsert($this, $table);
	}

	public function getQueryBuilderSelect($table, $alias=null) {
		return new \JNMFW\classes\databases\mysqli\MySQLiQueryBuilderSelect($this, $table, $alias);
	}

	public function getQueryBuilderUpdate($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLiQueryBuilderUpdate($this, $table);
	}
	
	public function getQueryBuilderDelete($table) {
		return new \JNMFW\classes\databases\mysqli\MySQLiQueryBuilderDelete($this, $table);
	}
}