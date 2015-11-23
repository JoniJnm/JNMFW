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
	
	protected function clearColumns() {
		$this->cols = array();
		return $this;
	}
	
	protected function join($type, $table, $alias, $col1, $col2) {
		$condition = new MySQLiCondition();
		$condition->whereColumns($col1, $col2);
		return $this->customJoin($type, $table, $alias, $condition);
	}
	
	protected function innerJoin($table, $alias, $col1, $col2) {
		return $this->join('INNER', $table, $alias, $col1, $col2);
	}
	
	protected function leftJoin($table, $alias, $col1, $col2) {
		return $this->join('LEFT', $table, $alias, $col1, $col2);
	}
	
	protected function rightJoin($table, $alias, $col1, $col2) {
		return $this->join('RIGHT', $table, $alias, $col1, $col2);
	}
	
	protected function customJoin($type, $table, $alias, DBCondition $condition) {
		$this->joins[] = $type.' JOIN '.$this->db->quoteName($table).' AS '.$alias
				. ' ON '.$condition->build();
		return $this;
	}
	
	protected function setGlueAnd() {
		$this->condition->setGlueAnd();
		return $this;
	}
	
	protected function setGlueOr() {
		$this->condition->setGlueOr();
		return $this;
	}
	
	protected function where($column, $value, $op = '=') {
		$this->condition->where($column, $value, $op);
		return $this;
	}
	
	protected function whereOr(DBCondition $condition) {
		$this->condition->whereOr($condition);
		return $this;
	}

	protected function whereAnd(DBCondition $condition) {
		$this->condition->whereAnd($condition);
		return $this;
	}
	
	protected function whereNull($column) {
		$this->condition->whereNull($column);
		return $this;
	}
	
	protected function whereNotNull($column) {
		$this->condition->whereNotNull($column);
		return $this;
	}

	protected function whereLike($column, $value) {
		$this->condition->whereLike($column, $value);
		return $this;
	}

	protected function whereIn($column, $values) {
		$this->condition->whereIn($column, $values);
		return $this;
	}

	protected function whereRaw($condition, $data=null) {
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
