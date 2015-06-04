<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderInsert extends DBQueryBuilder {
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function columns($columns);
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function values($columns);
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function data($data);
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicate($data);
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateCustom($data);
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateUpdateColums($colums);
	public function execute();
}
