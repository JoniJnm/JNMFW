<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderInsert;

class MySQLiQueryBuilderInsert extends MySQLiQueryBuilder implements DBQueryBuilderInsert {
	private $vals = array();
	private $onDuplicate = array();
	
	public function columns($columns) {
		return parent::columns($columns);
	}

	public function values($values) {
		if (is_array($values)) $this->vals = array_merge($this->vals, $values);
		else $this->vals[] = $values;
		return $this;
	}
	
	public function data($data) {
		foreach ($data as $key => $value) {
			$this->cols[] = $key;
			$this->vals[] = $value;
		}
		return $this;
	}
	
	public function onDuplicate($data) {
		foreach ($data as $key => $value) {
			$this->onDuplicate[] = $this->db->quoteName($key).'='.$this->db->quote($value);
		}
		return $this;
	}

	public function onDuplicateRaw($data) {
		foreach ($data as $key => $value) {
			$this->onDuplicate[] = $this->db->quoteName($key).'='.$value;
		}
		return $this;
	}
	
	public function onDuplicateUpdateColums($colums) {
		foreach ($colums as $colum) {
			$name = $this->db->quoteName($colum);
			$this->onDuplicate[] = $name.'=VALUES('.$name.')';
		}
		return $this;
	}
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		$sql = 'INSERT INTO '.$this->db->quoteName($this->table);
		$sql .= ' '.$this->db->quoteNames($this->cols);
		$sql .= ' VALUES '.$this->db->quoteArray($this->vals);
		if ($this->onDuplicate) {
			$sql .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $this->onDuplicate);
		}
		return $sql;
	}
}
