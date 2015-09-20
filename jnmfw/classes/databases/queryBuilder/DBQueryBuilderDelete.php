<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderDelete extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function innerJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function customJoin($type, $table, $alias, $conditions);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function where($column, $value);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereCustom($condition, $data=null);
	
	public function execute();
}
