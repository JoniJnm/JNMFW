<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderSelect;

class MySQLQueryBuilderSelect extends MySQLQueryBuilder implements DBQueryBuilderSelect {
	private $joins = array();
	private $groups = array();
	private $orders = array();
	private $limit = '';
	private $tableAlias;
	
	public function __construct($db, $table, $alias=null) {
		parent::__construct($db, $table);
		$this->tableAlias = $alias;
	}
	
	public function columns($columns) {
		return parent::columns($columns);
	}
	
	public function leftJoin($table, $alias, $col1, $col2) {
		$this->joins[] = 'LEFT JOIN '.$this->db->quoteName($table).' AS '.$alias
				. ' ON '.$this->db->quoteName($col1).' = '.$this->db->quoteName($col2);
		return $this;
	}
	
	public function leftJoinMulti($table, $alias, $assoc) {
		if (!$assoc) return $this;
		$where = array();
		foreach ($assoc as $key => $value) {
			//$value should quoted manually (column or value?)
			$where[] = $this->db->quoteName($key).' = '.$value;
		}
		$this->joins[] = 'LEFT JOIN '.$this->db->quoteName($table).' AS '.$alias
				. ' ON '.implode(' AND ', $where);
		return $this;
	}
	
	public function where($column, $value) {
		return parent::where($column, $value);
	}
	
	public function whereLike($column, $value) {
		return parent::whereLike($column, $value);
	}
	
	public function whereIn($column, $values) {
		return parent::whereIn($column, $values);
	}
	
	public function whereCustom($condition, $data=null) {
		return parent::whereCustom($condition, $data);
	}
	
	public function order($column, $direction=null) {
		$order = $this->db->quoteName($column);
		if ($direction) $order .= ' '.$direction;
		$this->orders[] = $order;
		return $this;
	}
	
	public function group($columns) {
		if (is_array($columns)) $this->groups = array_merge($this->groups, $columns);
		else $this->groups[] = $columns;
		return $this;
	}
	
	public function limit($limit, $offset=0) {
		$this->limit = ' LIMIT '.intval($offset).','.intval($limit);
		return $this;
	}

	public function build() {
		$sql = 'SELECT ';
		if ($this->cols) $sql .= $this->db->quoteNames($this->cols, false);
		else $sql .= '*';
		$sql .= ' FROM '.$this->db->quoteName($this->table);
		if ($this->tableAlias) $sql .= ' AS '.$this->tableAlias;
		if ($this->joins) $sql .= ' '.implode(' ', $this->joins);
		$sql .= $this->buildWhere();
		if ($this->groups) $sql .= ' GROUP BY '.$this->db->quoteNames($this->groups, false);
		if ($this->orders) $sql .= ' ORDER BY '.implode(',', $this->orders);
		$sql .= $this->limit;
		return $sql;
	}

	public function loadObject() {
		if (!$this->limit) $this->limit(1);
		return $this->db->loadObject($this->build());
	}

	public function loadObjectList($keycol = null) {
		return $this->db->loadObjectList($this->build(), $keycol);
	}

	public function loadResult() {
		if (!$this->limit) $this->limit(1);
		return $this->db->loadResult($this->build());
	}

	public function loadResultArray() {
		return $this->db->loadResultArray($this->build());
	}
}
