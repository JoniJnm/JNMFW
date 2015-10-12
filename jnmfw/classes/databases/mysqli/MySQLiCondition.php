<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\queryBuilder\DBCondition;

class MySQLiCondition implements DBCondition {
	/**
	 * @var DBCondition[] 
	 */
	private $conditionsAnd = array();
	
	/**
	 * @var DBCondition[] 
	 */
	private $conditionsOr = array();
	
	/**
	 * @var DBConnection
	 */
	private $db;
	
	private $wheres = array();
	private $glue = 'AND';
	
	public function __construct(DBConnection $db) {
		$this->db = $db;
	}
	
	public function setGlueAnd() {
		$this->glue = 'AND';
	}
	
	public function setGlueOr() {
		$this->glue = 'OR';
	}
	
	public function where($column, $value, $op = '=') {
		$this->wheres[] = $this->db->quoteName($column).' '.$op.' '.$this->db->quote($value);
		return $this;
	}
	
	public function whereOr(DBCondition $condition) {
		$this->conditionsOr[] = $condition;
		return $this;
	}
	
	public function whereAnd(DBCondition $condition) {
		$this->conditionsAnd[] = $condition;
		return $this;
	}
	
	public function whereLike($column, $value) {
		$this->wheres[] = $this->db->quoteName($column).' LIKE '.$this->db->quote($value);
		return $this;
	}
		
	public function whereIn($column, $values) {
		$this->wheres[] = $this->db->quoteName($column).' IN '.$this->db->quoteArray($values);
		return $this;
	}
	
	public function whereRaw($condition, $data=null) {
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
	
	private function isGlueAnd() {
		return $this->glue == 'AND';
	}
	
	private function isGlueOr() {
		return $this->glue == 'OR';
	}
	
	public function build() {
		if (!$this->wheres && !$this->conditionsAnd && $this->conditionsOr) return '';
		$ands = array();
		$ors = array();
		foreach ($this->conditionsAnd as $condition) {
			$str = $condition->build();
			if (!$str) continue;
			$ands[] = '('.$str.')';
		}
		foreach ($this->conditionsOr as $condition) {
			$str = $condition->build();
			if (!$str) continue;
			$ors[] = '('.$str.')';
		}
		if ($this->wheres) {
			if ($this->isGlueAnd()) {
				$str = implode(' AND ', $this->wheres);
				if ($ors) $str = '('.$str.')';
				array_unshift($ands, $str);
			}
			elseif ($this->isGlueOr()) {
				$str = implode(' OR ', $this->wheres);
				if ($ands) $str = '('.$str.')';
				array_unshift($ors, $str);
			}
			else {
				throw new \RuntimeException("Invalid glue: ".$this->glue);
			}
		}
		$and = implode(' AND ', $ands);
		$or = implode(' OR ', $ors);
		if ($and && $or) return $and .' OR '.$or;
		else return $and.$or;
	}
}
