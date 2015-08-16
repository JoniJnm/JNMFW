<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderDelete extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoinMulti($table, $alias, $assoc, $autoQuote = true);
	
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
