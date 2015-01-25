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
	public function leftJoin($table, $alias, $col1, $col2);
	/**
	 * @return DBQueryBuilderSelect
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
	 * @return DBQueryBuilderSelect
	 */
	public function whereCustom($condition, $data=null);
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
	public function limit($offset, $limit);
	
	public function loadObject();
	
	/**
	 * @param string $keycol Columna para crear los índices del array
	 */
	public function loadObjectList($keycol = null);
	public function loadResult();
	public function loadResultArray();
}
