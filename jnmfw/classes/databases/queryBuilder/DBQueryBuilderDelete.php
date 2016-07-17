<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderDelete extends DBQueryBuilder
{
	/**
	 * @return $this
	 */
	public function innerJoin($table, $alias, $col1, $col2);

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

	public function execute();
}
