<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderUpdate extends DBQueryBuilder
{
	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function set($data, $autoQuote = true);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function innerJoin($table, $alias, $col1, $col2);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function customJoin($type, $table, $alias, DBCondition $condition);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function setGlueAnd();

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function setGlueOr();

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function where($column, $value, $op = '=');

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereCondition(DBCondition $condition);

	/**
	 * @return DBQueryBuilderSelect
	 */
	public function whereNull($column);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereNotNull($column);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereLike($column, $value);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereNotLike($column, $value);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereIn($column, $values);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereNotIn($column, $values);

	/**
	 * @return DBQueryBuilderUpdate
	 */
	public function whereRaw($condition, $data = null);

	public function execute();
}
