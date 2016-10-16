<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBCondition;
use JNMFW\classes\databases\queryBuilder\DBQueryBuilderDelete;
use JNMFW\classes\databases\queryBuilder\DBQueryBuilderSelect;

class MySQLiQueryBuilderDelete extends MySQLiQueryBuilder implements DBQueryBuilderDelete
{
	private $tableAlias;

	public function __construct($db, $table, $alias = null) {
		parent::__construct($db, $table);
		$this->tableAlias = $alias;
	}

	public function innerJoin($table, $alias, $col1, $col2) {
		return parent::innerJoin($table, $alias, $col1, $col2);
	}

	public function customJoinSelect($type, DBQueryBuilderSelect $select, $alias, DBCondition $condition) {
		return parent::customJoinSelect($type, $select, $alias, $condition);
	}

	public function leftJoin($table, $alias, $col1, $col2) {
		return parent::leftJoin($table, $alias, $col1, $col2);
	}

	public function rightJoin($table, $alias, $col1, $col2) {
		return parent::rightJoin($table, $alias, $col1, $col2);
	}

	public function customJoin($type, $table, $alias, DBCondition $condition) {
		return parent::customJoin($type, $table, $alias, $condition);
	}

	public function setGlueAnd() {
		return parent::setGlueAnd();
	}

	public function setGlueOr() {
		return parent::setGlueOr();
	}

	public function where($column, $value, $op = '=') {
		return parent::where($column, $value, $op);
	}

	public function whereCondition(DBCondition $condition) {
		return parent::whereCondition($condition);
	}

	public function whereNull($column) {
		return parent::whereNull($column);
	}

	public function whereNotNull($column) {
		return parent::whereNotNull($column);
	}

	public function whereLike($column, $value) {
		return parent::whereLike($column, $value);
	}

	public function whereNotLike($column, $value) {
		return parent::whereNotLike($column, $value);
	}

	public function whereIn($column, $values) {
		return parent::whereIn($column, $values);
	}

	public function whereNotIn($column, $values) {
		return parent::whereNotIn($column, $values);
	}

	public function whereRaw($condition, $data = null) {
		return parent::whereRaw($condition, $data);
	}

	public function execute() {
		return parent::execute();
	}

	public function build() {
		$sql = 'DELETE FROM ' . $this->db->quoteName($this->table);
		if ($this->tableAlias) {
			$sql .= ' AS ' . $this->tableAlias;
		}
		if ($this->joins) {
			$sql .= ' ' . implode(' ', $this->joins);
		}
		$sql .= $this->buildWhere();
		return $sql;
	}
}
