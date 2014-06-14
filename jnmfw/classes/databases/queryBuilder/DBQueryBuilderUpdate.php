<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderUpdate extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function set($data);
	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function where($column, $value);
	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereCustom($condition, $data=null);
	public function execute();
}
