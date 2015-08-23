<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderDelete extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function innerJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function customJoin($type, $table, $alias, $conditions);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function where($column, $value);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereCustom($condition, $data=null);
	
	public function execute();
}
