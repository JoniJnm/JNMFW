<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderDelete extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function where($column, $value);
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereCustom($condition, $data=null);
	public function execute();
}
