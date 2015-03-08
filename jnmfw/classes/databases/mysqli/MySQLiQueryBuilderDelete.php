<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderDelete;

class MySQLiQueryBuilderDelete extends MySQLiQueryBuilder implements DBQueryBuilderDelete {
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
		return 'DELETE FROM '.$this->db->quoteName($this->table) . $this->buildWhere();
	}
}
