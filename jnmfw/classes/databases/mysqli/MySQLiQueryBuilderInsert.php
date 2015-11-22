<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderInsert;

class MySQLiQueryBuilderInsert extends MySQLiQueryBuilder implements DBQueryBuilderInsert {
	private $rows = array();
	private $onDuplicate = array();
	
	public function colums($columns) {
		return parent::columns($columns);
	}
	
	public function data($row) {
		$isArray = is_array($row);
		if (!$this->cols) {
			$this->cols = $isArray ? array_keys($row) : array_keys(get_object_vars($row));
		}
		$arr = array();
		foreach ($this->cols as $col) {
			$arr[] = $isArray ? $row[$col] : $row->$col;
		}
		$this->rows[] = $arr;
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
		$values = array();
		foreach ($this->rows as $row) {
			$values[] = $this->db->quoteArray($row);
		}
		$sql .= ' VALUES '.implode(', ', $values);
		if ($this->onDuplicate) {
			$sql .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $this->onDuplicate);
		}
		return $sql;
	}
}
