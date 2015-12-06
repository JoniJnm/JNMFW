<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBConnection;

class PDOConnection extends DBConnection {
	public function getQueryBuilderInsert($table) {
		throw new \Exception("PDO cannot use query builder");
	}

	public function getQueryBuilderSelect($table, $alias=null) {
		throw new \Exception("PDO cannot use query builder");
	}

	public function getQueryBuilderUpdate($table) {
		throw new \Exception("PDO cannot use query builder");
	}
	
	public function getQueryBuilderDelete($table) {
		throw new \Exception("PDO cannot use query builder");
	}
	
	public function createConditionAnds() {
		throw new \Exception("PDO cannot use query builder");
	}
	
	public function createConditionOrs() {
		throw new \Exception("PDO cannot use query builder");
	}

	public function getAsyncPoll($queries) {
		throw new \Exception("PDO cannot use async query");
	}
}