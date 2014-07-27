<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderUpdate;

class MySQLQueryBuilderUpdate extends MySQLQueryBuilder implements DBQueryBuilderUpdate {
	private $data = array();
	
	public function set($data) {
		$this->data += $data;
		return $this;
	}
	
	public function where($column, $value) {
		return parent::where($column, $value);
	}
	
	public function whereLike($column, $value) {
		return parent::whereLike($column, $value);
	}
	
	public function whereCustom($condition, $data=null) {
		return parent::whereCustom($condition, $data);
	}
	
	private function buildSet() {
		$arr = array();
		foreach ($this->data as $key => $value) {
			$arr[] = $this->db->quoteName($key).'='.$this->db->quote($value);
		}
		return ' SET '.\implode(',', $arr);
	}
	
	public function execute() {
		return parent::execute();
	}

	public function build() {
		$sql = 'UPDATE '.$this->db->quoteName($this->table);
		$sql .= $this->buildSet();
		$sql .= $this->buildWhere();
		return $sql;
	}
}
