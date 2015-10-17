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
	public function whereOr(DBCondition $condition);
	
	/**
	 * @return DBCondition
	 */
	public function whereAnd(DBCondition $condition);
	
	/**
	 * @return DBCondition
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBCondition
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBCondition
	 */
	public function whereRaw($condition, $data=null);
	
	public function build();
}