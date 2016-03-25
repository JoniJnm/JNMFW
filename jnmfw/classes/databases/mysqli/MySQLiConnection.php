<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBConnection;

class MySQLiConnection extends DBConnection
{
	public function getQueryBuilderInsert($table) {
		return new MySQLiQueryBuilderInsert($this, $table);
	}

	public function getQueryBuilderSelect($table, $alias = null) {
		return new MySQLiQueryBuilderSelect($this, $table, $alias);
	}

	public function getQueryBuilderUpdate($table) {
		return new MySQLiQueryBuilderUpdate($this, $table);
	}

	public function getQueryBuilderDelete($table) {
		return new MySQLiQueryBuilderDelete($this, $table);
	}

	public function createConditionAnds() {
		return new MySQLiCondition($this);
	}

	public function createConditionOrs() {
		$condition = new MySQLiCondition($this);
		return $condition->setGlueOr();
	}

	public function getAsyncPoll($queries) {
		return new MySQLiPoll($this, $queries);
	}
}