<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderSelect extends DBQueryBuilder
{
	/**
	 * @return $this
	 */
	public function addOption($option);

	/**
	 * @return $this
	 */
	public function columns($columns);

	/**
	 * @return $this
	 */
	public function columnsRaw($columns);

	/**
	 * @return $this
	 */
	public function clearColumns();

	/**
	 * @return $this
	 */
	public function innerJoin($table, $alias, $col1, $col2);

	/**
	 * @return $this
	 */
	public function leftJoin($table, $alias, $col1, $col2);

	/**
	 * @return $this
	 */
	public function rightJoin($table, $alias, $col1, $col2);

	/**
	 * @return $this
	 */
	public function customJoin($type, $table, $alias, DBCondition $condition);

	/**
	 * @return $this
	 */
	public function customJoinSelect($type, DBQueryBuilderSelect $select, $alias, DBCondition $condition);

	/**
	 * @return $this
	 */
	public function setGlueAnd();

	/**
	 * @return $this
	 */
	public function setGlueOr();

	/**
	 * @return $this
	 */
	public function where($column, $value, $op = '=');

	/**
	 * @return $this
	 */
	public function whereCondition(DBCondition $condition);

	/**
	 * @return $this
	 */
	public function whereNull($column);

	/**
	 * @return $this
	 */
	public function whereNotNull($column);

	/**
	 * @return $this
	 */
	public function whereLike($column, $value);

	/**
	 * @return $this
	 */
	public function whereNotLike($column, $value);

	/**
	 * @return $this
	 */
	public function whereIn($column, $values);

	/**
	 * @return $this
	 */
	public function whereNotIn($column, $values);

	/**
	 * @return $this
	 */
	public function whereRaw($condition, $data = null);

	/**
	 * @return $this
	 */
	public function order($column, $direction = null);

	/**
	 * @return $this
	 */
	public function group($columns);

	/**
	 * @return $this
	 */
	public function limit($limit, $offset = 0);

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
