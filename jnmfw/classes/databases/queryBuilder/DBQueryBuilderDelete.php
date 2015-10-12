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
	public function whereOr(DBCondition $condition);
	
	/**
	 * @return DBQueryBuilderDelete
	 */
	public function whereAnd(DBCondition $condition);
	
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
	public function whereRaw($condition, $data=null);
	
	public function execute();
}
