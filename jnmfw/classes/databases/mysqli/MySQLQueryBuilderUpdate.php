<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderUpdate;

class MySQLQueryBuilderUpdate extends MySQLQueryBuilder implements DBQueryBuilderUpdate {
	private $set = array();
	
	public function set($data, $autoQuote = true) {
		foreach ($data as $key => $value) {
			if ($autoQuote) $value = $this->db->quote($value);
			$this->set[] = $this->db->quoteName($key).'='.$value;
		}
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
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		$sql = 'UPDATE '.$this->db->quoteName($this->table);
		$sql .= ' SET '.implode(', ', $this->set);
		$sql .= $this->buildWhere();
		return $sql;
	}
}
