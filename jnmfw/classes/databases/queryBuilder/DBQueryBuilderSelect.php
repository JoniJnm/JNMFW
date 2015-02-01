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
	 * @param string $table
	 * @param string $alias
	 * @param array $assoc key (column) => value. The value must be quoted manually
	 * @return DBQueryBuilderSelect
	 */
	public function leftJoinMulti($table, $alias, $assoc);
	
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
