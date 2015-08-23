<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderUpdate;

class MySQLiQueryBuilderUpdate extends MySQLiQueryBuilder implements DBQueryBuilderUpdate {
	private $set = array();
	
	private $tableAlias;
	
	public function __construct($db, $table, $alias=null) {
		parent::__construct($db, $table);
		$this->tableAlias = $alias;
	}
	
	public function set($data, $autoQuote = true) {
		foreach ($data as $key => $value) {
			if ($autoQuote) $value = $this->db->quote($value);
			$this->set[] = $this->db->quoteName($key).'='.$value;
		}
		return $this;
	}
	
	public function innerJoin($table, $alias, $col1, $col2) {
		return parent::innerJoin($table, $alias, $col1, $col2);
	}
	
	public function customJoin($type, $table, $alias, $conditions) {
		return parent::customJoin($type, $table, $alias, $conditions);
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
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		$sql = 'UPDATE '.$this->db->quoteName($this->table);
		if ($this->tableAlias) $sql .= ' AS '.$this->tableAlias;
		if ($this->joins) $sql .= ' '.implode(' ', $this->joins);
		$sql .= ' SET '.implode(', ', $this->set);
		$sql .= $this->buildWhere();
		return $sql;
	}
}
