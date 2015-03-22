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

	public function onDuplicateCustom($data) {
		foreach ($data as $key => $value) {
			$this->onDuplicate[] = $this->db->quoteName($key).'='.$value;
		}
		return $this;
	}
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		$query = 'INSERT INTO '.$this->db->quoteName($this->table).''
				. ' '.$this->db->quoteNames($this->cols)
				. ' VALUES '.$this->db->quoteArray($this->vals);
		if ($this->onDuplicate) {
			$query .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $this->onDuplicate);
		}
		return $query;
	}
}