<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBQueryBuilderDelete;

class MySQLQueryBuilderDelete extends MySQLQueryBuilder implements DBQueryBuilderDelete {
	public function where($column, $value) {
		return parent::where($column, $value);
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
