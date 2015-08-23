<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilder;
use JNMFW\classes\databases\DatabaseConnection;

abstract class MySQLiQueryBuilder implements DBQueryBuilder {
	/**
	 * @var DatabaseConnection
	 */
	protected $db;
	protected $table;
	protected $cols = array();
	protected $joins = array();
	private $wheres = array();
	
	public function __construct($db, $table) {
		$this->db = $db;
		$this->table = $table;
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
	
	protected function where($column, $value) {
		$this->wheres[] = $this->db->quoteName($column.' = '.$this->db->quote($value));
		return $this;
	}
	
	protected function whereLike($column, $value) {
		$this->wheres[] = $this->db->quoteName($column.' LIKE '.$this->db->quote($value));
		return $this;
	}
		
	public function whereIn($column, $values) {
		$this->wheres[] = $this->db->quoteName($column.' IN '.$this->db->quoteArray($values));
		return $this;
	}
	
	protected function whereCustom($condition, $data=null) {
		if (stripos($condition, 'OR') !== false) {
			$condition = '('.$condition.')';
		}
		if ($data) {
			$from = array();
			$to = array();
			foreach ($data as $key => $value) {
				$from[] = '{'.$key.'}';
				$to[] = $this->db->quote($value);
			}
			$this->wheres[] = str_replace($from, $to, $condition);
		}
		else {
			$this->wheres[] = $condition;
		}
		return $this;
	}
	
	protected function buildWhere() {
		if ($this->wheres) return ' WHERE '.implode(' AND ', $this->wheres);
		else return '';
	}
	
	protected function execute() {
		return $this->db->execute($this->build());
	}
}
