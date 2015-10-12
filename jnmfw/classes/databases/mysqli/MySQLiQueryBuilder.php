<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilder;
use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\queryBuilder\DBCondition;

abstract class MySQLiQueryBuilder implements DBQueryBuilder {
	/**
	 * @var DBConnection
	 */
	protected $db;
	protected $table;
	protected $cols = array();
	protected $joins = array();
	
	/**
	 * @var DBCondition 
	 */
	protected $condition = null;
	
	/**
	 * @param DBConnection $db
	 */
	public function __construct(DBConnection $db, $table) {
		$this->db = $db;
		$this->table = $table;
		$this->condition = $db->createConditionAnds();
	}
	
	protected function columns($columns) {
		if (is_array($columns)) $this->cols = array_merge($this->cols, $columns);
		else $this->cols[] = $columns;
		return $this;
	}
	
	protected function clearColums() {
		$this->cols = array();
		return $this;
	}
	
	public function innerJoin($table, $alias, $col1, $col2) {
		$this->joins[] = 'INNER JOIN '.$this->db->quoteName($table).' AS '.$alias
				. ' ON '.$this->db->quoteName($col1).' = '.$this->db->quoteName($col2);
		return $this;
	}
	
	public function customJoin($type, $table, $alias, $conditions) {
		$this->joins[] = $type.' JOIN '.$this->db->quoteName($table).' AS '.$alias
				. ' ON '.$conditions;
		return $this;
	}
	
	public function setGlueAnd() {
		$this->condition->setGlueAnd();
		return $this;
	}
	
	public function setGlueOr() {
		$this->condition->setGlueOr();
		return $this;
	}
	
	public function where($column, $value, $op = '=') {
		$this->condition->where($column, $value, $op);
		return $this;
	}
	
	public function whereOr(DBCondition $condition) {
		$this->condition->whereOr($condition);
		return $this;
	}

	public function whereAnd(DBCondition $condition) {
		$this->condition->whereAnd($condition);
		return $this;
	}

	public function whereLike($column, $value) {
		$this->condition->whereLike($column, $value);
		return $this;
	}

	public function whereIn($column, $values) {
		$this->condition->whereIn($column, $values);
		return $this;
	}

	public function whereRaw($condition, $data=null) {
		$this->condition->whereRaw($condition, $data);
		return $this;
	}
	
	protected function buildWhere() {
		$str = $this->condition->build();
		if ($str) return ' WHERE '.$str;
		return '';
	}
	
	protected function execute() {
		return $this->db->execute($this->build());
	}
}
