<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderInsert extends DBQueryBuilder {
	public function columns($columns);
	
	/**
	 * Add one row to insert
	 * @param array|object $row the keys are the columns
	 * @return DBQueryBuilderInsert
	 */
	public function data($row);
	
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicate($data);
	
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateRaw($data);
	
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateUpdateColumns($columns);
	
	public function execute();
}
