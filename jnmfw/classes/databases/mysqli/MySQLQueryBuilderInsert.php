<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderInsert;

class MySQLQueryBuilderInsert extends MySQLQueryBuilder implements DBQueryBuilderInsert {
	private $vals = array();
	
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
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		return 'INSERT INTO '.$this->db->quoteName($this->table).''
				. ' '.$this->db->quoteNames($this->cols)
				. ' VALUES '.$this->db->quoteArray($this->vals);
	}
}
