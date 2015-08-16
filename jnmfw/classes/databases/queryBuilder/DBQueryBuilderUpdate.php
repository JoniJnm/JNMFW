<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderUpdate extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function set($data, $autoQuote = true);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoin($table, $alias, $col1, $col2);
	
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoinMulti($table, $alias, $assoc, $autoQuote = true);
	
	/**
	 * @return DBQueryBuilderUpdate
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
	 * @return DBQueryBuilderUpdate
	 */
	public function whereCustom($condition, $data=null);
	
	public function execute();
}
