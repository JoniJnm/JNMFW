<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBConnection;
use JNMFW\exceptions\JNMDBException;

class PDOConnection extends DBConnection {
	public function getQueryBuilderInsert($table) {
		throw new JNMDBException("PDO cannot use query builder");
	}

	public function getQueryBuilderSelect($table, $alias=null) {
		throw new JNMDBException("PDO cannot use query builder");
	}

	public function getQueryBuilderUpdate($table) {
		throw new JNMDBException("PDO cannot use query builder");
	}
	
	public function getQueryBuilderDelete($table) {
		throw new JNMDBException("PDO cannot use query builder");
	}
	
	public function createConditionAnds() {
		throw new JNMDBException("PDO cannot use query builder");
	}
	
	public function createConditionOrs() {
		throw new JNMDBException("PDO cannot use query builder");
	}

	public function getAsyncPoll($queries) {
		throw new JNMDBException("PDO cannot use async query");
	}
}