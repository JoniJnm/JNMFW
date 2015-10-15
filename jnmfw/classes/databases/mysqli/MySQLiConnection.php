<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBConnection;

class MySQLiConnection extends DBConnection {	
	public function getQueryBuilderInsert($table) {
		return new MySQLiQueryBuilderInsert($this, $table);
	}

	public function getQueryBuilderSelect($table, $alias=null) {
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
		$condition->setGlueOr();
		return $condition;
	}

	public function getAsyncPoll($queries) {
		return new MySQLiPoll($this, $queries);
	}

	//needed for async poll
	
	/**
	 * @return \mysqli
	 */
	public function createNewNativeConnection() {
		return $this->conn->createNewNativeConnection();
	}
	
	public function _parseObject($res) {
		return parent::parseObject($res);
	}
	
	public function _parseObjectList($res, $keycol) {
		return parent::parseObjectList($res, $keycol);
	}
	
	public function _parseValue($res, $col) {
		return parent::parseValue($res, $col);
	}
	
	public function _parseValueArray($res, $col) {
		return parent::parseValueArray($res, $col);
	}
}