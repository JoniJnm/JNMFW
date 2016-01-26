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
	public function customJoin($type, $table, $alias, DBCondition $condition);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function setGlueAnd();
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function setGlueOr();
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function where($column, $value, $op = '=');
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereCondition(DBCondition $condition);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereNull($column);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereNotNull($column);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereNotLike($column, $value);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereNotIn($column, $values);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereRaw($condition, $data=null);
	
	public function execute();
}
