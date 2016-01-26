<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBCondition {
	/**
	 * @return DBCondition
	 */
	public function setGlueAnd();
	
	/**
	 * @return DBCondition
	 */
	public function setGlueOr();
	
	/**
	 * @return DBCondition
	 */
	public function where($column, $value, $op = '=');
	
	/**
	 * @return DBCondition
	 */
	public function whereCondition(DBCondition $condition);
	
	/**
	 * @return DBCondition
	 */
	public function whereNull($column);
	
	/**
	 * @return DBCondition
	 */
	public function whereNotNull($column);
	
	/**
	 * @return DBCondition
	 */
	public function whereColumns($col1, $col2);
	
	/**
	 * @return DBCondition
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBCondition
	 */
	public function whereNotLike($column, $value);
	
	/**
	 * @return DBCondition
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBCondition
	 */
	public function whereNotIn($column, $values);
	
	/**
	 * @return DBCondition
	 */
	public function whereRaw($condition, $data=null);
	
	public function isEmpty();
	
	public function build();
}
