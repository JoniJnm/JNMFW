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
	public function execute();
}
