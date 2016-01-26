<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderSelect extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function columns($columns);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function clearColumns();
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function innerJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function rightJoin($table, $alias, $col1, $col2);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function customJoin($type, $table, $alias, DBCondition $condition);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function setGlueAnd();
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function setGlueOr();
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function where($column, $value, $op = '=');
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereCondition(DBCondition $condition);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereNull($column);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereNotNull($column);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereLike($column, $value);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereNotLike($column, $value);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereIn($column, $values);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereNotIn($column, $values);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereRaw($condition, $data=null);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function order($column, $direction=null);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function group($columns);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function limit($limit, $offset=0);
	
	/**
	 * @return DBQueryBuilderSelect
	 */
	public function addOption($option);
	
	public function loadObject($class_name = "stdClass");
	
	/**
	 * @param string $keycol Columna para crear los índices del array
	 */
	public function loadObjectList($class_name = "stdClass", $keycol = null);
	
	public function loadValue($column_number = 0);
	
	public function loadValueArray($column_number = 0);
	
	/**
	 * Returns DBResource to loop it manually
	 * @return \JNMFW\classes\databases\DBResource
	 */
	public function loadResource();
}
