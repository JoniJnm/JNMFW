<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBCondition;
use JNMFW\classes\databases\queryBuilder\DBQueryBuilderSelect;

class MySQLiQueryBuilderSelect extends MySQLiQueryBuilder implements DBQueryBuilderSelect
{
	private $groups = array();
	private $orders = array();
	private $limit = '';
	private $tableAlias;

	public function __construct($db, $table, $alias = null) {
		parent::__construct($db, $table);
		$this->tableAlias = $alias;
	}

	public function addOption($option) {
		return parent::addOption($option);
	}

	public function columns($columns) {
		return parent::columns($columns);
	}

	public function columnsRaw($columns) {
		return parent::columnsRaw($columns);
	}

	public function clearColumns() {
		return parent::clearColumns();
	}

	public function innerJoin($table, $alias, $col1, $col2) {
		return parent::innerJoin($table, $alias, $col1, $col2);
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

	public function order($column, $direction = null) {
		$order = $this->db->quoteName($column);
		if ($direction) {
			$order .= ' ' . $direction;
		}
		$this->orders[] = $order;
		return $this;
	}

	public function group($columns) {
		if (is_array($columns)) {
			$this->groups = array_merge($this->groups, $columns);
		}
		else {
			$this->groups[] = $columns;
		}
		return $this;
	}

	public function limit($limit, $offset = 0) {
		$this->limit = ' LIMIT ' . intval($offset) . ',' . intval($limit);
		return $this;
	}

	public function build() {
		$sql = 'SELECT ';
		$sql .= parent::buildOptions();
		$cols = array_map(function($col) {
			return $this->db->quoteName($col);
		}, $this->cols);
		$cols = array_merge($cols, $this->colsRaw);
		if ($cols) {
			$sql .= implode(',', $cols);
		}
		else {
			$sql .= '*';
		}
		$sql .= ' FROM ' . $this->db->quoteName($this->table);
		if ($this->tableAlias) {
			$sql .= ' AS ' . $this->tableAlias;
		}
		if ($this->joins) {
			$sql .= ' ' . implode(' ', $this->joins);
		}
		$sql .= $this->buildWhere();
		if ($this->groups) {
			$sql .= ' GROUP BY ' . $this->db->quoteNames($this->groups, false);
		}
		if ($this->orders) {
			$sql .= ' ORDER BY ' . implode(',', $this->orders);
		}
		$sql .= $this->limit;
		return $sql;
	}

	private function setLimit1IfNeeded() {
		if ($this->limit) {
			return;
		}
		$this->limit(1);
	}

	public function loadObject($class_name = "stdClass") {
		$this->setLimit1IfNeeded();
		return $this->db->loadObject($this->build(), $class_name);
	}

	public function loadObjectList($class_name = "stdClass", $keycol = null) {
		return $this->db->loadObjectList($this->build(), $class_name, $keycol);
	}

	public function loadValue($column_number = 0) {
		$this->setLimit1IfNeeded();
		return $this->db->loadValue($this->build(), $column_number);
	}

	public function loadValueArray($column_number = 0) {
		return $this->db->loadValueArray($this->build(), $column_number);
	}

	public function loadResource() {
		return $this->db->loadResource($this->build());
	}
}
