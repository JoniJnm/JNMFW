<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderDelete;

class MySQLiQueryBuilderDelete extends MySQLiQueryBuilder implements DBQueryBuilderDelete {
	private $tableAlias;
	
	public function __construct($db, $table, $alias=null) {
		parent::__construct($db, $table);
		$this->tableAlias = $alias;
	}
	
	public function leftJoin($table, $alias, $col1, $col2) {
		return parent::leftJoin($table, $alias, $col1, $col2);
	}
	
	public function leftJoinMulti($table, $alias, $assoc, $autoQuote = true) {
		return parent::leftJoinMulti($table, $alias, $assoc, $autoQuote);
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
		$sql = 'DELETE FROM '.$this->db->quoteName($this->table);
		if ($this->tableAlias) $sql .= ' AS '.$this->tableAlias;
		if ($this->joins) $sql .= ' '.implode(' ', $this->joins);
		$sql .= $this->buildWhere();
		return $sql;
	}
}
